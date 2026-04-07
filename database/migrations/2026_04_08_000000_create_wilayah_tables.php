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
        // 1. Tabel Provinsi (provinces)
        Schema::create('provinces', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('name', 255);
        });

        // 2. Tabel Kota/Kabupaten (regencies)
        Schema::create('regencies', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->char('province_id', 2);
            $table->string('name', 255);
            
            $table->foreign('province_id')
                  ->references('id')
                  ->on('provinces')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        // 3. Tabel Kecamatan (districts)
        Schema::create('districts', function (Blueprint $table) {
            $table->char('id', 7)->primary();
            $table->char('regency_id', 4);
            $table->string('name', 255);

            $table->foreign('regency_id')
                  ->references('id')
                  ->on('regencies')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        // 4. Tabel Kelurahan/Desa (villages)
        Schema::create('villages', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->char('district_id', 7);
            $table->string('name', 255);

            $table->foreign('district_id')
                  ->references('id')
                  ->on('districts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};