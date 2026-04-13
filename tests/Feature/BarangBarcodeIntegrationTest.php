<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BarangBarcodeIntegrationTest extends TestCase
{
    // Menggunakan trait ini agar data dummy otomatis dihapus kembali setelah tes selesai
    use DatabaseTransactions;

    /**
     * Memastikan request ke cetak Barcode berhasil menghasilkan Application/PDF
     */
    public function test_fitur_cetak_barcode_merespon_dengan_file_pdf()
    {
        // Login sebagai user dummy agar lolos middleware auth
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Buat 1 dummy data Barang
        $barang = Barang::create([
            'nama' => 'Testing PDF Barcode',
            'harga' => 50000,
            'timestamp' => now()
        ]);

        // Karena ID dibuat otomatis oleh trigger PostgreSQL ("2503..."), 
        // kita ambil data terakhir yang dimasukkan
        $barang = Barang::latest('timestamp')->first(); 

        // 2. Eksekusi endpoint cetak yang kita asumsikan di route 'barang.cetak' menggunakan POST
        $response = $this->post(route('barang.cetak'), [
            'selected_ids' => [$barang->id_barang],
            'koordinat_x' => '1',
            'koordinat_y' => '1'
        ]);

        // 3. Verifikasi response adalah 200 OK dan tipe konten adalah application/pdf
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Memastikan struktur view blade berjalan baik dengan array buatan
     * untuk memvalidasi injeksi gambar base64 generator Barcode.
     */
    public function test_view_cetak_menampilkan_barcode_base64_dengan_benar()
    {
        // 1. Persiapkan array dummy persis dengan output BarangController
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        
        // Aturan: Barcode 1D (max 25 karakter) diproses menggunakan TYPE_CODE_128
        $dummyIdBarang = '25030201'; 
        $barcodeBase64 = base64_encode($generator->getBarcode($dummyIdBarang, $generator::TYPE_CODE_128, 1.5, 30));

        $dataLabel = [
            'id_barang' => $dummyIdBarang,
            'nama' => 'Mock Data Tag Harga',
            'harga' => 75000,
            'barcode' => $barcodeBase64
        ];

        // Format halaman (chunk 5 item per baris sesuai di cetak.blade.php)
        $halaman = [
            [ $dataLabel, null, null, null, null ]
        ];

        // 2. Render view secara langsung
        $view = $this->view('barang.cetak', [
            'halaman' => $halaman,
            'x' => 1,
            'y' => 1
        ]);

        // 3. Validasi elemen-elemen di HTML sebelum render ke PDF DOM
        $view->assertSee('Mock Data Tag Harga'); // Nama barang muncul
        $view->assertSee('75.000'); // Harga telah terformat IDR
        $view->assertSee($dummyIdBarang); // ID Barang muncul (tepat di bawah barcode sesuai aturan)
        
        // Pastikan tag base64 img barcode utuh dan tidak error (null/empty)
        $view->assertSee('<img src="data:image/png;base64,' . $barcodeBase64 . '"', false);
    }
}
