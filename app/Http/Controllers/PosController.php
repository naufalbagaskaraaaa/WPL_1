<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // Halaman versi jQuery
    public function indexJquery()
    {
        return view('pos.jquery');
    }

    // Halaman versi Axios
    public function indexAxios()
    {
        return view('pos.axios');
    }

    // Mencari data barang berdasarkan ID/Kode
    public function cariBarang($id_barang)
    {
        // Cari data barang di tabel barang
        $barang = DB::table('barang')->where('id_barang', $id_barang)->first();
        
        if ($barang) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Barang ditemukan',
                'data' => $barang
            ]);
        }

        // Return error 404 jika barang tidak ditemukan
        return response()->json([
            'status' => 'error',
            'code' => 404,
            'message' => 'Barang dengan kode tersebut tidak ditemukan',
            'data' => null
        ], 404);
    }

    // Menyimpan data Penjualan ke 2 tabel secara bersamaan (Database Transaction)
    public function simpanTransaksi(Request $request)
    {
        $items = $request->input('items', []);
        $total = $request->input('total', 0);

        if (count($items) === 0 || $total <= 0) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Keranjang kosong atau total tidak sah.',
                'data' => null
            ], 400);
        }

        DB::beginTransaction();

        try {
            // 1. Simpan ke tabel penjualan dan ambil ID/Penjualan-nya
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(), 
                'total' => $total
            ], 'id_penjualan');

            // 2. Loop dan simpan ke tabel penjualan_detail
            foreach ($items as $item) {
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $id_penjualan,
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // Validasi semuanya dan eksekusi
            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Transaksi berhasil disimpan!',
                'data' => [
                    'id_penjualan' => $id_penjualan
                ]
            ]);

        } catch (\Exception $e) {
            // Batalkan semua query jika terjadi error (tidak ada data setengah jadi)
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan internal: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}