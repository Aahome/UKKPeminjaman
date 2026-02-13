<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus function jika sudah ada untuk menghindari error saat migrate ulang
        DB::unprepared('DROP FUNCTION IF EXISTS fine_count');

        DB::unprepared("
            CREATE FUNCTION fine_count(date1 DATE, date2 DATE, qty INT)
            RETURNS INT
            DETERMINISTIC
            -- Menghitung denda berdasarkan:
            -- (selisih hari keterlambatan x 5000 x jumlah barang)
            -- GREATEST digunakan agar hasil tidak pernah negatif
            RETURN (
                GREATEST(DATEDIFF(date2, date1), 0) * 5000 * qty
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus function saat rollback migration
        DB::unprepared('DROP FUNCTION IF EXISTS fine_count');
    }
};
