<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [AR] DDL Modul 3 §4.1 — tabel FoundReport
 * STATUS: KERANGKA (hanya PK, agar FK tabel `Match` milik YN tidak gagal).
 * TODO AR: lengkapi kolom & constraint sesuai DDL kalian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('FoundReport', function (Blueprint $t) {
            $t->char('id', 36)->primary();

            // TODO AR — kolom wajib:
            //   reportCode VARCHAR(20) UNIQUE, reporterId FK User, reporterName VARCHAR(100)
            //   locationId FK Location (RESTRICT), floorRoom VARCHAR(50)
            //   foundDate DATE, foundTime TIME, categoryId FK Category (RESTRICT)
            //   description TEXT (CHECK min 5), photoUrl VARCHAR(255) NOT NULL (foto wajib)
            //   status ENUM(FOUND_REPORTED, MATCH_CANDIDATE, VERIFIED_MATCH,
            //               NOTIFIED, RETURNED, CLOSED) DEFAULT FOUND_REPORTED
            //   receivedBy CHAR(36) NULL FK User, receivedAt TIMESTAMP NULL, receiptNote TEXT NULL
            //   createdAt TIMESTAMP, updatedAt TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            // TODO AR — INDEX idx_found_matching (status, locationId, categoryId, foundDate)
        });
    }

    public function down(): void { Schema::dropIfExists('FoundReport'); }
};