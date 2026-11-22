<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Match', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->string('matchCode', 20)->unique();
            $t->char('lostReportId', 36);
            $t->char('foundReportId', 36);
            $t->enum('status', ['MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'REJECTED', 'CLOSED'])
                ->default('MATCH_CANDIDATE');
            $t->char('verifiedBy', 36)->nullable();
            $t->timestamp('verifiedAt')->nullable();
            $t->text('revisionNote')->nullable();
            $t->timestamp('createdAt')->useCurrent();
            $t->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
            $t->unique(['lostReportId', 'foundReportId'], 'uq_match_pair');
            $t->foreign('lostReportId', 'fk_match_lost')->references('id')->on('LostReport');
            $t->foreign('foundReportId', 'fk_match_found')->references('id')->on('FoundReport');
            $t->foreign('verifiedBy', 'fk_match_security')->references('id')->on('User');
            $t->index(['status', 'createdAt'], 'idx_match_status');
        });
    }

    public function down(): void { Schema::dropIfExists('Match'); }
};