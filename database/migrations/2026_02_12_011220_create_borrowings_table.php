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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tool_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->enum('status', ['pending', 'rejected', 'approved', 'returned'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS borrowings_after_insert');
        // Trigger INSERT peminjaman: otomatis mencatat setiap peminjaman tool yang baru dibuat
        // NEW.created_by adalah ID user admin/staff yang membuat record peminjaman
        // Activity string menunjukkan action + ID record yang dibuat
        // new_data menyimpan informasi detail peminjaman dalam format JSON
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER borrowings_after_insert
        AFTER INSERT ON borrowings
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created borrowing Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'user_id', NEW.user_id, 'tool_id', NEW.tool_id, 'quantity', NEW.quantity, 'status', NEW.status), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS borrowings_after_update');
        // Trigger UPDATE peminjaman: mencatat perubahan status peminjaman seperti approve, reject, atau return
        // NEW.modified_by adalah user yang melakukan perubahan
        // Menyimpan old_data dan new_data untuk tracking perubahan status dan informasi peminjaman
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER borrowings_after_update
        AFTER UPDATE ON borrowings
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated borrowing Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'user_id', NEW.user_id, 'tool_id', NEW.tool_id, 'quantity', NEW.quantity, 'status', NEW.status), JSON_OBJECT('id', OLD.id, 'user_id', OLD.user_id, 'tool_id', OLD.tool_id, 'quantity', OLD.quantity, 'status', OLD.status), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS borrowings_after_delete');
        // Trigger DELETE peminjaman: mencatat penghapusan data peminjaman
        // OLD.modified_by dari user yang menghapus record
        // old_data penting untuk audit trail dan melihat history dari peminjaman yang dihapus
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER borrowings_after_delete
        AFTER DELETE ON borrowings
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted borrowing Id: ', OLD.id), NULL, JSON_OBJECT('id', OLD.id, 'user_id', OLD.user_id, 'tool_id', OLD.tool_id, 'quantity', OLD.quantity, 'status', OLD.status), NOW(), NOW());
        END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
