<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookstoreVendorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Vendor / Penerbit Buku
        $vendors = [
            'Gramedia Pustaka Utama',
            'Bentang Pustaka',
            'Erlangga',
            'Noura Books',
            'Republika Penerbit',
            'Mizan Publishing'
        ];

        // insert tanpa truncate semua, biar aman jika ada relasi lain (walau sudah direfactor)
        foreach ($vendors as $vendor) {
            DB::table('vendors')->updateOrInsert(
                ['nama_vendor' => $vendor],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
