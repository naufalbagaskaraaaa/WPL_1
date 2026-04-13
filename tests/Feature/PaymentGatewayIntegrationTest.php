<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barang;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Midtrans\Snap;
use Midtrans\Config;
use Mockery;

class PaymentGatewayIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock MIDTRANS Config untuk test environment (Bypass call asli ke API Midtrans)
        Config::$serverKey = 'dummy_server_key';
        Config::$isProduction = false;
        
        // Kita intercept instance Snap API agar tidak benar-benar nembak HTTP request ke Midtrans
        // melainkan mengembalikan string token tiruan
        $mock = Mockery::mock('alias:' . Snap::class);
        $mock->shouldReceive('getSnapToken')
             ->andReturn('dummy_snap_token_123');
    }

    public function test_membuat_pesanan_baru_dan_mendapatkan_snap_token()
    {
        // 1. Buat Dummy Barang
        $barang = Barang::create([
            'nama' => 'Buku Belajar Laravel 12',
            'harga' => 150000,
            'timestamp' => now()
        ]);
        $barang = Barang::latest('timestamp')->first(); 

        // 2. Simulasi Data Checkout
        $payload = [
            'total' => 150000,
            'metode_bayar' => '1',
            'detail' => [
                [
                    'idmenu' => $barang->id_barang,
                    'jumlah' => 1,
                    'harga' => 150000,
                    'subtotal' => 150000,
                    'catatan' => 'Bungkus rapi'
                ]
            ]
        ];

        // 3. Post ke endpoint /customer/simpan
        $response = $this->postJson('/customer/simpan', $payload);

        // 4. Verifikasi format respon & token
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pesanan berhasil dibuat',
            'data' => [
                'snap_token' => 'dummy_snap_token_123'
            ]
        ]);

        // 5. Pastikan data terekam di Database pesanan dan detail pesanans
        $this->assertDatabaseHas('pesanan', [
            'total' => 150000,
            'metode_bayar' => '1',
            'status_bayar' => '0',
            'snap_token' => 'dummy_snap_token_123'
        ]);

        $this->assertDatabaseHas('detail_pesanans', [
            'id_barang' => $barang->id_barang,
            'jumlah' => 1,
            'subtotal' => 150000
        ]);
    }

    public function test_webhook_midtrans_bisa_mengubah_status_pesanan_menjadi_lunas()
    {
        // 1. Buat Pesanan awal (status belum bayar)
        $pesanan = Pesanan::create([
            'nama_customer' => 'Guest_0000001',
            'total' => 50000,
            'status_bayar' => '0',
            'transaction_id' => 'ORD-12345-1',
            'snap_token' => 'dummy_token'
        ]);

        // 2. Simulasi Webhook dari Midtrans
        $payloadWebhook = [
            'transaction_status' => 'settlement',
            'order_id' => 'ORD-12345-1',
            'fraud_status' => 'accept'
        ];

        // 3. Post dari Midtrans ke endpoint kita
        $response = $this->postJson('/webhook/midtrans', $payloadWebhook);
        $response->assertStatus(200);

        // 4. Refresh & Validasi DB (status jadi 1 = lunas)
        $pesanan->refresh();
        $this->assertEquals('1', $pesanan->status_bayar);
    }
}
