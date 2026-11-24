<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FoundReport;
use App\Models\Location;
use App\Models\LostReport;
use App\Models\ReportMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FindkomFlowTest extends TestCase
{
    use RefreshDatabase;

    private const CIVITAS = 'rina.wulandari@student.ub.ac.id';
    private const SECURITY = 'budi.santoso@ub.ac.id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    private function masuk(string $email): User
    {
        $u = User::where('email', $email)->firstOrFail();
        $this->post('/login', ['email' => $u->email, 'password' => 'findkom123', 'peran' => $u->role]);
        return $u;
    }

    private function payloadLaporan(string $gedung, string $kategori, string $jenis): array
    {
        return [
            'locationId' => Location::where('name', $gedung)->value('id'),
            'categoryId' => Category::where('name', $kategori)->value('id'),
            'floorRoom' => 'Lab G3.2',
            ($jenis === 'lost' ? 'lostDate' : 'foundDate') => now()->toDateString(),
            ($jenis === 'lost' ? 'lostTime' : 'foundTime') => '09:30',
            'description' => 'Laptop ASUS silver dengan stiker himpunan di cover.',
        ];
    }

    /** UC-01 / PC001 */
    public function test_registrasi_membuat_akun_civitas_dengan_sandi_terhash(): void
    {
        $this->post('/registrasi', [
            'namaLengkap' => 'Nadia Putri', 'nim' => '255150409111088',
            'email' => 'nadia.putri@student.ub.ac.id', 'username' => 'nadiaputri',
            'password' => 'rahasia123', 'kontak' => '081234500011',
        ])->assertRedirect(route('login'));

        $u = User::where('email', 'nadia.putri@student.ub.ac.id')->firstOrFail();
        $this->assertSame('CIVITAS', $u->role);
        $this->assertTrue(Hash::check('rahasia123', $u->passwordHash));
    }

    public function test_registrasi_menolak_email_non_ub(): void
    {
        $this->post('/registrasi', [
            'namaLengkap' => 'Budi Luar', 'nim' => '255150409111099',
            'email' => 'budi@gmail.com', 'username' => 'budiluar',
            'password' => 'rahasia123', 'kontak' => '081234500022',
        ])->assertSessionHasErrors('email');
    }

    /** UC-02 / PC002 — tab peran harus cocok */
    public function test_login_ditolak_bila_peran_tidak_sesuai(): void
    {
        $this->post('/login', ['email' => self::CIVITAS, 'password' => 'findkom123', 'peran' => 'SECURITY'])
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_security_masuk_ke_dashboard(): void
    {
        $this->masuk(self::SECURITY);
        $this->get('/dashboard')->assertOk()->assertSee('Dashboard Security');
    }

    /** UC-03 / PC003 */
    public function test_laporan_kehilangan_terbit_kode_dan_status(): void
    {
        $this->masuk(self::CIVITAS);
        $this->post('/lapor/kehilangan', $this->payloadLaporan('Gedung B', 'Aksesoris', 'lost'))
            ->assertRedirect(route('kehilangan.form'))->assertSessionHas('kodeLaporan');
        $this->assertDatabaseHas('LostReport', ['floorRoom' => 'Lab G3.2', 'status' => 'LOST_REPORTED']);
    }

    /** UC-04 + UC-05 — pencocokan otomatis gedung + kategori + ±4 hari */
    public function test_laporan_penemuan_membentuk_kandidat_otomatis(): void
    {
        if (!extension_loaded('gd')) 
        {
            $this->markTestSkipped('GD extension not installed');
        }

        $this->masuk(self::CIVITAS);
        $data = $this->payloadLaporan('Gedung G', 'Elektronik', 'found');
        $data['foto'] = UploadedFile::fake()->image('laptop.jpg', 800, 600);
        $this->post('/lapor/penemuan', $data)->assertRedirect(route('penemuan.form'));

        $this->assertSame('MATCH_CANDIDATE', LostReport::where('reportCode', 'L-0008')->value('status'));
        $this->assertDatabaseHas('Match', ['status' => 'MATCH_CANDIDATE']);
        Storage::disk('public')->assertDirectoryExists('uploads/found');
    }

    public function test_penemuan_menolak_tanpa_foto(): void
    {
        $this->masuk(self::CIVITAS);
        $this->post('/lapor/penemuan', $this->payloadLaporan('Gedung G', 'Elektronik', 'found'))
            ->assertSessionHasErrors('foto');
    }

    /** UC-06 → UC-07 → notifikasi */
    public function test_verifikasi_menunggu_penerimaan_fisik_lalu_mengirim_notifikasi(): void
    {
        $match = ReportMatch::where('matchCode', 'M-0008')->firstOrFail(); // fisik belum diterima
        $this->masuk(self::SECURITY);

        $this->post("/kandidat/{$match->id}/cocok")->assertSessionHas('warning');
        $this->assertSame('MATCH_CANDIDATE', $match->fresh()->status);

        $this->post("/penerimaan/{$match->foundReportId}", ['catatan' => 'Kondisi sesuai'])
            ->assertRedirect(route('receipt.index'));
        $this->assertNotNull(FoundReport::find($match->foundReportId)->receivedAt);

        $this->post("/kandidat/{$match->id}/cocok")->assertRedirect(route('match.index'));
        $this->assertSame('NOTIFIED', $match->fresh()->status);
        $this->assertSame('NOTIFIED', LostReport::find($match->lostReportId)->status);
        $this->assertDatabaseHas('Notification', ['matchId' => $match->id, 'isRead' => false]);
    }

    /** UC-08 / PC015–PC017 */
    public function test_serah_terima_dua_foto_menutup_semua_laporan(): void
    {
        if (!extension_loaded('gd')) 
        {
            $this->markTestSkipped('GD extension not installed');
        }

        $m = ReportMatch::where('matchCode', 'M-0005')->firstOrFail();
        $this->masuk(self::SECURITY);

        $this->post("/serah-terima/{$m->id}/selesai", ['ktm_confirmed' => '1'])->assertSessionHas('warning');
        $this->assertSame('NOTIFIED', $m->fresh()->status);

        $this->post("/serah-terima/{$m->id}/bukti", [
            'fotoPemegangBarang' => UploadedFile::fake()->image('barang.jpg'),
            'fotoKTM' => UploadedFile::fake()->image('ktm.jpg'),
        ])->assertRedirect();
        $this->assertDatabaseHas('HandoverEvidence', ['matchId' => $m->id]);

        $this->post("/serah-terima/{$m->id}/selesai", ['ktm_confirmed' => '1'])
            ->assertRedirect(route('handover.index'));
        $this->assertSame('CLOSED', $m->fresh()->status);
        $this->assertSame('CLOSED', LostReport::find($m->lostReportId)->status);
        $this->assertSame('CLOSED', FoundReport::find($m->foundReportId)->status);
    }

    /** UC-09 / PC019–PC022 */
    public function test_master_data_menolak_duplikat_dan_penghapusan_terpakai(): void
    {
        $this->masuk(self::SECURITY);
        $this->post('/master', ['jenis' => 'gedung', 'name' => 'Gedung F'])->assertSessionHas('error');

        $id = Location::where('name', 'Gedung F')->value('id');
        $this->delete("/master/{$id}", ['jenis' => 'gedung'])->assertSessionHas('error');
        $this->assertDatabaseHas('Location', ['id' => $id]);

        $this->post('/master', ['jenis' => 'kategori', 'name' => 'Powerbank'])->assertSessionHas('success');
        $baru = Category::where('name', 'Powerbank')->value('id');
        $this->delete("/master/{$baru}", ['jenis' => 'kategori'])->assertSessionHas('success');
        $this->assertDatabaseMissing('Category', ['id' => $baru]);
    }

    /** SR-06 — otorisasi peran & IDOR */
    public function test_civitas_tidak_bisa_masuk_halaman_security_atau_mengubah_laporan_orang_lain(): void
    {
        $this->masuk(self::CIVITAS);
        $this->get('/dashboard')->assertRedirect(route('beranda'));
        $this->get('/master')->assertRedirect(route('beranda'));

        $milikDimas = LostReport::where('reportCode', 'L-0008')->firstOrFail();
        $this->delete("/laporan/lost/{$milikDimas->id}")->assertForbidden();
    }
}