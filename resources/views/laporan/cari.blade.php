{{-- [AR] IM03 · UI-10 Cari Barang — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Cari Barang')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — AR</b>
  Artefak: AR-IM03 · UI-10 · UR-SCF-01 · SR-03<br>
  File: <span class="mono">resources/views/laporan/cari.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: form GET ke <span class="mono">route('laporan.cari')</span> berisi
    kata kunci, dropdown gedung, ruangan, kategori, jenis, rentang waktu, tanggal, urutan;
    jumlah hasil; grid kartu laporan (partial <span class="mono">laporan._rcard</span>);
    empty state; pagination <span class="mono">$hasil->withQueryString()->links('pagination')</span>.<br>
    Variabel: <span class="mono">$hasil, $daftarGedung, $daftarKategori, $daftarRuangan</span>.
  </div>
</div>
@endsection