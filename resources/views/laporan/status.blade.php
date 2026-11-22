{{-- [BK] IM03 · UI-09 Status & Riwayat — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Status & Riwayat')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — BK</b>
  Artefak: BK-IM03 · UI-09 · UR-PKB-04 · UR-PPB-04<br>
  File: <span class="mono">resources/views/laporan/status.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: daftar laporan milik sendiri (kode, jenis, kategori, gedung),
    <b>linimasa 5 status</b> (pakai partial <span class="mono">laporan._timeline</span>),
    lencana status, filter Semua/Aktif/Menunggu Diambil/Selesai,
    tombol Detail / Ubah / Hapus, dan bagian <b>Notifikasi</b>.<br>
    Variabel: <span class="mono">$laporan, $filter, $counts, $notifikasi</span>.
  </div>
</div>
@endsection