<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * EKSTENSI (di luar 9 tabel DDL Modul 3) untuk memenuhi SCF-SRS-10 & SCF-SRS-12:
 * Security mengelola lantai/ruangan di dalam tiap gedung.
 * Kolom LostReport.floorRoom / FoundReport.floorRoom TETAP VARCHAR(50) sesuai DDL;
 * tabel ini hanya menyuplai pilihan dropdown & filter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Room', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->char('locationId', 36);
            $t->string('name', 50);
            $t->unique(['locationId', 'name'], 'uq_room_per_location');
            $t->index('locationId', 'idx_room_location');
            $t->foreign('locationId', 'fk_room_location')->references('id')->on('Location')->cascadeOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('Room'); }
};