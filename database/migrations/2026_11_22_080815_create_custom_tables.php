<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('User', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('namaLengkap', 100);
            $table->string('nim', 20)->nullable()->unique();
            $table->string('email', 100)->unique();
            $table->string('username', 50)->unique();
            $table->string('passwordHash', 255);
            $table->string('kontak', 20);
            $table->enum('role', ['CIVITAS', 'SECURITY'])->default('CIVITAS');
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('Location', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name', 50)->unique();
        });

        Schema::create('Category', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name', 50)->unique();
        });

        Schema::create('LostReport', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('reportCode', 20)->unique();
            $table->string('reporterId', 36);
            $table->string('locationId', 36);
            $table->string('floorRoom', 50);
            $table->date('lostDate');
            $table->time('lostTime');
            $table->string('categoryId', 36);
            $table->text('description');
            $table->string('photoUrl', 255)->nullable();
            $table->enum('status', ['LOST_REPORTED','VERIFIED_MATCH','RETURNED','CLOSED'])->default('LOST_REPORTED');
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('FoundReport', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('reportCode', 20)->unique();
            $table->string('reporterId', 36)->nullable();
            $table->string('reporterName', 100)->nullable();
            $table->string('locationId', 36);
            $table->string('floorRoom', 50);
            $table->date('foundDate');
            $table->time('foundTime');
            $table->string('categoryId', 36);
            $table->text('description');
            $table->string('photoUrl', 255)->nullable();
            $table->enum('status', ['FOUND_REPORTED','VERIFIED_MATCH','RETURNED','CLOSED'])->default('FOUND_REPORTED');
            $table->string('receivedBy', 36)->nullable();
            $table->timestamp('receivedAt')->nullable();
            $table->text('receiptNote')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });

        Schema::create('Match', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('lostReportId', 36);
            $table->string('foundReportId', 36);
            $table->enum('status', ['MATCH_CANDIDATE','VERIFIED_MATCH','REJECTED'])->default('MATCH_CANDIDATE');
            $table->string('verifiedBy', 36)->nullable();
            $table->timestamp('verifiedAt')->nullable();
            $table->text('revisionNote')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });

        Schema::create('Notification', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('userId', 36);
            $table->string('matchId', 36)->nullable();
            $table->text('message');
            $table->boolean('isRead')->default(false);
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('HandoverEvidence', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('matchId', 36);
            $table->string('photoHolding', 255)->nullable();
            $table->string('photoKtm', 255)->nullable();
            $table->string('securityId', 36)->nullable();
            $table->timestamp('handoverAt')->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('Session', function (Blueprint $table) {
            $table->string('sid', 255)->primary();
            $table->string('userId', 36);
            $table->enum('hakAkses', ['CIVITAS','SECURITY']);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('expiredAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Session');
        Schema::dropIfExists('HandoverEvidence');
        Schema::dropIfExists('Notification');
        Schema::dropIfExists('Match');
        Schema::dropIfExists('FoundReport');
        Schema::dropIfExists('LostReport');
        Schema::dropIfExists('Category');
        Schema::dropIfExists('Location');
        Schema::dropIfExists('User');
    }
};