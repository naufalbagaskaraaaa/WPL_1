<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;

class PosUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_cari_barang_ditemukan()
    {
        $id_barang = 'BRG01';

        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('id_barang', $id_barang)->once()->andReturnSelf();
        $builder->shouldReceive('first')->once()->andReturn((object)[
            'id_barang' => 'BRG01',
            'nama' => 'Buku Laravel',
            'harga' => 100000
        ]);

        DB::shouldReceive('table')->with('barang')->once()->andReturn($builder);

        $controller = new PosController();
        $response = $controller->cariBarang($id_barang);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Buku Laravel', $data['data']['nama']);
    }

    public function test_cari_barang_tidak_ditemukan_return_404()
    {
        $id_barang = 'XXX';

        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('id_barang', $id_barang)->once()->andReturnSelf();
        $builder->shouldReceive('first')->once()->andReturn(null); // Barang tidak ada

        DB::shouldReceive('table')->with('barang')->once()->andReturn($builder);

        $controller = new PosController();
        $response = $controller->cariBarang($id_barang);
        $data = $response->getData(true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('error', $data['status']);
        $this->assertNull($data['data']);
    }

    public function test_simpan_transaksi_gagal_jika_keranjang_kosong()
    {
        $request = Request::create('/api/penjualan', 'POST', [
            'items' => [],
            'total' => 0
        ]);

        $controller = new PosController();
        $response = $controller->simpanTransaksi($request);
        $data = $response->getData(true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('error', $data['status']);
    }
}