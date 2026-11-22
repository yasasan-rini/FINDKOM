{{-- [BK] IM02 · UI-03 / UI-003 Form Lapor Kehilangan — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Lapor Kehilangan')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — BK</b>
  Artefak: BK-IM02 · UI-03 / UI-003 · PC003 · SD-03<br>
  File: <span class="mono">resources/views/lapor/kehilangan.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: dropdown <b>gedung</b>, input <b>lantai/ruang</b>, <b>tanggal</b> (max hari ini),
    <b>jam</b>, dropdown <b>kategori</b>, textarea <b>deskripsi</b> (min 5),
    unggah <b>foto referensi (opsional)</b>, tombol Kirim →
    <span class="mono">route('kehilangan.buat')</span> (POST + @csrf + enctype).<br>
    Variabel dari controller: <span class="mono">$daftarGedung, $daftarKategori, $daftarRuangan, $laporan</span>.
    Boleh pakai partial <span class="mono">lapor._tab</span> dan <span class="mono">lapor._fields</span>.
  </div>
</div>
@endsection