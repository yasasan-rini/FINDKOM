{{-- [AR] IM01 · UI-02 / UI-002 Gerbang Masuk & Pilih Peran — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Masuk')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — AR</b>
  Artefak: AR-IM01 · UI-02 / UI-002 · PC002 · SD-02<br>
  File: <span class="mono">resources/views/auth/login.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: tab pilih peran <b>Civitas FILKOM / Security</b> (radio name="peran"),
    input <b>email</b> &amp; <b>password</b>, tombol Masuk →
    <span class="mono">route('autentikasi')</span> (POST + @csrf),
    pesan kesalahan, tautan ke <span class="mono">route('registrasi.form')</span>.
  </div>
</div>
@endsection