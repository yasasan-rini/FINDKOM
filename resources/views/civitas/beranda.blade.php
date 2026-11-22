{{-- [BK] Beranda Civitas (halaman pendukung, bukan artefak wajib) — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Beranda')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — BK</b>
  Halaman: Beranda Civitas · <span class="mono">HomeController@beranda</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Saran isi: sapaan pengguna, dua kartu aksi (Lapor Kehilangan / Lapor Penemuan),
    ringkasan laporan aktif, dan daftar barang baru ditemukan.<br>
    Variabel: <span class="mono">$aktif, $menunggu, $baruDitemukan</span>.
  </div>
</div>
@endsection