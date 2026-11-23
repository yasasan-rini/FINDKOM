<?php

use App\Models\SesiPengguna;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('findkom:prune-sesi', function () {
    $n = SesiPengguna::where('expiredAt', '<', now())->delete();
    $this->info("{$n} sesi kedaluwarsa dihapus.");
})->purpose('Hapus sesi login yang sudah melewati expiredAt');

Schedule::command('findkom:prune-sesi')->hourly();