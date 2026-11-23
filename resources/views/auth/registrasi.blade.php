{{-- [BK] IM01 · UI-01 / UI-001 Registrasi Akun — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Registrasi Akun')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — BK</b>
  Artefak: BK-IM01 · UI-01 / UI-001 · PC001 · SD-01<br>
  File: <span class="mono">resources/views/auth/register.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: input <b>namaLengkap, nim, email (domain UB), username, kontak, password</b>;
    tombol Daftar → <span class="mono">route('registrasi')</span> (POST + @csrf);
    tautan ke <span class="mono">route('login')</span>;
    pesan hasil &amp; <span class="mono">@error</span> per kolom.
  </div>
</div>
@endsection