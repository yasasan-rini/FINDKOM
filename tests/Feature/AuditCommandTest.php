<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuditCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_audit_menyatakan_implementasi_lengkap(): void
    {
        $this->artisan('findkom:audit')->assertExitCode(0);
    }

    public function test_audit_json_memuat_semua_kelompok_artefak(): void
    {
        $kode = Artisan::call('findkom:audit', ['--json' => true]);
        $this->assertSame(0, $kode);

        $hasil = json_decode(Artisan::output(), true);
        $this->assertIsArray($hasil);
        $this->assertSame(0, $hasil['gagal_wajib']);
        $this->assertGreaterThan(100, $hasil['total']);

        $grup = collect($hasil['rincian'])->pluck('grup')->unique();
        foreach (['DDL 4.1', 'STD 2.3', 'PC 3.6', 'UI 3.7 / 4.3', 'Rute', 'CD 3.4', 'Pendukung'] as $g) {
            $this->assertTrue($grup->contains($g), "Kelompok artefak {$g} tidak diperiksa.");
        }
    }
}