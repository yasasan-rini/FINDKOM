<?php

namespace Tests\Feature;

use App\Models\HandoverEvidence;
use App\Models\LostReport;
use App\Models\Notification;
use App\Models\ReportMatch;
use App\Models\SesiPengguna;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    //use RefreshDatabase;

    private const CIVITAS = 'rina.wulandari@student.ub.ac.id';
    private const SECURITY = 'budi.santoso@ub.ac.id';
    private const LAIN = 'dimas.prasetyo@student.ub.ac.id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function masuk(string $email): User
    {
        $u = User::where('email', $email)->firstOrFail();
        $this->post('/login', ['email' => $u->email, 'password' => 'findkom123', 'peran' => $u->role]);
        return $u;
    }

    /** NFR-SRS-03 — bcrypt, bukan plaintext. */
    public function test_kata_sandi_tersimpan_sebagai_bcrypt(): void
    {
        $u = User::where('email', self::CIVITAS)->firstOrFail();
        $this->assertStringStartsWith('$2y$', $u->passwordHash);
        $this->assertTrue(Hash::check('findkom123', $u->passwordHash));
        $this->assertNotSame('findkom123', $u->passwordHash);
    }

    /** Abuse case: brute force login → rate limit. */
    public function test_login_dibatasi_enam_percobaan_per_menit(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', ['email' => 'salah@student.ub.ac.id', 'password' => 'tebakan', 'peran' => 'CIVITAS']);
        }
        $this->post('/login', ['email' => 'salah@student.ub.ac.id', 'password' => 'tebakan', 'peran' => 'CIVITAS'])
            ->assertStatus(429);
    }

    /** Session.isActive — sesi kedaluwarsa memaksa login ulang. */
    public function test_sesi_kedaluwarsa_memaksa_login_ulang(): void
    {
        $u = $this->masuk(self::CIVITAS);
        SesiPengguna::where('userId', $u->id)->update([
            'createdAt' => now()->subHours(13), 'expiredAt' => now()->subHour(),
        ]);
        $this->get('/beranda')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** Abuse case: unggahan berbahaya (.php) ditolak. */
    public function test_unggahan_non_gambar_ditolak(): void
    {
        $this->masuk(self::CIVITAS);
        $this->post('/lapor/penemuan', [
            'locationId' => \App\Models\Location::where('name', 'Gedung G')->value('id'),
            'categoryId' => \App\Models\Category::where('name', 'Elektronik')->value('id'),
            'floorRoom' => 'Lab G3.2', 'foundDate' => now()->toDateString(), 'foundTime' => '10:00',
            'description' => 'Laptop ASUS silver ditemukan di lab.',
            'foto' => UploadedFile::fake()->create('shell.php', 8, 'application/x-php'),
        ])->assertSessionHasErrors('foto');
    }

    /** Abuse case: XSS pada deskripsi laporan. */
    public function test_deskripsi_di_escape_pada_halaman_detail(): void
    {
        $this->masuk(self::CIVITAS);
        $this->post('/lapor/kehilangan', [
            'locationId' => \App\Models\Location::where('name', 'Gedung B')->value('id'),
            'categoryId' => \App\Models\Category::where('name', 'Aksesoris')->value('id'),
            'floorRoom' => 'Ruang B1.1', 'lostDate' => now()->toDateString(), 'lostTime' => '09:00',
            'description' => '<script>alert(1)</script> payung lipat navy',
        ])->assertSessionHas('kodeLaporan');

        $kode = LostReport::orderByDesc('createdAt')->value('reportCode');
        $this->get("/laporan/lost/{$kode}")->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    /** Abuse case: IDOR — laporan CLOSED milik orang lain. */
    public function test_laporan_selesai_milik_orang_lain_tidak_bisa_dibuka(): void
    {
        $this->masuk(self::CIVITAS);
        $this->get('/laporan/lost/L-0001')->assertForbidden();
    }

    /** Privasi: foto KTM hanya untuk Security & pemilik barang. */
    public function test_foto_ktm_hanya_untuk_security_dan_pemilik(): void
    {
        $m = ReportMatch::where('matchCode', 'M-0005')->firstOrFail(); // pemilik: Rina
        $this->masuk(self::SECURITY);
        $this->post("/serah-terima/{$m->id}/bukti", [
            'fotoPemegangBarang' => UploadedFile::fake()->image('barang.jpg'),
            'fotoKTM' => UploadedFile::fake()->image('ktm.jpg'),
        ])->assertRedirect();

        $ev = HandoverEvidence::where('matchId', $m->id)->firstOrFail();
        Storage::disk('local')->assertExists($ev->photoKtm);
        Storage::disk('public')->assertMissing($ev->photoKtm);

        $this->get("/serah-terima/bukti/{$ev->id}/ktm")->assertOk();   // Security
        $this->post('/logout');

        $this->masuk(self::CIVITAS);                                   // pemilik
        $this->get("/serah-terima/bukti/{$ev->id}/ktm")->assertOk();
        $this->post('/logout');

        $this->masuk(self::LAIN);                                      // civitas lain
        $this->get("/serah-terima/bukti/{$ev->id}/ktm")->assertForbidden();
    }

    /** Notifikasi orang lain tidak bisa dibaca (scope userId). */
    public function test_notifikasi_orang_lain_tidak_bisa_dibaca(): void
    {
        $n = Notification::whereHas('user', fn ($q) => $q->where('email', self::SECURITY))->firstOrFail();
        $this->masuk(self::CIVITAS);
        $this->get("/notifikasi/{$n->id}")->assertNotFound();
    }
}