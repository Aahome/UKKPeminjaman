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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS roles_after_insert');
        // Trigger ketika ada data role baru yang dimasukkan
        // Trigger ini akan secara otomatis menyimpan log aktivitas ke tabel activity_logs
        // NEW.created_by berisi ID user yang membuat record ini
        // CONCAT menggabungkan string untuk membuat pesan aktivitas yang deskriptif
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER roles_after_insert
        AFTER INSERT ON roles
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created role Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'role_name', NEW.role_name), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS roles_after_update');
        // Trigger ketika ada data role yang diubah
        // NEW.modified_by berisi ID user yang memodifikasi record ini
        // Menyimpan data lama (OLD.*) dan data baru (NEW.*) untuk perbandingan
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER roles_after_update
        AFTER UPDATE ON roles
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated role Id: ', NEW.id), JSON_OBJECT('id', NEW.id, 'role_name', NEW.role_name), JSON_OBJECT('id', OLD.id, 'role_name', OLD.role_name), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS roles_after_delete');
        // Trigger ketika ada data role yang dihapus
        // OLD.modified_by berisi ID user yang menghapus record ini
        // new_data bernilai NULL karena data sudah dihapus, old_data menyimpan data yang dihapus
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER roles_after_delete
        AFTER DELETE ON roles
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted role Id: ', OLD.id), NULL, JSON_OBJECT('id', OLD.id, 'role_name', OLD.role_name), NOW(), NOW());
        END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
