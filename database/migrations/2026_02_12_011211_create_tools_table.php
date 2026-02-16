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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('tool_name');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->enum('condition', ['good', 'damaged'])->default('good');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS tools_after_insert');
        // Trigger berkaitan dengan pembuatan alat baru
        // Secara otomatis memanggil INSERT pada tabel activity_logs ketika tool baru dibuat
        // user_id diambil dari NEW.created_by (ID user yang membuat record)
        // Activity berisi deskripsi, new_data berisi data yang baru dibuat (dalam format JSON)
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER tools_after_insert
        AFTER INSERT ON tools
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created tool Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'tool_name', NEW.tool_name, 'price', NEW.price, 'stock', NEW.stock, 'condition', NEW.condition), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS tools_after_update');
        // Trigger ketika data tool diubah/diedit
        // NEW.modified_by mengambil ID user yang melakukan perubahan
        // JSON_OBJECT membuat snapshot dari data lama dan data baru untuk difaatkan tracking perubahan
        // Penting menyimpan keduanya untuk mengetahui apa saja yang berubah
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER tools_after_update
        AFTER UPDATE ON tools
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated tool Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'tool_name', NEW.tool_name, 'price', NEW.price, 'stock', NEW.stock, 'condition', NEW.condition), JSON_OBJECT('id', OLD.id, 'tool_name', OLD.tool_name, 'price', OLD.price, 'stock', OLD.stock, 'condition', OLD.condition), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS tools_after_delete');
        // Trigger ketika tool dihapus dari database
        // old_data menyimpan data tool yang dihapus untuk keperluan audit dan possibly recovery
        // new_data = NULL karena record sudah tidak ada di database
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER tools_after_delete
        AFTER DELETE ON tools
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted tool Id: ', OLD.id), NULL, JSON_OBJECT('id', OLD.id, 'tool_name', OLD.tool_name, 'price', OLD.price, 'stock', OLD.stock, 'condition', OLD.condition), NOW(), NOW());
        END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
