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
        Schema::create('pesanan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_customer');
        $table->integer('total');
        $table->string('metode_bayar')->nullable();
        $table->string('status_bayar')->default('Belum Bayar');
        
        $table->string('snap_token')->nullable();
        $table->string('transaction_id')->nullable();
        
        $table->timestamps();
    });

    Schema::create('detail_pesanans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
        $table->string('id_barang', 8); // Foreign key ke tabel barang
        $table->foreign('id_barang')->references('id_barang')->on('barang')->cascadeOnDelete();
        $table->integer('jumlah');
        $table->integer('harga');
        $table->integer('subtotal');
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
