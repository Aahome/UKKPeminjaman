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
        // Hapus procedure jika sudah ada, agar migrate ulang tidak error
        DB::unprepared('DROP PROCEDURE IF EXISTS approve_borrowing');
        DB::unprepared('DROP PROCEDURE IF EXISTS reject_borrowing');
        DB::unprepared('DROP PROCEDURE IF EXISTS store_return');

        // Procedure untuk menyetujui peminjaman
        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE approve_borrowing(IN p_borrowing_id INT)
        BEGIN
            DECLARE v_tool_id INT;
            DECLARE v_qty INT;
            DECLARE v_status VARCHAR(20);
            DECLARE v_stock INT;

            -- Handler jika terjadi exception, rollback transaksi
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;

            START TRANSACTION; -- Mulai transaksi agar perubahan atomic

            -- Ambil data borrowing dan kunci baris agar tidak ada race condition
            SELECT tool_id, quantity, status
            INTO v_tool_id, v_qty, v_status
            FROM borrowings
            WHERE id = p_borrowing_id
            FOR UPDATE;

            -- Validasi status, hanya pending yang bisa di-approve
            IF v_status != 'pending' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only pending borrowings can be approved.';
            END IF;

            -- Ambil stok alat yang terkait dan lock row
            SELECT stock INTO v_stock FROM tools WHERE id = v_tool_id FOR UPDATE;

            -- Validasi stok
            IF v_stock < v_qty THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tool stock not available.';
            END IF;

            -- Update status peminjaman dan kurangi stok
            UPDATE borrowings SET status = 'approved' WHERE id = p_borrowing_id;
            UPDATE tools SET stock = stock - v_qty WHERE id = v_tool_id;

            COMMIT; -- Commit jika semua validasi lolos
        END
        SQL
        );

        // Procedure untuk menolak peminjaman
        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE reject_borrowing(IN p_borrowing_id INT, IN p_reason VARCHAR(255))
        BEGIN
            DECLARE v_status VARCHAR(20);

            -- Handler jika terjadi exception, rollback transaksi
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;

            START TRANSACTION; -- Mulai transaksi

            -- Ambil status peminjaman dan lock row
            SELECT status INTO v_status FROM borrowings WHERE id = p_borrowing_id FOR UPDATE;

            -- Validasi hanya pending yang bisa ditolak
            IF v_status != 'pending' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Only pending borrowings can be rejected.';
            END IF;

            -- Update status dan alasan penolakan
            UPDATE borrowings SET status = 'rejected', rejection_reason = p_reason WHERE id = p_borrowing_id;

            COMMIT; -- Commit jika semua validasi lolos
        END
        SQL
        );

        // Procedure untuk menyimpan data pengembalian dan menghitung denda
        DB::unprepared(<<<'SQL'
        CREATE PROCEDURE store_return(IN p_borrowing_id INT)
        BEGIN
            DECLARE v_tool_id INT;
            DECLARE v_qty INT;
            DECLARE v_due_date DATE;
            DECLARE v_price DECIMAL(10,2);
            DECLARE v_fine DECIMAL(12,2);
            DECLARE v_status VARCHAR(20);
            DECLARE v_return_exists INT;

            -- Handler jika terjadi exception, rollback transaksi
            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;

            START TRANSACTION; -- Mulai transaksi

            -- Ambil data borrowing dan lock row
            SELECT tool_id, quantity, due_date, status
            INTO v_tool_id, v_qty, v_due_date, v_status
            FROM borrowings
            WHERE id = p_borrowing_id
            FOR UPDATE;

            -- Ambil harga tool
            SELECT price INTO v_price
            FROM tools
            WHERE id = v_tool_id;

            -- Check if status is returned and return data already exists
            SELECT COUNT(*) INTO v_return_exists
            FROM returns
            WHERE borrowing_id = p_borrowing_id;

            IF v_status = 'returned' AND v_return_exists > 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This borrowing has already been returned.';
            END IF;

            -- Hitung denda menggunakan fine_count function dengan price parameter
            SELECT fine_count(v_due_date, CURDATE(), v_qty, v_price) INTO v_fine;

            -- Simpan data pengembalian
            INSERT INTO returns (borrowing_id, return_date, fine, created_at, updated_at)
            VALUES (p_borrowing_id, CURDATE(), v_fine, NOW(), NOW());

            -- Kembalikan stok alat
            UPDATE tools SET stock = stock + v_qty WHERE id = v_tool_id;

            COMMIT; -- Commit jika semua operasi sukses
        END
        SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus procedure saat rollback
        DB::unprepared('DROP PROCEDURE IF EXISTS approve_borrowing');
        DB::unprepared('DROP PROCEDURE IF EXISTS reject_borrowing');
        DB::unprepared('DROP PROCEDURE IF EXISTS store_return');
    }
};
