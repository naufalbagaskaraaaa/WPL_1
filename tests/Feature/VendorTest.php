<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;

class VendorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_halaman_index_vendor_menampilkan_pilihan_vendor()
    {
        $vendor = Vendor::create(["nama_vendor" => "Kantin Mamah Dedeh Tested"]);
        $response = $this->get("/vendor");
        $response->assertStatus(200);
        $response->assertSee("Kantin Mamah Dedeh Tested");
    }

    public function test_halaman_master_menu_menampilkan_daftar_menu_tertentu()
    {
        $vendor = Vendor::create(["nama_vendor" => "Ayam Keprabon"]);
        Menu::create(["vendor_id" => $vendor->id, "nama_menu" => "Nasi Bakar", "harga" => 15000]);

        $response = $this->get("/vendor/".$vendor->id."/menu");
        $response->assertStatus(200);
        $response->assertSee("Nasi Bakar");
        $response->assertSee("15.000");
    }

    public function test_tambah_menu_berhasil_menyimpan_data_serta_simulasi_gambar()
    {
        Storage::fake("public");
        $vendor = Vendor::create(["nama_vendor" => "Vendor Test Image"]);
        $gambar = UploadedFile::fake()->image("steak.jpg");

        $response = $this->postJson("/vendor/".$vendor->id."/menu/tambah", [
            "nama_menu" => "Steak Lokal",
            "harga" => 35000,
            "gambar" => $gambar,
        ]);

        $response->assertStatus(200)->assertJson(["status" => "success", "code" => 200]);

        $this->assertDatabaseHas("menu", [
            "vendor_id" => $vendor->id,
            "nama_menu" => "Steak Lokal",
            "harga" => 35000,
        ]);

        $menu = Menu::where("nama_menu", "Steak Lokal")->first();
        
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk("public");
        $disk->assertExists($menu->path_gambar);
    }

    public function test_edit_menu_berhasil_memperbarui_data_walaupun_form_gambar_kosong()
    {
        $vendor = Vendor::create(["nama_vendor" => "Kantin Nasi"]);
        $menu = Menu::create([
            "vendor_id" => $vendor->id,
            "nama_menu" => "Nasi Padang",
            "harga" => 20000,
            "path_gambar" => "menu/gambar_lama.jpg"
        ]);

        $response = $this->postJson("/vendor/".$vendor->id."/menu/".$menu->id."/update", [
            "nama_menu" => "Nasi Padang Renyah",
            "harga" => 25000,
        ]);

        $response->assertStatus(200)->assertJson(["code" => 200]);

        $this->assertDatabaseHas("menu", [
            "id" => $menu->id,
            "nama_menu" => "Nasi Padang Renyah",
            "harga" => 25000,
        ]);
        
        $menuUlang = Menu::find($menu->id);
        $this->assertEquals("menu/gambar_lama.jpg", $menuUlang->path_gambar);
    }

    public function test_hapus_menu_berhasil_menghilangkan_data_dan_file_terkait_di_storage()
    {
        Storage::fake("public");
        $gambarFake = UploadedFile::fake()->image("es_jeruk.jpg");
        $pathGbr = $gambarFake->store("menu", "public");

        $vendor = Vendor::create(["nama_vendor" => "Es Jeruk Mantul"]);
        $menu = Menu::create([
            "vendor_id" => $vendor->id,
            "nama_menu" => "Es Jeruk",
            "harga" => 5000,
            "path_gambar" => $pathGbr
        ]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk("public");
        $disk->assertExists($pathGbr);

        $response = $this->deleteJson("/vendor/".$vendor->id."/menu/".$menu->id."/hapus");
        $response->assertStatus(200);

        $this->assertDatabaseMissing("menu", ["id" => $menu->id]);
        $disk->assertMissing($pathGbr);
    }

    public function test_pesanan_vendor_spesifik()
    {
        $vendorKita = Vendor::create(["nama_vendor" => "Warung Kita", "toko_tutup" => 0]);
        $vendorLain = Vendor::create(["nama_vendor" => "Warung Lain", "toko_tutup" => 0]);

        $mKita = Menu::create(["vendor_id" => $vendorKita->id, "nama_menu" => "Burger", "harga" => 20000]);
        $mLain = Menu::create(["vendor_id" => $vendorLain->id, "nama_menu" => "Spaghetti", "harga" => 15000]);

        $pesananLunas = Pesanan::create([
            "nama_customer" => "Joko",
            "total" => 35000,
            "status_bayar" => "1",
            "transaction_id" => "ORD-999",
        ]);
        DetailPesanan::create(["pesanan_id" => $pesananLunas->id, "menu_id" => $mKita->id, "jumlah" => 1, "harga" => 20000, "subtotal" => 20000]);
        DetailPesanan::create(["pesanan_id" => $pesananLunas->id, "menu_id" => $mLain->id, "jumlah" => 1, "harga" => 15000, "subtotal" => 15000]);

        $pesananBelumBayar = Pesanan::create([
            "nama_customer" => "Budi",
            "total" => 20000,
            "status_bayar" => "0",
            "transaction_id" => "ORD-1000",
        ]);
        DetailPesanan::create(["pesanan_id" => $pesananBelumBayar->id, "menu_id" => $mKita->id, "jumlah" => 1, "harga" => 20000, "subtotal" => 20000]);

        $response = $this->get("/vendor/".$vendorKita->id."/pesanan");

        $response->assertStatus(200);
        $response->assertSee("Joko");
        $response->assertSee("Burger");
        $response->assertDontSee("Spaghetti");
        $response->assertDontSee("Budi");
    }
}
