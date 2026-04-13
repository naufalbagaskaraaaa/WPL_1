<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PosController;

Route::redirect('/', '/login');

Route::get('/test', function () {
    return view('test');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/kategori', [KategoriController::class, 'create'])
        ->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])
        ->name('kategori.store');

    Route::get('/buku', [BukuController::class, 'create'])
        ->name('buku.create');
    Route::post('/buku', [BukuController::class, 'store'])
        ->name('buku.store');

    Route::get('/generate-pdf', [PdfController::class, 'generatePDFLandscape'])->name('generate.pdf.landscape');
    Route::get('/generate-undangan', [PdfController::class, 'generatePDFPortrait'])->name('generate.pdf.portrait');

    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    Route::get('auth/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('auth/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.process');

    Route::get('barang/cetak', function () {
        return redirect()->route('barang.index');
    });
    Route::post('barang/cetak', [BarangController::class, 'cetak'])->name('barang.cetak');
    Route::resource('barang', BarangController::class)->except(['show']);

    Route::get('/modul_4/tabel', function () {
        return view('modul_4.tabel');
    })->name('modul_4.tabel');

    Route::get('/modul_4/datatables', function () {
        return view('modul_4.datatables');
    })->name('modul_4.datatables');

    Route::get('/modul_4/select', function () {
        return view('modul_4.select');
    })->name('modul_4.select');

    Route::get('/wilayah/provinces', [WilayahController::class, 'getProvinces'])->name('wilayah.provinces');
    Route::get('/wilayah/regencies/{id_provinsi}', [WilayahController::class, 'getRegencies'])->name('wilayah.regencies');
    Route::get('/wilayah/districts/{id_kota}', [WilayahController::class, 'getDistricts'])->name('wilayah.districts');
    Route::get('/wilayah/villages/{id_kecamatan}', [WilayahController::class, 'getVillages'])->name('wilayah.villages');

    // Tugas Wilayah: View
    Route::get('/wilayah/jquery', function () {
        return view('wilayah.jquery');
    })->name('wilayah.jquery');

    Route::get('/wilayah/axios', function () {
        return view('wilayah.axios');
    })->name('wilayah.axios');

    // Tugas POS (Kasir)
    Route::get('/pos/jquery', [PosController::class, 'indexJquery'])->name('pos.jquery');
    Route::get('/pos/axios', [PosController::class, 'indexAxios'])->name('pos.axios');
    Route::get('/api/barang/{id_barang}', [PosController::class, 'cariBarang'])->name('api.barang.get');
    Route::post('/api/penjualan', [PosController::class, 'simpanTransaksi'])->name('api.penjualan.store');
});

use App\Http\Controllers\CustomerController;
Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/menu/{idvendor}', [CustomerController::class, 'cariMenu'])->name('customer.cari-menu');
Route::post('/customer/simpan', [CustomerController::class, 'simpanPesanan'])->name('customer.simpan');
Route::get('/customer/sukses/{order_id}', [CustomerController::class, 'pembayaranSukses'])->name('customer.sukses');
Route::post('/webhook/midtrans', [CustomerController::class, 'notifikasi'])->name('webhook.midtrans');

use App\Http\Controllers\VendorController;
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');
    Route::get('/{idvendor}/menu', [VendorController::class, 'menu'])->name('menu');
    Route::post('/{idvendor}/menu/tambah', [VendorController::class, 'tambahMenu'])->name('menu.tambah');
    Route::post('/{idvendor}/menu/{idmenu}/update', [VendorController::class, 'updateMenu'])->name('menu.update');
    Route::delete('/{idvendor}/menu/{idmenu}/hapus', [VendorController::class, 'hapusMenu'])->name('menu.hapus');
    Route::get('/{idvendor}/pesanan', [VendorController::class, 'pesananLunas'])->name('pesanan');
});

use App\Http\Controllers\PelangganController;
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/', [PelangganController::class, 'index'])->name('index');
    Route::get('/tambah-blob', [PelangganController::class, 'createBlob'])->name('create_blob');
    Route::post('/simpan-blob', [PelangganController::class, 'storeBlob'])->name('store_blob');
    Route::get('/tambah-file', [PelangganController::class, 'createFile'])->name('create_file');
    Route::post('/simpan-file', [PelangganController::class, 'storeFile'])->name('store_file');
});
