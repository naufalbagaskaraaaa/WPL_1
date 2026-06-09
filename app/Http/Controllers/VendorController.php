<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    // 1. Halaman utama vendor (pilih vendor tanpa login)
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendor.index', compact('vendors'));
    }

    // 2. Halaman master menu vendor (tampil menu & form tambah)
    public function menu($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        $menus = Menu::where('vendor_id', $idvendor)->get();
        
        return view('vendor.menu', compact('vendor', 'menus'));
    }

    // 3. Proses simpan / upload menu baru (via AJAX)
    public function tambahMenu(Request $request, $idvendor)
    {
        $request->validate([
            'nama_menu' => 'required',
            'harga' => 'required|integer',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $pathGambar = null;
        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('menu', 'public');
        }

        $menu = Menu::create([
            'vendor_id' => $idvendor,
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'path_gambar' => $pathGambar,
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil menambahkan menu, segera refresh daftar.',
            'data' => $menu
        ]);
    }

    // 4. Proses update menu (via AJAX POST)
    public function updateMenu(Request $request, $idvendor, $idmenu)
    {
        $request->validate([
            'nama_menu' => 'required',
            'harga' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $menu = Menu::where('vendor_id', $idvendor)->findOrFail($idmenu);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->path_gambar && Storage::disk('public')->exists($menu->path_gambar)) {
                Storage::disk('public')->delete($menu->path_gambar);
            }
            $menu->path_gambar = $request->file('gambar')->store('menu', 'public');
        }

        $menu->nama_menu = $request->nama_menu;
        $menu->harga = $request->harga;
        $menu->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil memperbarui menu',
            'data' => $menu
        ]);
    }

    // 5. Proses hapus menu (via AJAX DELETE)
    public function hapusMenu($idvendor, $idmenu)
    {
        $menu = Menu::where('vendor_id', $idvendor)->findOrFail($idmenu);

        // Hapus gambar dari storage
        if ($menu->path_gambar && Storage::disk('public')->exists($menu->path_gambar)) {
            Storage::disk('public')->delete($menu->path_gambar);
        }

        $menu->delete();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Berhasil menghapus menu'
        ]);
    }

    // 6. Halaman daftar pesanan lunas khusus vendor ini
    public function pesananLunas($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);

        $pesanans = Pesanan::where('status_bayar', '1')
            ->whereHas('detailPesanan.menu', function ($query) use ($idvendor) {
                $query->where('vendor_id', $idvendor);
            })
            ->with(['detailPesanan' => function ($query) use ($idvendor) {
                $query->whereHas('menu', function ($q) use ($idvendor) {
                    $q->where('vendor_id', $idvendor);
                })->with('menu');
            }])
            ->get();

        return view('vendor.pesanan', compact('vendor', 'pesanans'));
    }

    // 7. Halaman scanner barcode pesanan (QR)
    public function scanQr($idvendor)
    {
        $vendor = Vendor::findOrFail($idvendor);
        return view('vendor.scan', compact('vendor'));
    }

    // 8. Proses cek QR (AJAX)
    public function checkQr(Request $request, $idvendor)
    {
        $qrData = $request->qr_data;
        
        // Asumsi dataQR berisi text dengan format:
        // ID Pesanan: ORD-1718000000-1\n...
        
        $order_id = null;
        if (preg_match('/ID Pesanan:\s*([^\s]+)/', $qrData, $matches)) {
            $order_id = $matches[1];
        } else {
            return response()->json(['status' => 'error', 'message' => 'Format QR tidak valid'], 400);
        }

        $pesanan = Pesanan::where('transaction_id', $order_id)
            ->with(['detailPesanan' => function ($query) use ($idvendor) {
                $query->whereHas('menu', function ($q) use ($idvendor) {
                    $q->where('vendor_id', $idvendor);
                })->with('menu');
            }])
            ->first();

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Cek apakah ada pesanan untuk vendor terkait
        if ($pesanan->detailPesanan->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan ini tidak ditujukan untuk toko Anda'], 404);
        }

        $listPesanan = $pesanan->detailPesanan->map(function ($detail) {
            return $detail->menu->nama_menu . ' ('.$detail->jumlah.'x) - Catatan: ' . ($detail->catatan ?: '-');
        });

        $statusBayarText = $pesanan->status_bayar == '1' ? 'LUNAS' : ($pesanan->status_bayar == '0' ? 'BELUM BAYAR' : 'BATAL');

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $pesanan->transaction_id,
                'customer' => $pesanan->nama_customer,
                'status_bayar' => $statusBayarText,
                'items' => $listPesanan
            ]
        ]);
    }
}
