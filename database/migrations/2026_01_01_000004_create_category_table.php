<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [VP] DDL Modul 3 §4.1 — tabel Category
 * STATUS: KERANGKA (hanya PK).
 * TODO VP: tambahkan kolom name VARCHAR(50) UNIQUE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Category', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            // TODO VP — name VARCHAR(50) NOT NULL, CONSTRAINT uq_category_name UNIQUE (name)
        });
    }

    public function down(): void { Schema::dropIfExists('Category'); }
};