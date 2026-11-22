<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [VP] DDL Modul 3 §4.1 — tabel HandoverEvidence
 * STATUS: KERANGKA (hanya PK).
 * TODO VP: lengkapi kolom & constraint sesuai DDL kalian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HandoverEvidence', function (Blueprint $t) {
            $t->char('id', 36)->primary();

            // TODO VP — kolom wajib:
            //   matchId CHAR(36) NOT NULL UNIQUE  FK -> Match(id)
            //   photoHolding VARCHAR(255) NOT NULL
            //   photoKtm     VARCHAR(255) NOT NULL
            //   securityId   CHAR(36) NOT NULL FK -> User(id)
            //   handoverAt   TIMESTAMP NOT NULL
            //   createdAt    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        });
    }

    public function down(): void { Schema::dropIfExists('HandoverEvidence'); }
};