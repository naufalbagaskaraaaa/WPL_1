<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class PosIntegrationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user memory agar bisa melewati middleware 'auth'
        $this->user = User::factory()->make();
    }

    public function test_halaman_pos_jquery_bisa_diakses()
    {
        $response = $this->actingAs($this->user)->get('/pos/jquery');

        $response->assertStatus(200);
        $response->assertSee('Aplikasi Kasir (POS) - jQuery', false);
        $response->assertSee('jquery.min.js', false); // Ada script jQuery
        $response->assertSee('id="kode_barang"', false);
    }

    public function test_halaman_pos_axios_bisa_diakses()
    {
        $response = $this->actingAs($this->user)->get('/pos/axios');

        $response->assertStatus(200);
        // Samakan judul String-nya dengan di file axios.blade.php
        $response->assertSee('Aplikasi Kasir (POS) - Axios', false);
        $response->assertSee('axios.min.js', false); // Ada script Axios
        $response->assertSee('id="btn-tambahkan"', false);
    }

    public function test_api_pencarian_barang_mengembalikan_json()
    {
        // Mock DB connection untuk menghindari trigger plpgsql DB In-Memory
        $builder = \Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('id_barang', 'BRG123')->once()->andReturnSelf();
        $builder->shouldReceive('first')->once()->andReturn((object)[
            'id_barang' => 'BRG123',
            'nama' => 'Spidol Hitam',
            'harga' => 5000
        ]);

        DB::shouldReceive('table')->with('barang')->once()->andReturn($builder);

        $response = $this->actingAs($this->user)->getJson('/api/barang/BRG123');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'nama' => 'Spidol Hitam'
            ]
        ]);
    }

    public function test_api_penjualan_berhasil_menyimpan_transaksi()
    {
        // Simulasi DB Transaction (Begin, Commit)
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        // Simulasi Insert Tabel Penjualan (Parent) return ID Penjualan = 100
        $penjualanBuilder = \Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $penjualanBuilder->shouldReceive('insertGetId')->once()->andReturn(100);
        
        // Simulasi Insert Tabel Penjualan Detail (Child) return true
        $detailBuilder = \Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $detailBuilder->shouldReceive('insert')->twice()->andReturn(true); // called twice for 2 items

        // Sambungkan Mock ke Facade
        DB::shouldReceive('table')->with('penjualan')->andReturn($penjualanBuilder);
        DB::shouldReceive('table')->with('penjualan_detail')->andReturn($detailBuilder);

        $payload = [
            'total' => 20000,
            'items' => [
                [
                    'id_barang' => 'BRG1',
                    'jumlah' => 2,
                    'subtotal' => 10000
                ],
                [
                    'id_barang' => 'BRG2',
                    'jumlah' => 1,
                    'subtotal' => 10000
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->postJson('/api/penjualan', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id_penjualan' => 100
            ]
        ]);
    }
}