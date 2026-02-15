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
        Schema::create('buku', function (Blueprint $table) {
            $table->integer('idbuku')->autoIncrement();
            $table->string('kode', 20);
            $table->string('judul', 500);
            $table->string('pengarang', 200);
            $table->integer('idkategori');

            $table->foreign('idkategori')->references('idkategori')
            ->on('kategori')->onDelete('cascade');
        });

        DB::table('buku')->insert([
            [
            'kode' => 'NV-01',
            'judul' => 'Home Sweet Loan',
            'pengarang' => 'Almira Bastari',
            'idkategori' => 1,
            ],
            [
            'kode' => 'BO-01',
            'judul' => 'Mohammad Hatta, Untuk Negeriku',
            'pengarang' => 'Taufik Abdullah',
            'idkategori' => 2,
            ],
            [
            'kode' => 'NV-02',
            'judul' => 'Keajaiban Toko Kelontong Namiya',
            'pengarang' => 'Keigo Higashino',
            'idkategori' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
