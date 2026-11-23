<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('LostReport', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->string('reportCode', 20)->unique();
            $t->char('reporterId', 36);
            $t->char('locationId', 36);
            $t->string('floorRoom', 50);
            $t->date('lostDate');
            $t->time('lostTime');
            $t->char('categoryId', 36);
            $t->text('description');
            $t->string('photoUrl', 255)->nullable();
            $t->enum('status', ['LOST_REPORTED', 'MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'RETURNED', 'CLOSED'])
                ->default('LOST_REPORTED');
            $t->timestamp('createdAt')->useCurrent();
            $t->foreign('reporterId', 'fk_lost_reporter')->references('id')->on('User');
            $t->foreign('locationId', 'fk_lost_location')->references('id')->on('Location')->restrictOnDelete();
            $t->foreign('categoryId', 'fk_lost_category')->references('id')->on('Category')->restrictOnDelete();
            $t->index(['status', 'locationId', 'categoryId', 'lostDate'], 'idx_lost_matching');
        });
        //DB::statement('ALTER TABLE LostReport ADD CONSTRAINT chk_lost_description CHECK (CHAR_LENGTH(TRIM(description)) >= 5)');
    }

    public function down(): void { Schema::dropIfExists('LostReport'); }
};