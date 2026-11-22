<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [VP] EKSTENSI di luar 9 tabel DDL — tabel Room (lantai/ruangan per gedung)
 * Menutup SCF-SRS-10 & SCF-SRS-12 (Critical).
 * STATUS: KERANGKA (hanya PK).
 * TODO VP: locationId CHAR(36) FK Location cascadeOnDelete, name VARCHAR(50),
 *          UNIQUE(locationId, name), INDEX(locationId).
 * Catatan: kolom floorRoom pada LostReport/FoundReport TETAP VARCHAR(50);
 *          tabel ini hanya menyuplai pilihan dropdown & filter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Room', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            // TODO VP — locationId, name, unique(locationId,name)
        });
    }

    public function down(): void { Schema::dropIfExists('Room'); }
};