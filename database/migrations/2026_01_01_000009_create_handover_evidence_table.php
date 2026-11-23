<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HandoverEvidence', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->char('matchId', 36)->unique();
            $t->string('photoHolding', 255);
            $t->string('photoKtm', 255);
            $t->char('securityId', 36);
            $t->timestamp('handoverAt');
            $t->timestamp('createdAt')->useCurrent();
            $t->foreign('matchId', 'fk_handover_match')->references('id')->on('Match');
            $t->foreign('securityId', 'fk_handover_security')->references('id')->on('User');
        });
    }

    public function down(): void { Schema::dropIfExists('HandoverEvidence'); }
};