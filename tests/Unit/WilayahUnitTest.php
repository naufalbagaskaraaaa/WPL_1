<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\DB;
use Mockery;

class WilayahUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test JSON structure for getProvinces.
     */
    public function test_get_provinces_returns_correct_json_format()
    {
        // Mock DB Builder
        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('get')->once()->andReturn(collect([
            (object) ['id' => '11', 'name' => 'ACEH']
        ]));
        DB::shouldReceive('table')->with('reg_provinces')->once()->andReturn($builder);

        $controller = new WilayahController();
        $response = $controller->getProvinces();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertArrayHasKey('message', $data);
        $this->assertIsArray($data['data']);
        $this->assertEquals('ACEH', $data['data'][0]['name']);
    }

    /**
     * Test JSON structure for getRegencies.
     */
    public function test_get_regencies_returns_correct_json_format()
    {
        $id_provinsi = '11';

        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('province_id', $id_provinsi)->once()->andReturnSelf();
        $builder->shouldReceive('get')->once()->andReturn(collect([
            (object) ['id' => '1101', 'province_id' => '11', 'name' => 'KABUPATEN SIMEULUE']
        ]));
        DB::shouldReceive('table')->with('reg_regencies')->once()->andReturn($builder);

        $controller = new WilayahController();
        $response = $controller->getRegencies($id_provinsi);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertCount(1, $data['data']);
        $this->assertEquals('KABUPATEN SIMEULUE', $data['data'][0]['name']);
    }

    /**
     * Test JSON structure for getDistricts.
     */
    public function test_get_districts_returns_correct_json_format()
    {
        $id_kota = '1101';

        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('regency_id', $id_kota)->once()->andReturnSelf();
        $builder->shouldReceive('get')->once()->andReturn(collect([
            (object) ['id' => '1101010', 'regency_id' => '1101', 'name' => 'TEUPAH SELATAN']
        ]));
        DB::shouldReceive('table')->with('reg_districts')->once()->andReturn($builder);

        $controller = new WilayahController();
        $response = $controller->getDistricts($id_kota);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(200, $data['code']);
    }

    /**
     * Test JSON structure for getVillages.
     */
    public function test_get_villages_returns_correct_json_format()
    {
        $id_kecamatan = '1101010';

        $builder = Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('where')->with('district_id', $id_kecamatan)->once()->andReturnSelf();
        $builder->shouldReceive('get')->once()->andReturn(collect([
            (object) ['id' => '1101010001', 'district_id' => '1101010', 'name' => 'LATIUNG']
        ]));
        DB::shouldReceive('table')->with('reg_villages')->once()->andReturn($builder);

        $controller = new WilayahController();
        $response = $controller->getVillages($id_kecamatan);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(200, $data['code']);
    }
}