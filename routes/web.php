<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\LaporanKehilanganController;
use App\Http\Controllers\LaporanPenemuanController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\HandoverController;

// --- AUTH ---
Route::get('/login', [LoginController::class, 'bukaHalamanLogin'])->name('login');
Route::post('/login', [LoginController::class, 'autentikasi'])->name('autentikasi');
Route::post('/logout', [LoginController::class, 'keluar'])->name('logout');

Route::get('/registrasi', function() { return view('auth.registrasi'); })->name('registrasi.form');
Route::post('/registrasi', [RegistrasiController::class, 'registrasi'])->name('registrasi');

// --- DASHBOARD ---
Route::get('/', function() { return view('civitas.beranda'); })->name('beranda'); // Nanti AR/BK isi
Route::get('/dashboard', function() { return view('security.dashboard'); })->name('dashboard'); // Nanti YN isi

// --- MATCH (TUGAS YN) ---
Route::get('/match', [MatchController::class, 'requestKandidatSiapVerifikasi'])->name('match.index');
Route::get('/match/{matchId}', [MatchController::class, 'requestDetailKandidat'])->name('match.detail');
Route::post('/match/{matchId}/verify', [MatchController::class, 'verifikasiCocok'])->name('match.cocok');
Route::post('/match/{matchId}/reject', [MatchController::class, 'tolakKandidat'])->name('match.tolak');

// --- HANDOVER (TUGAS VP) ---
Route::get('/handover', [HandoverController::class, 'requestDaftarTerverifikasi'])->name('handover.index');
Route::get('/handover/{matchId}', [HandoverController::class, 'getDetailSerahTerima'])->name('handover.form');
Route::post('/handover/{matchId}/bukti', [HandoverController::class, 'submitBukti'])->name('handover.bukti');
Route::post('/handover/{matchId}/close', [HandoverController::class, 'selesaikanLaporan'])->name('handover.selesai');

// --- LAPORAN (TUGAS BK & AR) ---
Route::get('/lapor', function() { return view('lapor.kehilangan'); })->name('laporan.buat'); // Nanti BK isi
Route::post('/lapor-hilang', [LaporanKehilanganController::class, 'buatLaporan'])->name('kehilangan.buat');
Route::get('/status', function() { return view('laporan.status'); })->name('laporan.status'); // Nanti BK isi
Route::get('/cari', function() { return view('laporan.cari'); })->name('laporan.cari'); // Nanti AR isi
Route::get('/semua', function() { return view('laporan.semua'); })->name('laporan.semua'); // Nanti VP isi
Route::get('/laporan/{code}', function($code) { return view('laporan.detail'); })->name('laporan.detail');

// --- MASTER DATA (TUGAS VP) ---
Route::get('/master', function() { return view('master.index'); })->name('master.index');
Route::post('/master', function() {})->name('master.tambah');
Route::put('/master/{id}', function() {})->name('master.ubah');
Route::delete('/master/{id}', function() {})->name('master.hapus');