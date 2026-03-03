<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [
            ['nama' => 'Makanya, Mikir!', 'harga' => 55000],
            ['nama' => 'Prinsip-Prinsip Ekonomi', 'harga' => 125000],
            ['nama' => 'Laskar Pelangi', 'harga' => 85000],
            ['nama' => 'Bumi Manusia', 'harga' => 120000],
            ['nama' => 'Cantik Itu Luka', 'harga' => 90000],
            ['nama' => 'Laut Bercerita', 'harga' => 95000],
            ['nama' => 'Negeri 5 Menara', 'harga' => 80000],
            ['nama' => 'Sapiens', 'harga' => 140000],
            ['nama' => 'The Psychology of Money', 'harga' => 110000],
            ['nama' => 'Rantau 1 Muara', 'harga' => 75000],
        ];

        foreach ($barangs as $barang) {
            DB::table('barang')->insert([
                'id_barang' => 'TEMP',
                'nama'      => $barang['nama'],
                'harga'     => $barang['harga'],
                'timestamp' => now(),
            ]);
        }
    }
}
