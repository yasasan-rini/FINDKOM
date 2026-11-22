{{-- [VP] IM03 · UI-12 Semua Laporan — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Semua Laporan')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — VP</b>
  Artefak: VP-IM03 · UI-12<br>
  File: <span class="mono">resources/views/laporan/semua.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: filter (kata kunci, gedung, ruangan, jenis, status, urutan),
    tabel semua laporan (kode, jenis, barang, gedung/ruang, tanggal, pelapor, status),
    baris dapat diklik menuju detail, pagination, dan tombol Ekspor CSV
    yang membawa filter aktif → <span class="mono">route('laporan.export', request()->query())</span>.<br>
    Variabel: <span class="mono">$laporan, $daftarGedung, $daftarRuangan</span>.
  </div>
</div>
@endsection