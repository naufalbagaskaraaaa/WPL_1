<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class WilayahIntegrationTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat instance objek user sementara (make) tanpa menyimpannya ke database (create)
        $this->user = User::factory()->make();
    }

    /**
     * Test tampilan halaman jQuery dapat diakses dan memastikan elemen HTML (Selects) disertakan
     */
    public function test_jquery_view_can_be_rendered_and_contains_select_elements()
    {
        $response = $this->actingAs($this->user)->get('/wilayah/jquery');

        $response->assertStatus(200);
        $response->assertSee('Cascading Dropdown', false); // Cek title string persis
        $response->assertSee('jquery.min.js', false); // Memastikan ada library jquery
        $response->assertSee('<select id="provinsi"', false);
        $response->assertSee('<select id="kota"', false);
        $response->assertSee('<select id="kecamatan"', false);
        $response->assertSee('<select id="kelurahan"', false);
    }

    /**
     * Test tampilan halaman Axios dapat diakses dan memastikan elemen HTML (Selects) disertakan
     */
    public function test_axios_view_can_be_rendered_and_contains_select_elements()
    {
        $response = $this->actingAs($this->user)->get('/wilayah/axios');

        $response->assertStatus(200);
        $response->assertSee('Cascading Dropdown', false);
        $response->assertSee('axios.min.js', false); // Memastikan ada library axios
        $response->assertSee('<select id="provinsi"', false);
        $response->assertSee('<select id="kota"', false);
    }

    /**
     * Memastikan route provinces dapat diakses dengan respons JSON standar Anda (Integration)
     */
    public function test_provinces_endpoint_returns_successful_json_response()
    {
        // Mock DB connection agar tidak meminta query asli ke tabel databases
        $builder = \Mockery::mock(\Illuminate\Database\Query\Builder::class);
        $builder->shouldReceive('get')->once()->andReturn(collect([
            (object) ['id' => '11', 'name' => 'ACEH']
        ]));
        DB::shouldReceive('table')->with('reg_provinces')->once()->andReturn($builder);

        $response = $this->actingAs($this->user)->getJson('/wilayah/provinces');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'code',
            'message',
            'data' // Struktur JSON standar milik Anda
        ]);
        $response->assertJson([
            'status' => 'success',
            'code' => 200
        ]);
    }

    /**
     * Jika user belum diautentikasi (guest), akses wilayah harus diarahkan ke login
     */
    public function test_guest_cannot_access_wilayah_routes()
    {
        $response = $this->get('/wilayah/jquery');
        $response->assertRedirect('/login');

        $apiResponse = $this->getJson('/wilayah/provinces');
        $apiResponse->assertStatus(401); // Unauthorized Response jika dipanggil pakai Accept: application/json
    }
}