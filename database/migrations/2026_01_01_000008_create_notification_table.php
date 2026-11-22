<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Notification', function (Blueprint $t) {
            $t->char('id', 36)->primary();
            $t->char('userId', 36);
            $t->char('matchId', 36)->nullable();
            $t->text('message');
            $t->boolean('isRead')->default(false);
            $t->timestamp('createdAt')->useCurrent();
            $t->foreign('userId', 'fk_notification_user')->references('id')->on('User')->cascadeOnDelete();
            $t->foreign('matchId', 'fk_notification_match')->references('id')->on('Match')->nullOnDelete();
        });
    }

    public function down(): void { Schema::dropIfExists('Notification'); }
};