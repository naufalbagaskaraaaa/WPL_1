<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Penjualan
        Schema::create('penjualan', function (Blueprint $table) {
            $table->increments('id_penjualan'); // int4 PK dengan auto-increment
            $table->timestamp('timestamp');
            $table->integer('total');
        });

        // Tabel Penjualan Detail
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('idpenjualan_detail'); // int4 PK dengan auto-increment
            $table->integer('id_penjualan');
            $table->string('id_barang', 8);
            $table->smallInteger('jumlah');
            $table->integer('subtotal');

            // Foreign keys
            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualan')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
        Schema::dropIfExists('penjualan');
    }
};