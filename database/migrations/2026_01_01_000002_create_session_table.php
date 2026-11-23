<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Session', function (Blueprint $t) {
            $t->string('sid', 64)->primary();
            $t->char('userId', 36);
            $t->enum('hakAkses', ['CIVITAS', 'SECURITY']);
            $t->timestamp('createdAt')->useCurrent();
            $t->timestamp('expiredAt')->useCurrent();
            $t->foreign('userId', 'fk_session_user')->references('id')->on('User')->cascadeOnDelete();
        });
        DB::statement('ALTER TABLE `Session` ADD CONSTRAINT chk_session_expired CHECK (expiredAt > createdAt)');
    }

    public function down(): void { Schema::dropIfExists('Session'); }
};