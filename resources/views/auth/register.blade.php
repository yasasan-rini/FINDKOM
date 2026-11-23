@extends('layouts.app')
@section('judul', 'Registrasi Akun')
@section('full')
<div class="auth">
  <section class="auth-brand">
    <div class="mesh"><i class="m1"></i><i class="m2"></i><i class="m3"></i></div>
    <div class="logo"><img src="{{ asset('img/logo.svg') }}" alt=""><b>FINDKOM</b></div>
    <h1>Satu akun untuk<br><em>lapor &amp; pantau.</em></h1>
    <p class="lead">Khusus civitas FILKOM UB — mahasiswa dan karyawan dengan email institusi. Setelah terdaftar, kamu bisa melaporkan kehilangan, melaporkan penemuan, dan memantau status barang tanpa bolak-balik ke pos.</p>
    <div class="ticker"><span class="dot"></span><span>Verifikasi otomatis lewat email @student.ub.ac.id / @ub.ac.id</span></div>
  </section>
  <section class="auth-panel"><div class="auth-card">
    <div class="hd"><h2>Buat Akun Findkom</h2><p class="mut small">Gunakan email institusi UB.</p></div>
    <form method="POST" action="{{ route('registrasi') }}" novalidate>
      @csrf
      @foreach ([
        ['namaLengkap', 'Nama Lengkap', 'text', 'cth. Rina Wulandari'],
        ['nim', 'NIM / NIP', 'text', '10–20 digit angka'],
        ['email', 'Email Institusi UB', 'email', 'nama@student.ub.ac.id'],
        ['username', 'Username', 'text', 'min. 4 karakter'],
        ['kontak', 'Kontak (WhatsApp)', 'tel', '08xxxxxxxxxx'],
      ] as [$nama, $label, $tipe, $ph])
        <div class="fl"><label for="{{ $nama }}">{{ $label }}</label>
          <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" value="{{ old($nama) }}" placeholder="{{ $ph }}" required>
          @error($nama)<small class="er">{{ $message }}</small>@enderror</div>
      @endforeach
      <div class="fl"><label for="password">Kata Sandi (min. 8 karakter)</label>
        <div class="passwrap"><input id="password" name="password" type="password" required placeholder="••••••••">
          <button type="button" class="eye">👁</button></div>
        @error('password')<small class="er">{{ $message }}</small>@enderror</div>
      <button class="btn pr w">Daftar Sekarang →</button>
    </form>
    <p class="mut small" style="text-align:center;margin-top:18px">Sudah punya akun? <a href="{{ route('login') }}"><b>Masuk</b></a></p>
  </div></section>
</div>
@endsection