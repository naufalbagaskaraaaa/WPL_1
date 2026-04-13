<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Pesanan;

class MidtransWebhookTest extends TestCase
{
    // Cukup gunakan DatabaseTransactions karena test dijalankan ke real pgsql
    // dan akan di rollback saat test selesai tanpa merusak data aslinya.
    use DatabaseTransactions;

    /**
     * Test integrasi Webhook Midtrans untuk memperbarui status pesanan menjadi Lunas (1)
     */
    public function test_webhook_midtrans_berhasil_update_status_bayar_ke_lunas()
    {
        // 1. SIAPKAN DATA DUMMY: Buat 1 pesanan yang "Belum Bayar" (0)
        $dummyOrderId = 'ORD-TEST-' . time();
        
        $pesanan = Pesanan::create([
            'nama_customer' => 'Guest Testing Webhook',
            'total' => 66000,
            'metode_bayar' => '1',
            'status_bayar' => '0', // 0 = Belum Bayar
            'transaction_id' => $dummyOrderId,
            'snap_token' => 'dummy_token_123',
        ]);

        // Pastikan pesanan benar-benar masuk ke database dengan status 0
        $this->assertDatabaseHas('pesanan', [
            'transaction_id' => $dummyOrderId,
            'status_bayar' => '0',
        ]);

        // 2. SIAPKAN PAYLOAD WEBHOOK: Simulasikan data (JSON) yang dikirim Midtrans ketika Lunas (settlement)
        $payloadMidtrans = [
            'transaction_time'   => now()->format('Y-m-d H:i:s'),
            'transaction_status' => 'settlement',  // Arti: Pembayaran Sukses (Lunas)
            'transaction_id'     => '12345-midtrans-id',
            'status_message'     => 'midtrans payment notification',
            'status_code'        => '200',
            'signature_key'      => 'fake_signature',
            'payment_type'       => 'bank_transfer',
            'order_id'           => $dummyOrderId, // Harus sama dengan order_id di database
            'gross_amount'       => '66000.00',
            'fraud_status'       => 'accept',
            'currency'           => 'IDR'
        ];

        // 3. JALANKAN TEST: Tembak URL Webhook Anda menggunakan metode POST
        $response = $this->postJson('/webhook/midtrans', $payloadMidtrans);

        // 4. ASSERTION (PENCOCOKKAN HASIL):
        // a. Pastikan controller merespon Http 200 (OK)
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Notification processed successfully']);

        // b. Pastikan data di tabel 'pesanan' BERUBAH status_bayar-nya dari '0' menjadi '1' (Lunas)
        $this->assertDatabaseHas('pesanan', [
            'transaction_id' => $dummyOrderId,
            'status_bayar'   => '1', // 1 = Lunas
        ]);
        
        // c. Pastikan tidak ada lagi pesanan dengan ID tersebut yang masih berstatus '0'
        $this->assertDatabaseMissing('pesanan', [
            'transaction_id' => $dummyOrderId,
            'status_bayar'   => '0',
        ]);
    }
}
