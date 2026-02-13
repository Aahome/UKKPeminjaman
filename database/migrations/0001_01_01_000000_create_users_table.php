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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        DB::unprepared('DROP TRIGGER IF EXISTS users_after_insert');
        // Trigger INSERT pada tabel users: mencatat ketika user baru dibuat
        // NEW.created_by mengambil ID user yang membuat data ini dari kolom created_by
        // CONCAT mengabungkan teks dan ID untuk membuat pesan deskriptif
        // JSON_OBJECT menyimpan data baru dalam format JSON untuk audit trail
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER users_after_insert
        AFTER INSERT ON users
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.created_by, CONCAT('created user Id: ', NEW.id, ' (', NEW.email, ')'), JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'email', NEW.email), NULL, NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS users_after_update');
        // Trigger UPDATE pada tabel users: mencatat perubahan data user
        // NEW.modified_by berisi ID user yang melakukan perubahan
        // Menyimpan data lama (OLD) dan data baru (NEW) untuk membandingkan apa yang berubah
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER users_after_update
        AFTER UPDATE ON users
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (NEW.modified_by, CONCAT('updated user Id: ', NEW.id, ' (', NEW.email, ')'), JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'email', NEW.email), JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'email', OLD.email), NOW(), NOW());
        END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS users_after_delete');
        // Trigger DELETE pada tabel users: mencatat ketika user dihapus
        // OLD.modified_by menggunakan kolom modified_by dari record yang dihapus
        // new_data = NULL karena data sudah tidak ada
        // old_data menyimpan data yang dihapus untuk keperluan rollback atau audit
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER users_after_delete
        AFTER DELETE ON users
        FOR EACH ROW
        BEGIN
            INSERT INTO activity_logs (user_id, activity, new_data, old_data, created_at, updated_at)
            VALUES (OLD.modified_by, CONCAT('deleted user Id: ', OLD.id, ' (', OLD.email, ')'), NULL, JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'email', OLD.email), NOW(), NOW());
        END
        SQL);

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
