<?php

namespace App\Providers;

use App\Models\Room;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tanggal Indonesia untuk translatedFormat()/diffForHumans()
        Carbon::setLocale(config('app.locale', 'id'));

        // Pakai tampilan pagination sendiri, bukan Tailwind bawaan
        Paginator::defaultView('pagination');
        Paginator::defaultSimpleView('pagination');

        // NFR-SRS-04: seluruh transfer data memakai HTTPS di produksi
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Mitigasi brute-force login (Modul 1 §1.5.d) — dipakai routes/web.php
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(6)
            ->by(strtolower((string) $r->input('email')).'|'.$r->ip()));

        // SCF-SRS-12: daftar lantai/ruangan untuk form pelaporan & filter pencarian.
        // Guard fase stub: bila kolom/relasi Room belum dibuat VP, kirim koleksi kosong
        // supaya halaman tidak error. Boleh dihapus setelah migration Room lengkap.
        View::composer(
            ['lapor.kehilangan', 'lapor.penemuan', 'laporan.cari', 'laporan.semua'],
            function ($view) {
                static $ruangan = null;
                if ($ruangan === null) {
                    $siap = Schema::hasTable('Room')
                        && Schema::hasColumn('Room', 'name')
                        && method_exists(Room::class, 'location');
                    $ruangan = $siap
                        ? Room::with('location')->orderBy('name')->get()
                        : collect();
                }
                $view->with('daftarRuangan', $ruangan);
            }
        );
    }
}