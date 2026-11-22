<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function masuk(string $email): void
    {
        $u = User::where('email', $email)->firstOrFail();
        $this->post('/login', ['email' => $u->email, 'password' => 'findkom123', 'peran' => $u->role]);
    }

    /** NFR-SRS-01: dashboard harus ringan (eager loading, bukan N+1). */
    public function test_dashboard_tidak_memicu_n_plus_one(): void
    {
        $this->masuk('budi.santoso@ub.ac.id');
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/dashboard')->assertOk();
        $jumlah = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertLessThan(60, $jumlah, "Dashboard memicu {$jumlah} query — indikasi N+1.");
    }

    /** NFR-SRS-01: halaman status & riwayat juga ringan. */
    public function test_halaman_status_tidak_memicu_n_plus_one(): void
    {
        $this->masuk('rina.wulandari@student.ub.ac.id');
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/status')->assertOk();
        $jumlah = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertLessThan(30, $jumlah, "Halaman status memicu {$jumlah} query — indikasi N+1.");
    }
}