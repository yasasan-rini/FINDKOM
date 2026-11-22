<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditFindkom extends Command
{
    protected $signature = 'findkom:audit {--json : Cetak hasil sebagai JSON}';
    protected $description = 'Verifikasi kelengkapan implementasi FINDKOM terhadap Modul 1-3 (DDL, STD, PC, UI, rute, model, konfigurasi)';

    private array $hasil = [];

    public function handle(): int
    {
        $this->cekSkema();
        $this->cekStatusEnum();
        $this->cekPseudocode();
        $this->cekUi();
        $this->cekRute();
        $this->cekModel();
        $this->cekPendukung();

        $koleksi = collect($this->hasil);
        $gagal = $koleksi->where('level', 'wajib')->where('ok', false)->count();
        $lulus = $koleksi->where('ok', true)->count();
        $total = $koleksi->count();

        if ($this->option('json')) {
            $this->line(json_encode(
                ['total' => $total, 'lulus' => $lulus, 'gagal_wajib' => $gagal, 'rincian' => $this->hasil],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ));
            return $gagal ? self::FAILURE : self::SUCCESS;
        }

        $this->table(['Artefak', 'Item', 'Status', 'Catatan'], $koleksi->map(fn ($r) => [
            $r['grup'], $r['item'],
            $r['ok'] ? '✓ LULUS' : ($r['level'] === 'wajib' ? '✗ GAGAL' : '• INFO'),
            $r['catatan'],
        ])->all());

        $this->newLine();
        $this->info("{$lulus}/{$total} pemeriksaan lulus.");

        if ($gagal > 0) {
            $this->error("{$gagal} pemeriksaan WAJIB gagal. Perbaiki item bertanda ✗ di atas.");
            return self::FAILURE;
        }

        $this->info('Implementasi lengkap terhadap Modul 1-3. Siap dikumpulkan.');
        return self::SUCCESS;
    }

    private function catat(string $grup, string $item, bool $ok, string $catatan = '', string $level = 'wajib'): void
    {
        $this->hasil[] = compact('grup', 'item', 'ok', 'catatan', 'level');
    }

    /** Modul 3 §4.1 — DDL 9 tabel inti + ekstensi Room (SCF-SRS-10/12). */
    private function cekSkema(): void
    {
        $skema = [
            'User' => ['id', 'namaLengkap', 'nim', 'email', 'username', 'passwordHash', 'kontak', 'role', 'createdAt'],
            'Session' => ['sid', 'userId', 'hakAkses', 'createdAt', 'expiredAt'],
            'Location' => ['id', 'name'],
            'Category' => ['id', 'name'],
            'LostReport' => ['id', 'reportCode', 'reporterId', 'locationId', 'floorRoom', 'lostDate', 'lostTime',
                'categoryId', 'description', 'photoUrl', 'status', 'createdAt'],
            'FoundReport' => ['id', 'reportCode', 'reporterId', 'reporterName', 'locationId', 'floorRoom', 'foundDate',
                'foundTime', 'categoryId', 'description', 'photoUrl', 'status', 'receivedBy', 'receivedAt',
                'receiptNote', 'createdAt', 'updatedAt'],
            'Match' => ['id', 'matchCode', 'lostReportId', 'foundReportId', 'status', 'verifiedBy', 'verifiedAt',
                'revisionNote', 'createdAt', 'updatedAt'],
            'Notification' => ['id', 'userId', 'matchId', 'message', 'isRead', 'createdAt'],
            'HandoverEvidence' => ['id', 'matchId', 'photoHolding', 'photoKtm', 'securityId', 'handoverAt', 'createdAt'],
            'Room' => ['id', 'locationId', 'name'],
        ];

        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            foreach ($skema as $tabel => $kolom) {
                $this->catat('DDL 4.1', "Tabel {$tabel}", false, 'Koneksi database gagal — periksa .env lalu jalankan migrate');
            }
            return;
        }

        foreach ($skema as $tabel => $kolom) {
            $ada = Schema::hasTable($tabel);
            $hilang = $ada
                ? array_values(array_filter($kolom, fn ($c) => ! Schema::hasColumn($tabel, $c)))
                : $kolom;

            $this->catat(
                'DDL 4.1',
                "Tabel {$tabel}".($tabel === 'Room' ? ' (ekstensi)' : ''),
                $ada && empty($hilang),
                ! $ada ? 'Belum ada — jalankan php artisan migrate'
                    : ($hilang ? 'Kolom hilang: '.implode(', ', $hilang) : count($kolom).' kolom sesuai PDM')
            );
        }
    }

    /** Modul 1 §2.3 — transisi status harus sama dengan enum DDL. */
    private function cekStatusEnum(): void
    {
        $harapan = [
            'LostReport' => ['LOST_REPORTED', 'MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'RETURNED', 'CLOSED'],
            'FoundReport' => ['FOUND_REPORTED', 'MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'RETURNED', 'CLOSED'],
            'Match' => ['MATCH_CANDIDATE', 'VERIFIED_MATCH', 'NOTIFIED', 'REJECTED', 'CLOSED'],
        ];

        try {
            if (DB::getDriverName() !== 'mysql') {
                $this->catat('STD 2.3', 'Enum status', true, 'Dilewati (bukan MySQL)', 'info');
                return;
            }
            foreach ($harapan as $tabel => $status) {
                if (! Schema::hasTable($tabel)) {
                    $this->catat('STD 2.3', "Enum {$tabel}.status", false, 'Tabel belum ada');
                    continue;
                }
                $kolom = DB::selectOne("SHOW COLUMNS FROM `{$tabel}` WHERE Field = 'status'");
                $tipe = (string) ($kolom->Type ?? '');
                $kurang = array_values(array_filter($status, fn ($s) => ! str_contains($tipe, "'{$s}'")));
                $this->catat('STD 2.3', "Enum {$tabel}.status", empty($kurang),
                    $kurang ? 'Nilai hilang: '.implode(', ', $kurang) : count($status).' status sesuai DDL');
            }
        } catch (Throwable $e) {
            $this->catat('STD 2.3', 'Enum status', false, 'Gagal membaca skema: '.$e->getMessage());
        }
    }

    /** Modul 2 §3.6 — PC001..PC022 harus punya method nyata. */
    private function cekPseudocode(): void
    {
        $peta = [
            'PC001 registrasi' => ['RegistrasiController', 'registrasi'],
            'PC002 autentikasi' => ['LoginController', 'autentikasi'],
            'PC003 buatLaporan (hilang)' => ['LaporanKehilanganController', 'buatLaporan'],
            'PC004 buatLaporan (temuan)' => ['LaporanPenemuanController', 'buatLaporan'],
            'PC005 requestKandidatSiapVerifikasi' => ['MatchController', 'index'],
            'PC006 requestDetailKandidat' => ['MatchController', 'detail'],
            'PC007 verifikasiCocok' => ['MatchController', 'cocok'],
            'PC008 tolakKandidat' => ['MatchController', 'tolak'],
            'PC009 kirimHimbauanRevisi' => ['MatchController', 'revisi'],
            'PC010 requestHalaman' => ['ReceiptController', 'index'],
            'PC011 cariLaporanPenemuan' => ['ReceiptController', 'index'],
            'PC012 konfirmasiPenerimaan' => ['ReceiptController', 'konfirmasi'],
            'PC013 requestDaftarTerverifikasi' => ['HandoverController', 'index'],
            'PC014 getDetailSerahTerima' => ['HandoverController', 'form'],
            'PC015 submitBukti' => ['HandoverController', 'bukti'],
            'PC016 validasiKelengkapanBukti' => ['HandoverController', 'bukti'],
            'PC017 selesaikanLaporan' => ['HandoverController', 'selesai'],
            'PC018 requestDaftar' => ['MasterDataController', 'index'],
            'PC019 tambahData' => ['MasterDataController', 'tambah'],
            'PC020 validasiData' => ['MasterDataController', 'validasiData'],
            'PC021 hapusData (konfirmasi)' => ['MasterDataController', 'hapus'],
            'PC022 konfirmasiHapus' => ['MasterDataController', 'hapus'],
        ];

        foreach ($peta as $label => [$kelas, $method]) {
            $fqcn = "App\\Http\\Controllers\\{$kelas}";
            $ada = class_exists($fqcn) && method_exists($fqcn, $method);
            $this->catat('PC 3.6', $label, $ada,
                $ada ? "{$kelas}@{$method}" : "Method {$kelas}::{$method} tidak ditemukan");
        }

        $this->catat('UC-05', 'Mesin pencocokan otomatis',
            class_exists(\App\Services\MatchingService::class)
            && method_exists(\App\Services\MatchingService::class, 'run'),
            'MatchingService::run — gedung + kategori + ±4 hari');
    }

    /** Modul 2 §3.7 (UI-01..UI-12) & Modul 3 §4.3 (UI-001..UI-006). */
    private function cekUi(): void
    {
        $peta = [
            'UI-01 / UI-001 Registrasi' => 'auth.register',
            'UI-02 / UI-002 Login' => 'auth.login',
            'UI-03 / UI-003 Lapor Kehilangan' => 'lapor.kehilangan',
            'UI-04 / UI-004 Lapor Penemuan' => 'lapor.penemuan',
            'UI-05 / UI-005 Kandidat (daftar)' => 'match.index',
            'UI-05 / UI-005 Kandidat (detail)' => 'match.detail',
            'UI-06 Konfirmasi Penerimaan' => 'receipt.index',
            'UI-07 / UI-006 Serah Terima (daftar)' => 'handover.index',
            'UI-07 / UI-006 Serah Terima (form)' => 'handover.form',
            'UI-08 Master Data' => 'master.index',
            'UI-09 Status & Riwayat' => 'laporan.status',
            'UI-10 Cari Barang' => 'laporan.cari',
            'UI-11 Dashboard Security' => 'security.dashboard',
            'UI-12 Semua Laporan' => 'laporan.semua',
            'Beranda Civitas' => 'civitas.beranda',
            'Detail Laporan' => 'laporan.detail',
            'Layout bersama' => 'layouts.app',
            'Partial linimasa status' => 'laporan._timeline',
        ];

        foreach ($peta as $label => $view) {
            $ada = view()->exists($view);
            $this->catat('UI 3.7 / 4.3', $label, $ada, $ada ? "resources/views/{$view}" : "View {$view} belum ada");
        }
    }

    private function cekRute(): void
    {
        $nama = [
            'login', 'autentikasi', 'login.demo', 'registrasi.form', 'registrasi', 'logout',
            'beranda', 'laporan.buat', 'kehilangan.form', 'kehilangan.buat', 'penemuan.form', 'penemuan.buat',
            'laporan.status', 'laporan.cari', 'laporan.detail', 'laporan.edit', 'laporan.update', 'laporan.destroy',
            'dashboard', 'match.index', 'match.detail', 'match.cocok', 'match.tolak', 'match.revisi',
            'receipt.index', 'receipt.konfirmasi',
            'handover.index', 'handover.form', 'handover.bukti', 'handover.selesai', 'handover.tunda', 'handover.foto',
            'master.index', 'master.tambah', 'master.ubah', 'master.hapus',
            'laporan.semua', 'laporan.export', 'notifikasi.baca', 'notifikasi.bacaSemua', 'api.ringkas',
        ];

        foreach ($nama as $n) {
            $ada = Route::has($n);
            $this->catat('Rute', $n, $ada, $ada ? 'terdaftar' : 'Belum ada di routes/web.php');
        }
    }

    private function cekModel(): void
    {
        $model = ['User', 'SesiPengguna', 'Location', 'Category', 'LostReport',
            'FoundReport', 'ReportMatch', 'Notification', 'HandoverEvidence', 'Room'];

        foreach ($model as $m) {
            $fqcn = "App\\Models\\{$m}";
            $this->catat('CD 3.4', "Model {$m}", class_exists($fqcn),
                class_exists($fqcn) ? 'app/Models/'.$m.'.php' : 'Model belum ada');
        }

        $relasi = [
            ['ReportMatch', 'lostReport'], ['ReportMatch', 'foundReport'], ['ReportMatch', 'evidence'],
            ['FoundReport', 'receiver'], ['Location', 'rooms'], ['User', 'notifications'],
            ['LostReport', 'reporter'], ['HandoverEvidence', 'security'],
        ];
        foreach ($relasi as [$kelas, $method]) {
            $fqcn = "App\\Models\\{$kelas}";
            $ada = class_exists($fqcn) && method_exists($fqcn, $method);
            $this->catat('Relasi PDM 3.5', "{$kelas}::{$method}", $ada, $ada ? 'terdefinisi' : 'Relasi belum ada');
        }
    }

    private function cekPendukung(): void
    {
        $alias = app('router')->getMiddleware();
        foreach (['role' => 'Otorisasi peran (SR-06)', 'sesi' => 'Sesi kedaluwarsa (Session.isActive)'] as $k => $ket) {
            $this->catat('Pendukung', "Middleware {$k}", array_key_exists($k, $alias), $ket);
        }

        $this->catat('NFR / abuse case', 'Rate limiter login',
            RateLimiter::limiter('login') !== null, 'Mitigasi brute-force (Modul 1 §1.5.d)');

        $this->catat('Privasi bukti', "Disk local 'serve' = false",
            config('filesystems.disks.local.serve') === false,
            'Foto KTM tidak boleh dilayani otomatis — salin config/filesystems.php');

        $this->catat('Pendukung', 'Perintah findkom:prune-sesi',
            array_key_exists('findkom:prune-sesi', Artisan::all()), 'Pembersihan sesi kedaluwarsa');

        foreach (['img/logo.svg', 'img/placeholder.svg', 'img/items/wallet.svg', 'img/evidence/ktm-card.svg'] as $a) {
            $this->catat('Aset UI', $a, file_exists(public_path($a)),
                file_exists(public_path($a)) ? 'ada' : 'Salin folder public/img dari starter');
        }

        // Pemeriksaan lingkungan (tidak menggagalkan audit kode)
        $this->catat('Lingkungan', 'Symlink storage:link', file_exists(public_path('storage')),
            file_exists(public_path('storage')) ? 'sudah dibuat' : 'Jalankan php artisan storage:link', 'info');

        $this->catat('Lingkungan', 'APP_KEY terisi', (string) config('app.key') !== '',
            config('app.key') ? 'sudah' : 'Jalankan php artisan key:generate', 'info');

        try {
            $adaData = Schema::hasTable('User') && DB::table('User')->count() > 0;
            $this->catat('Lingkungan', 'Data demo (seeder)', $adaData,
                $adaData ? 'sudah terisi' : 'Jalankan php artisan migrate --seed', 'info');
        } catch (Throwable $e) {
            $this->catat('Lingkungan', 'Data demo (seeder)', false, 'Database belum siap', 'info');
        }
    }
}