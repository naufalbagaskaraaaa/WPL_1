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
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_barang', 8)->primary();
            $table->string('nama', 50);
            $table->integer('harga');
            $table->timestamp('timestamp');
        });

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_id_barang()
            RETURNS TRIGGER AS \$\$
            DECLARE
                nr INTEGER DEFAULT 0;
                id_baru TEXT;
            BEGIN
                -- Hitung jumlah data yang diinsert HARI INI
                -- Logika sama dengan modul: count where DAY, MONTH, YEAR = hari ini
                SELECT COUNT(id_barang) INTO nr 
                FROM barang 
                WHERE DATE(timestamp) = CURRENT_DATE;

                nr := nr + 1;

                -- Format ID: YY + MM + DD + urutan (2 digit)
                -- Contoh: 25030201 = tahun 25, bulan 03, tgl 02, urutan ke-1
                id_baru := RIGHT(EXTRACT(YEAR FROM CURRENT_TIMESTAMP)::TEXT, 2) ||
                           LPAD(EXTRACT(MONTH FROM CURRENT_TIMESTAMP)::TEXT, 2, '0') ||
                           LPAD(EXTRACT(DAY FROM CURRENT_TIMESTAMP)::TEXT, 2, '0') ||
                           LPAD(nr::TEXT, 2, '0');

                NEW.id_barang := id_baru;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            CREATE TRIGGER trigger_id_barang
            BEFORE INSERT ON barang
            FOR EACH ROW
            EXECUTE FUNCTION generate_id_barang();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trigger_id_barang ON barang");
        DB::unprepared("DROP FUNCTION IF EXISTS generate_id_barang");
        Schema::dropIfExists('barangs');
    }
};
