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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->date('return_date');
            $table->integer('fine')->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS returns_after_insert');
        // Trigger INSERT pada returns: otomatis mencatat ketika tool dikembalikan
        // NEW.created_by adalah ID user yang mencatat pengembalian tool
        // Activity string menyertakan return ID dan borrowing ID untuk kemudahan tracking
        // new_data menyimpan detail pengembalian: tanggal kembali, denda, dll
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER returns_after_insert
        AFTER INSERT ON returns
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created return Id: ', NEW.id, ' for borrowing Id: ', NEW.borrowing_id), JSON_OBJECT('id', NEW.id, 'borrowing_id', NEW.borrowing_id, 'return_date', NEW.return_date, 'fine', NEW.fine, 'total_price', NEW.total_price), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS returns_after_update');
        // Trigger UPDATE pada returns: mencatat perubahan data pengembalian seperti tanggal kembali atau denda
        // NEW.modified_by adalah user yang melakukan perubahan (biasanya staff)
        // Menyimpan baik data lama maupun data baru untuk audit trail perubahan denda
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER returns_after_update
        AFTER UPDATE ON returns
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated return Id: ', NEW.id, ' for borrowing Id: ', NEW.borrowing_id), JSON_OBJECT('id', NEW.id, 'borrowing_id', NEW.borrowing_id, 'return_date', NEW.return_date, 'fine', NEW.fine, 'total_price', NEW.total_price), JSON_OBJECT('id', OLD.id, 'borrowing_id', OLD.borrowing_id, 'return_date', OLD.return_date, 'fine', OLD.fine, 'total_price', OLD.total_price), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS returns_after_delete');
        // Trigger DELETE pada returns: mencatat penghapusan data pengembalian
        // OLD.modified_by adalah user yang menghapus record (pembatalan pengembalian)
        // old_data penting untuk mengetahui record apa yang dihapus dan siapa yang menghapusnya
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER returns_after_delete
        AFTER DELETE ON returns
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted return Id: ', OLD.id, ' for borrowing Id: ', OLD.borrowing_id), NULL, JSON_OBJECT('id', OLD.id, 'borrowing_id', OLD.borrowing_id, 'return_date', OLD.return_date, 'fine', OLD.fine, 'total_price', OLD.total_price), NOW(), NOW());
        END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
