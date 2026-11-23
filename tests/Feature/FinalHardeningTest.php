<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalHardeningTest extends TestCase
{
    //use RefreshDatabase;

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

    public function test_header_keamanan_terpasang_pada_setiap_respons(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("frame-ancestors 'none'",
            $this->get('/login')->headers->get('Content-Security-Policy'));
    }

    public function test_endpoint_ringkas_mengembalikan_status_laporan_milik_sendiri(): void
    {
        $this->masuk('rina.wulandari@student.ub.ac.id');
        $res = $this->getJson('/api/status-ringkas')->assertOk()
            ->assertJsonStructure(['unread', 'notifikasi', 'laporan']);
        $kode = collect($res->json('laporan'))->pluck('kode');
        $this->assertTrue($kode->contains('L-0005'));   // milik Rina
        $this->assertFalse($kode->contains('L-0008'));  // milik Dimas
    }

    public function test_endpoint_ringkas_menolak_tamu(): void
    {
        $this->get('/api/status-ringkas')->assertRedirect(route('login'));
    }

    public function test_ekspor_csv_mengikuti_filter_aktif(): void
    {
        $this->masuk('budi.santoso@ub.ac.id');
        $isi = $this->get('/semua-laporan/export?tipe=LOST&q=L-0007')->assertOk()->streamedContent();
        $this->assertStringContainsString('L-0007', $isi);
        $this->assertStringNotContainsString('L-0005', $isi);
        $this->assertStringNotContainsString('F-0005', $isi);
    }
}