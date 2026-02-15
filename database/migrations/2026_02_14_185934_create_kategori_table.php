<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->integer('idkategori')->autoIncrement();
            $table->string('nama_kategori', 100);
        });

        DB::table('kategori')->insert([
            ['nama_kategori' => 'Novel'],
            ['nama_kategori' => 'Biografi'],
            ['nama_kategori' => 'Komik'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
