<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('FoundReport', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->string('reportCode', 20)->unique();
            $t->char('reporterId', 36);
            $t->string('reporterName', 100);
            $t->char('locationId', 36);
            $t->string('floorRoom', 50);
            $t->date('foundDate');
            $t->time('foundTime');
            $t->char('categoryId', 36);
            $t->text('description');
            $t->string('photoUrl', 255); // foto wajib
            $t->enum('status', ['FOUND_REPORTED', 'MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'RETURNED', 'CLOSED'])
                ->default('FOUND_REPORTED');
            $t->char('receivedBy', 36)->nullable();
            $t->timestamp('receivedAt')->nullable();
            $t->text('receiptNote')->nullable();
            $t->timestamp('createdAt')->useCurrent();
            $t->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $t->foreign('reporterId', 'fk_found_reporter')->references('id')->on('User');
            $t->foreign('locationId', 'fk_found_location')->references('id')->on('Location')->restrictOnDelete();
            $t->foreign('categoryId', 'fk_found_category')->references('id')->on('Category')->restrictOnDelete();
            $t->foreign('receivedBy', 'fk_found_receiver')->references('id')->on('User');
            $t->index(['status', 'locationId', 'categoryId', 'foundDate'], 'idx_found_matching');
        });
        //DB::statement('ALTER TABLE FoundReport ADD CONSTRAINT chk_found_description CHECK (CHAR_LENGTH(TRIM(description)) >= 5)');
    }

    public function down(): void { Schema::dropIfExists('FoundReport'); }
};