@extends('layouts.app')
@section('judul', 'Masuk')
@section('full')
@php
  $peran = old('peran', 'CIVITAS');
  $kembali = \App\Models\ReportMatch::where('status', 'CLOSED')->count();
  $aktif = \App\Models\LostReport::where('status', '!=', 'CLOSED')->count() + \App\Models\FoundReport::where('status', '!=', 'CLOSED')->count();
  $kandidat = \App\Models\ReportMatch::where('status', 'MATCH_CANDIDATE')->count();
@endphp
<div class="auth">
  <section class="auth-brand">
    <div class="mesh"><i class="m1"></i><i class="m2"></i><i class="m3"></i></div>
    <div class="logo"><img src="{{ asset('img/logo.svg') }}" alt=""><b>FINDKOM</b>
      <span style="font-size:10px;font-weight:800;letter-spacing:.14em;background:rgba(127,209,192,.18);border:1px solid rgba(127,209,192,.4);color:var(--mint);padding:3px 10px;border-radius:99px">FILKOM UB</span></div>
    <h1>Hilang di kampus,<br><em>kembali di tangan.</em></h1>
    <p class="lead">Sistem pelaporan kehilangan &amp; penemuan barang Fakultas Ilmu Komputer Universitas Brawijaya. Lapor, cocokkan otomatis, verifikasi, dan ambil kembali barangmu — semua tercatat rapi.</p>
    <div class="auth-stats">
      <div class="astat"><b>{{ $kembali }}</b><span>Barang kembali</span></div>
      <div class="astat"><b>{{ $aktif }}</b><span>Laporan aktif</span></div>
      <div class="astat"><b>{{ $kandidat }}</b><span>Kandidat cocok</span></div>
    </div>
    <div class="ticker"><span class="dot"></span><span>Pos Security Gedung F • buka 07.00–21.00 WIB • bawa KTM saat pengambilan</span></div>
    <div class="float-card fc1"><img src="{{ asset('img/items/wallet.svg') }}" alt=""><div><b>Dompet hitam</b><span style="color:var(--green2)">Kembali ✓</span></div></div>
    <div class="float-card fc2"><img src="{{ asset('img/items/keys.svg') }}" alt=""><div><b>Kunci motor</b><span style="color:#8A6100">Dicocokkan…</span></div></div>
    <div class="float-card fc3"><img src="{{ asset('img/items/tumbler.svg') }}" alt=""><div><b>Tumbler putih</b><span style="color:#1D5CA0">Menunggu diambil</span></div></div>
  </section>
  <section class="auth-panel"><div class="auth-card">
    <div class="hd"><h2>Masuk ke Findkom</h2><p class="mut small">Sistem lapor kehilangan &amp; penemuan barang FILKOM UB.</p></div>
    <form method="POST" action="{{ route('autentikasi') }}">
      @csrf
      <div class="seg" role="tablist">
        <label><input type="radio" name="peran" value="CIVITAS" @checked($peran === 'CIVITAS')><span>👤 Civitas FILKOM</span></label>
        <label><input type="radio" name="peran" value="SECURITY" @checked($peran === 'SECURITY')><span>🛡 Security</span></label>
      </div>
      @if (session('error'))<div class="autherr">{{ session('error') }}</div>@endif
      <div class="fl"><label for="li-email">Email Institusi</label>
        <input id="li-email" name="email" type="email" required placeholder="nama@student.ub.ac.id"
               value="{{ old('email', session('email')) }}" autocomplete="email">
        @error('email')<small class="er">{{ $message }}</small>@enderror</div>
      <div class="fl"><label for="li-pass">Kata Sandi</label>
        <div class="passwrap"><input id="li-pass" name="password" type="password" required placeholder="••••••••" autocomplete="current-password">
          <button type="button" class="eye" aria-label="Lihat kata sandi">👁</button></div>
        @error('password')<small class="er">{{ $message }}</small>@enderror</div>
      <button class="btn pr w">Masuk →</button>
    </form>
    <div class="divid">COBA CEPAT — AKUN DEMO</div>
    <div class="demo-row">
      <form method="POST" action="{{ route('login.demo', 'CIVITAS') }}">@csrf<button class="btn">👤 Rina · Civitas</button></form>
      <form method="POST" action="{{ route('login.demo', 'SECURITY') }}">@csrf<button class="btn am">🛡 Pak Budi · Security</button></form>
    </div>
    <p class="mut small" style="text-align:center;margin-top:20px">Belum punya akun? <a href="{{ route('registrasi.form') }}"><b>Daftar di sini</b></a></p>
    <p class="tiny faint" style="text-align:center;margin-top:24px">© {{ date('Y') }} FINDKOM — Kelompok 4 RSI, FILKOM Universitas Brawijaya</p>
  </div></section>
</div>
@endsection