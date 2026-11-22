<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Session', function(Blueprint $t){
            $t->string('sid', 64)->primary();
            $t->char('userId', 36);
            $t->enum('hakAkses', ['CIVITAS', 'SECURITY']);
            $t->timestamp('createdAt')->useCurrent();
            $t->timestamp('expiredAt');
            $t->foreign('userId', 'fk_session_user')->references('id')->on('User')->cascadeOnDelete();
        });
        // DB::statement dihapus karena tidak kompatibel dengan SQLite
    }

    public function down(): void
    {
        Schema::dropIfExists('Session');
    }
};