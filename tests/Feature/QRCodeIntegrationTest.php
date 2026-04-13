<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pesanan;
use App\Models\Barang;
use App\Models\DetailPesanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QRCodeIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_halaman_sukses_pembayaran_menampilkan_qrcode_base64()
    {
        // 1. Setup Dummy Data
        $barang = Barang::create([
            'nama' => 'Buku Ensiklopedia Test QR',
            'harga' => 200000,
            'timestamp' => now()
        ]);
        $barang = Barang::latest('timestamp')->first();

        $pesanan = Pesanan::create([
            'nama_customer' => 'Guest_QK2001',
            'total' => 200000,
            'status_bayar' => '1', // Lunas
            'transaction_id' => 'ORD-TEST-999',
            'snap_token' => 'dummy'
        ]);

        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'id_barang' => $barang->id_barang,
            'jumlah' => 1,
            'harga' => 200000,
            'subtotal' => 200000,
        ]);

        // 2. Kunjungi Route Customer Sukses
        $response = $this->get('/customer/sukses/' . $pesanan->transaction_id);

        // 3. Verifikasi response HTML OK
        $response->assertStatus(200);

        // 4. Verifikasi teks pada view HTML mengandung ID Transaction
        $response->assertSee('Pembayaran Berhasil');
        $response->assertSee('ORD-TEST-999');
        $response->assertSee('Guest_QK2001');
        
        // 5. Verifikasi tag gambar base64 QR Code ter-render di dalam halaman HTML
        $response->assertSee('<img src="data:image/png;base64,', false);
    }
}