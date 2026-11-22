<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [BK] DDL Modul 3 §4.1 — tabel LostReport
 * STATUS: KERANGKA. Hanya PK dibuat lebih dulu supaya foreign key
 *         tabel `Match` (milik YN) tidak gagal saat migrate.
 * TODO BK: lengkapi seluruh kolom, constraint, dan index sesuai DDL kalian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('LostReport', function (Blueprint $t) {
            $t->char('id', 36)->primary();

            // TODO BK — kolom wajib:
            //   reportCode  VARCHAR(20) UNIQUE
            //   reporterId  CHAR(36) FK -> User(id)
            //   locationId  CHAR(36) FK -> Location(id) ON DELETE RESTRICT
            //   floorRoom   VARCHAR(50)
            //   lostDate    DATE, lostTime TIME
            //   categoryId  CHAR(36) FK -> Category(id) ON DELETE RESTRICT
            //   description TEXT  (CHECK CHAR_LENGTH(TRIM(description)) >= 5)
            //   photoUrl    VARCHAR(255) NULL
            //   status      ENUM(LOST_REPORTED, MATCH_CANDIDATE, VERIFIED_MATCH,
            //                    NOTIFIED, RETURNED, CLOSED) DEFAULT LOST_REPORTED
            //   createdAt   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            // TODO BK — INDEX idx_lost_matching (status, locationId, categoryId, lostDate)
        });
    }

    public function down(): void { Schema::dropIfExists('LostReport'); }
};