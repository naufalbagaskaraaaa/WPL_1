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
}
