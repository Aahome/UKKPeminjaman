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
        DB::unprepared('DROP FUNCTION IF EXISTS total_price');

        DB::unprepared("
            CREATE FUNCTION fine_count(date1 DATE, date2 DATE, total_price DECIMAL(12,2))
            RETURNS DECIMAL(12,2)
            DETERMINISTIC
            COMMENT 'Menghitung denda berdasarkan total harga x jumlah hari keterlambatan x 1%'
            RETURN (
                GREATEST(DATEDIFF(date2, date1), 0) * 0.01 * total_price
            );
        ");

        DB::unprepared("
            CREATE FUNCTION total_price(qty INT, price DECIMAL(10,2))
            RETURNS DECIMAL(12,2)
            DETERMINISTIC
            COMMENT 'Menghitung total harga (quantity x price)'
            RETURN (
                qty * price
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
        DB::unprepared('DROP FUNCTION IF EXISTS total_price');
    }
};
