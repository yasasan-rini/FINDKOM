<?php

use App\Http\Controllers\HandoverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKehilanganController;
use App\Http\Controllers\LaporanPenemuanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegistrasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check()
    ? (auth()->user()->role === 'SECURITY' ? 'dashboard' : 'beranda') : 'login'));

/* ---- Guest: UI-01 / UI-02 ---- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'bukaHalamanLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'autentikasi'])->middleware('throttle:login')->name('autentikasi');
    Route::post('/login/demo/{role}', [LoginController::class, 'demo'])->middleware('throttle:login')->name('login.demo');
    Route::get('/registrasi', [RegistrasiController::class, 'form'])->name('registrasi.form');
    Route::post('/registrasi', [RegistrasiController::class, 'registrasi'])->middleware('throttle:6,1')->name('registrasi');
});

Route::post('/logout', [LoginController::class, 'keluar'])->middleware('auth')->name('logout');

/* ---- Civitas FILKOM ---- */
Route::middleware(['auth', 'sesi', 'role:CIVITAS'])->group(function () {
    Route::get('/beranda', [HomeController::class, 'beranda'])->name('beranda');
    Route::get('/lapor', fn (Request $r) => redirect()->route(
        $r->query('type') === 'found' ? 'penemuan.form' : 'kehilangan.form'))->name('laporan.buat');
    Route::get('/lapor/kehilangan', [LaporanKehilanganController::class, 'form'])->name('kehilangan.form');
    Route::post('/lapor/kehilangan', [LaporanKehilanganController::class, 'buatLaporan'])->name('kehilangan.buat');
    Route::get('/lapor/penemuan', [LaporanPenemuanController::class, 'bukaFormPenemuan'])->name('penemuan.form');
    Route::post('/lapor/penemuan', [LaporanPenemuanController::class, 'buatLaporan'])->name('penemuan.buat');
    Route::get('/status', [LaporanController::class, 'status'])->name('laporan.status');
    Route::get('/cari', [LaporanController::class, 'cari'])->name('laporan.cari');
});

/* ---- Security FILKOM ---- */
Route::middleware(['auth', 'sesi', 'role:SECURITY'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/kandidat', [MatchController::class, 'index'])->name('match.index');
    Route::get('/kandidat/{id}', [MatchController::class, 'detail'])->name('match.detail');
    Route::post('/kandidat/{id}/cocok', [MatchController::class, 'cocok'])->name('match.cocok');
    Route::post('/kandidat/{id}/tolak', [MatchController::class, 'tolak'])->name('match.tolak');
    Route::post('/kandidat/{id}/revisi', [MatchController::class, 'revisi'])->name('match.revisi');
    Route::get('/penerimaan', [ReceiptController::class, 'index'])->name('receipt.index');
    Route::post('/penerimaan/{id}', [ReceiptController::class, 'konfirmasi'])->name('receipt.konfirmasi');
    Route::get('/serah-terima', [HandoverController::class, 'index'])->name('handover.index');
    Route::get('/serah-terima/{id}', [HandoverController::class, 'form'])->name('handover.form');
    Route::post('/serah-terima/{id}/bukti', [HandoverController::class, 'bukti'])->name('handover.bukti');
    Route::post('/serah-terima/{id}/selesai', [HandoverController::class, 'selesai'])->name('handover.selesai');
    Route::post('/serah-terima/{id}/tunda', [HandoverController::class, 'tunda'])->name('handover.tunda');
    Route::get('/master', [MasterDataController::class, 'index'])->name('master.index');
    Route::post('/master', [MasterDataController::class, 'tambah'])->name('master.tambah');
    Route::put('/master/{id}', [MasterDataController::class, 'ubah'])->name('master.ubah');
    Route::delete('/master/{id}', [MasterDataController::class, 'hapus'])->name('master.hapus');
    Route::get('/semua-laporan', [LaporanController::class, 'semua'])->name('laporan.semua');
    Route::get('/semua-laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
});

/* ---- Bersama (auth): otorisasi dicek di controller ---- */
Route::middleware(['auth', 'sesi'])->group(function () {
    Route::get('/laporan/{type}/{code}', [LaporanController::class, 'detail'])->name('laporan.detail');
    Route::get('/laporan/{type}/{id}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{type}/{id}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{type}/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::get('/serah-terima/bukti/{evidence}/{jenis}', [HandoverController::class, 'foto'])->name('handover.foto');
    Route::get('/notifikasi/{id}', [NotificationController::class, 'baca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'bacaSemua'])->name('notifikasi.bacaSemua');
    // Pembaruan langsung (PKB-SRS-04 / PPB-SRS-09) — polling ringan tiap 25 detik
    Route::get('/api/status-ringkas', [NotificationController::class, 'ringkas'])
        ->middleware('throttle:60,1')->name('api.ringkas');
});