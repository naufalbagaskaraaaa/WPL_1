<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barang;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeTagHargaTest extends TestCase
{
    /**
     * Test validasi Unit untuk memastikan generator logika Barcode (1D)
     * dapat berjalan dengan data string (di bawah 25 karakter)
     */
    public function test_barcode_generator_menghasilkan_format_png_yang_valid()
    {
        // Pastikan library terinstall: composer require picqer/php-barcode-generator
        if (!class_exists(BarcodeGeneratorPNG::class)) {
            $this->markTestSkipped('Library Picqer/Barcode belum diinstall.');
        }

        $generator = new BarcodeGeneratorPNG();
        
        // Aturan 2: Simulasi ID/SKU Barang menggunakan karakter terbatas (< 25 karakter)
        $kodeKarakter = "BRG-2026-001X"; 
        
        $barcodeRaw = $generator->getBarcode($kodeKarakter, $generator::TYPE_CODE_128);
        
        // Verifikasi output barcode raw (bitstream PNG) berhasil terbentuk
        $this->assertNotEmpty($barcodeRaw);
        
        // Verifikasi bahwa data tersebut bisa dienkode ke base64 dengan baik
        $base64 = base64_encode($barcodeRaw);
        $this->assertIsString($base64);
    }

    /**
     * Test Integration untuk mengecek integrasi data di dalam View HTML/blade
     * yang akan dikonversi ke PDF.
     */
    public function test_view_tag_harga_menampilkan_barcode_base64_dan_informasi_barang()
    {
        if (!view()->exists('cetak.tag-harga')) {
            $this->markTestSkipped('View cetak.tag-harga belum dibuat. Buat terlebih dahulu sesuai langkah sebelumnya.');
        }

        // Gunakan instance model untuk testing tanpa menyentuh database dan memicu constraint table
        $barang = new Barang();
        $barang->id = 999;
        $barang->nama_barang = 'Produk Test Barcode';
        $barang->harga = 15000;

        $generator = new BarcodeGeneratorPNG();
        $barcodeBase64 = base64_encode($generator->getBarcode($barang->id, $generator::TYPE_CODE_128, 2, 50));

        // Render blade view secara langsung untuk mengecek HTML sebelum jadi PDF
        $view = $this->view('cetak.tag-harga', [
            'barang' => $barang,
            'barcodeBase64' => $barcodeBase64
        ]);

        // Verifikasi 1: Memastikan base64 image Barcode tersisip dengan benar
        $view->assertSee('data:image/png;base64,' . $barcodeBase64);

        // Verifikasi 2: Memastikan ID tertera tepat di komponen view untuk identifikasi manual (Aturan 2)
        $view->assertSee((string) $barang->id);
        
        // Verifikasi 3: Memastikan nama barang dan konversi format harga IDR berjalan
        $view->assertSee('Produk Test Barcode');
        $view->assertSee('15.000');
    }
}
