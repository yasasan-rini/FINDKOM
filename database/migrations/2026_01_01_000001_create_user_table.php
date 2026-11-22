<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [AR] DDL Modul 3 §4.1 — tabel User
 * STATUS: KERANGKA (hanya PK, agar FK Session/Match/Notification milik YN tidak gagal).
 * TODO AR: lengkapi kolom & constraint sesuai DDL kalian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('User', function (Blueprint $t) {
            $t->char('id', 36)->primary();

            // TODO AR — kolom wajib:
            //   namaLengkap VARCHAR(100)
            //   nim         VARCHAR(20) NULL UNIQUE
            //   email       VARCHAR(100) UNIQUE  (CHECK REGEXP domain UB)
            //   username    VARCHAR(50) UNIQUE
            //   passwordHash VARCHAR(255)
            //   kontak      VARCHAR(20)
            //   role        ENUM(CIVITAS, SECURITY) DEFAULT CIVITAS
            //   createdAt   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            // TODO AR — CONSTRAINT chk_user_email:
            //   DB::statement("ALTER TABLE `User` ADD CONSTRAINT chk_user_email
            //     CHECK (email REGEXP '@(student[.])?ub[.]ac[.]id$')");
        });
    }

    public function down(): void { Schema::dropIfExists('User'); }
};