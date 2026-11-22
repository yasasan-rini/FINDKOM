{{-- [AR] IM02 · UI-04 / UI-004 Form Lapor Penemuan — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Lapor Penemuan')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — AR</b>
  Artefak: AR-IM02 · UI-04 / UI-004 · PC004 · SD-04<br>
  File: <span class="mono">resources/views/lapor/penemuan.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: gedung, lantai/ruang, tanggal, jam, kategori, deskripsi,
    unggah <b>foto barang (WAJIB)</b>, teks panduan titip ke Pos Security,
    tombol Kirim → <span class="mono">route('penemuan.buat')</span>.<br>
    Variabel: <span class="mono">$daftarGedung, $daftarKategori, $daftarRuangan, $laporan</span>.
    Partial tersedia: <span class="mono">lapor._tab, lapor._fields, lapor._sukses</span>.
  </div>
</div>
@endsection