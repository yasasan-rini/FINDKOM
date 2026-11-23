<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('User', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->string('namaLengkap', 100);
            $t->string('nim', 20)->nullable()->unique();
            $t->string('email', 100)->unique();
            $t->string('username', 50)->unique();
            $t->string('passwordHash', 255);
            $t->string('kontak', 20);
            $t->enum('role', ['CIVITAS', 'SECURITY'])->default('CIVITAS');
            $t->timestamp('createdAt')->useCurrent();
        });
        //DB::statement("ALTER TABLE `User` ADD CONSTRAINT chk_user_email CHECK (email REGEXP '@(student[.])?ub[.]ac[.]id$')");
    }

    public function down(): void { Schema::dropIfExists('User'); }
};