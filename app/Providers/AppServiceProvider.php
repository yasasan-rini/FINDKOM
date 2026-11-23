<?php

namespace App\Providers;

use App\Models\Room;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
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
        Carbon::setLocale(config('app.locale', 'id'));

        Paginator::defaultView('pagination');
        Paginator::defaultSimpleView('pagination');

        // NFR-SRS-04: HTTPS di produksi
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Mitigasi brute-force login (Modul 1 §1.5.d)
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(6)
            ->by(strtolower((string) $r->input('email')).'|'.$r->ip()));

        // SCF-SRS-12: daftar lantai/ruangan tersedia di form pelaporan & filter pencarian
        View::composer(
            ['lapor.kehilangan', 'lapor.penemuan', 'laporan.cari', 'laporan.semua'],
            fn ($view) => $view->with('daftarRuangan', Room::with('location')->orderBy('name')->get())
        );
    }
}