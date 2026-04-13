<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Barang;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class CustomerController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return view('customer.index', compact('vendors'));
    }

    public function cariMenu($idvendor)
    {
        // Secara skenario karena tabel `barang` tidak punya relasi vendor,
        // kita panggil semua barang (atau random), namun logika flow tetep jalan
        $menus = Barang::inRandomOrder()->limit(10)->get();
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Berhasil mengambil data barang',
            'data'    => $menus
        ]);
    }

    public function simpanPesanan(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Generate Nama Guest (Auto-increment)
            $lastPesanan = Pesanan::orderBy('id', 'desc')->first();
            $nextId = $lastPesanan ? $lastPesanan->id + 1 : 1;
            $namaGuest = 'Guest_' . str_pad($nextId, 7, '0', STR_PAD_LEFT);

            // Generate transaction_id (order_id) unik untuk midtrans
            $orderIdStr = 'ORD-' . time() . '-' . $nextId;

            // 2. Simpan Pesanan Induk
            $pesanan = new Pesanan();
            $pesanan->nama_customer = $namaGuest;
            $pesanan->total = $request->total;
            $pesanan->metode_bayar = $request->metode_bayar; // 1 = VA
            $pesanan->status_bayar = '0'; // 0 = belum bayar
            $pesanan->transaction_id = $orderIdStr;
            $pesanan->save();

            // 3. Simpan Detail Pesanan
            foreach ($request->detail as $item) {
                $detail = new DetailPesanan();
                $detail->pesanan_id = $pesanan->id;
                $detail->id_barang = $item['idmenu']; // di frontend diset sebagai 'idmenu' tapi aslinya 'id_barang'
                $detail->jumlah = $item['jumlah'];
                $detail->harga = $item['harga'];
                $detail->subtotal = $item['subtotal'];
                $detail->catatan = $item['catatan'] ?? '';
                $detail->save();
            }

            // 4. Konfigurasi Midtrans & Generate Snap Token
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
            Config::$is3ds = env('MIDTRANS_IS_3DS', true);

            $params = [
                'transaction_details' => [
                    'order_id' => $pesanan->transaction_id,
                    'gross_amount' => $pesanan->total,
                ],
                'customer_details' => [
                    'first_name' => $pesanan->nama_customer,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Simpan snap token ke database
            $pesanan->snap_token = $snapToken;
            $pesanan->save();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Pesanan berhasil dibuat',
                'data'    => [
                    'snap_token' => $snapToken,
                    'order_id' => $pesanan->transaction_id,
                    'nama_guest' => $pesanan->nama_customer
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage(),
                'data'    => null
            ]);
        }
    }

    public function notifikasi(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        // SUPPORT UNTUK TESTING (phpunit) & MIDTRANS ASLI (php://input)
        if (app()->runningUnitTests() || !empty($request->all())) {
            $transaction_status = $request->transaction_status;
            $order_id = $request->order_id;
            $fraud_status = $request->fraud_status;
        } else {
            try {
                $notifikasi = new Notification();
                $transaction_status = $notifikasi->transaction_status;
                $order_id = $notifikasi->order_id;
                $fraud_status = $notifikasi->fraud_status;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 400);
            }
        }

        $pesanan = Pesanan::where('transaction_id', $order_id)->first();
        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
            if ($fraud_status == 'challenge') {
                // Biarkan saja status sementara (challenge)
            } else {
                $pesanan->status_bayar = '1'; // 1 = lunas
            }
        } else if ($transaction_status == 'cancel' || $transaction_status == 'deny' || $transaction_status == 'expire') {
            $pesanan->status_bayar = '2'; // 2 = Batal
        } else if ($transaction_status == 'pending') {
            $pesanan->status_bayar = '0'; // 0 = belum bayar
        }

        $pesanan->save();

        return response()->json(['message' => 'Notification processed successfully']);
    }

    public function pembayaranSukses($order_id)
    {
        $pesanan = Pesanan::where("transaction_id", $order_id)->firstOrFail();

        $dataQr = "ID Pesanan: " . $pesanan->transaction_id . "\n" .
            "Total Pembayaran: Rp" . number_format($pesanan->total, 0, ",", ".") . "\n" .
            "Nama Pelanggan: " . $pesanan->nama_customer;

        $qrCode = new QrCode(
            data: $dataQr,
            encoding: new Encoding("UTF-8"),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());

        return view("customer.sukses", compact("pesanan", "qrBase64"));
    }
}
