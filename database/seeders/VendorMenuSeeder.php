<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama (opsional)
        // DB::table('detail_pesanans')->truncate();
        // DB::table('pesanan')->truncate();
        // DB::table('menu')->truncate();
        // DB::table('vendors')->truncate();

        // 1. Buat Data Vendor
        $vendor1 = DB::table('vendors')->insertGetId([
            'nama_vendor' => 'Warung Nasi Bu Ana',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vendor2 = DB::table('vendors')->insertGetId([
            'nama_vendor' => 'Kedai Minuman Segar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vendor3 = DB::table('vendors')->insertGetId([
            'nama_vendor' => 'Ayam Geprek Nendang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Buat Data Menu berdasarkan Vendor
        DB::table('menu')->insert([
            // Menu Vendor 1
            ['vendor_id' => $vendor1, 'nama_menu' => 'Nasi Goreng Spesial', 'harga' => 15000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor1, 'nama_menu' => 'Mie Goreng Telur', 'harga' => 12000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor1, 'nama_menu' => 'Nasi Uduk', 'harga' => 10000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],

            // Menu Vendor 2
            ['vendor_id' => $vendor2, 'nama_menu' => 'Es Teh Manis', 'harga' => 4000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor2, 'nama_menu' => 'Es Jeruk', 'harga' => 5000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor2, 'nama_menu' => 'Kopi Hitam Panas', 'harga' => 4000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor2, 'nama_menu' => 'Jus Alpukat', 'harga' => 8000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],

            // Menu Vendor 3
            ['vendor_id' => $vendor3, 'nama_menu' => 'Paket Geprek Level 1', 'harga' => 18000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor3, 'nama_menu' => 'Paket Geprek Level 5', 'harga' => 20000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor3, 'nama_menu' => 'Jamur Krispi', 'harga' => 10000, 'path_gambar' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
