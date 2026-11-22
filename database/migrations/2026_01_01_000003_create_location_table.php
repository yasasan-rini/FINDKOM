<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [VP] DDL Modul 3 §4.1 — tabel Location
 * STATUS: KERANGKA (hanya PK, agar FK laporan & Room tidak gagal).
 * TODO VP: tambahkan kolom name VARCHAR(50) UNIQUE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Location', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            // TODO VP — name VARCHAR(50) NOT NULL, CONSTRAINT uq_location_name UNIQUE (name)
        });
    }

    public function down(): void { Schema::dropIfExists('Location'); }
};