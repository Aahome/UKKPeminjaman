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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS categories_after_insert');
        // Trigger INSERT: otomatis mencatat setiap kategori baru yang dibuat
        // AFTER INSERT artinya trigger dijalankan setelah INSERT berhasil
        // FOR EACH ROW artinya trigger berjalan untuk setiap baris yang diinsert
        // NEW merujuk ke baris baru yang baru saja diinsert sebelum trigger ini jalan
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER categories_after_insert
        AFTER INSERT ON categories
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created category Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'category_name', NEW.category_name, 'description', NEW.description, 'location', NEW.location), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS categories_after_update');
        // Trigger UPDATE: otomatis mencatat perubahan pada kategori
        // AFTER UPDATE berarti trigger jalan setelah UPDATE berhasil dilakukan
        // NEW berisi data baru setelah UPDATE
        // OLD berisi data lama sebelum UPDATE, untuk membuat audit trail
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER categories_after_update
        AFTER UPDATE ON categories
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated category Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'category_name', NEW.category_name, 'description', NEW.description, 'location', NEW.location), JSON_OBJECT('id', OLD.id, 'category_name', OLD.category_name, 'description', OLD.description, 'location', OLD.location), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS categories_after_delete');
        // Trigger DELETE: otomatis mencatat ketika kategori dihapus
        // AFTER DELETE jalan setelah penghapusan berhasil dilakukan
        // OLD berisi data yang baru saja dihapus
        // Menyimpan old_data penting untuk recovery dan audit trail
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER categories_after_delete
        AFTER DELETE ON categories
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted category Id: ', OLD.id), NULL, JSON_OBJECT('id', OLD.id, 'category_name', OLD.category_name, 'description', OLD.description, 'location', OLD.location), NOW(), NOW());
        END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
