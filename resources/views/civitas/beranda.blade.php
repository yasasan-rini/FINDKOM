@extends('layouts.app')
@section('judul', 'Beranda')
@section('konten')
@php $u = auth()->user(); $jam = now()->hour;
  $salam = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam')); @endphp
<div class="hero reveal"><div>
  <p class="mut small" style="font-weight:700">{{ now()->translatedFormat('l, d F Y') }}</p>
  <h1>{{ $salam }}, <em>{{ explode(' ', $u->namaLengkap)[0] }}</em> 👋</h1>
  <p class="mut">Kehilangan sesuatu? Atau menemukan barang tertinggal? Laporkan di sini.</p>
</div></div>

@if ($menunggu->count())
  <div class="notice reveal">✨<div><b>{{ $menunggu->count() }} barangmu menunggu diambil!</b>
    Datang ke Pos Security Gedung F dengan membawa KTM.
    {{ $menunggu->pluck('reportCode')->join(', ') }} — <a href="{{ route('laporan.status', ['filter' => 'wait']) }}"><b>Lihat detail →</b></a></div></div>
@endif

<div class="action-grid">
  <a class="action-card lost reveal" href="{{ route('kehilangan.form') }}"><i class="deco"></i>
    <span class="ic">🔍</span><h3>Lapor Kehilangan</h3>
    <p>Barangmu hilang di sekitar kampus? Catat ciri, lokasi, dan waktunya agar bisa dicocokkan otomatis.</p><span class="go">→</span></a>
  <a class="action-card found reveal" href="{{ route('penemuan.form') }}"><i class="deco"></i>
    <span class="ic">📦</span><h3>Lapor Penemuan</h3>
    <p>Menemukan barang tertinggal? Foto dan laporkan, lalu titipkan fisiknya ke pos Security.</p><span class="go">→</span></a>
</div>

<h2 class="reveal">Laporan aktifmu <span class="mut small" style="font-family:var(--fb)">({{ $aktif->count() }})</span></h2>
@if ($aktif->count())
  <div class="rgrid reveal">
    @foreach ($aktif->take(4) as $r)
      @include('laporan._rcard', ['r' => $r])
    @endforeach
  </div>
  @if ($aktif->count() > 4)<p style="margin-top:12px"><a href="{{ route('laporan.status') }}" class="btn sm">Lihat semua →</a></p>@endif
@else
  <div class="empty reveal"><b>Belum ada laporan aktif</b>Laporan yang kamu buat akan tampil di sini beserta statusnya.</div>
@endif

<h2 class="reveal">Baru ditemukan di kampus</h2>
<div class="hscroll reveal">
  @forelse ($baruDitemukan as $r) @include('laporan._rcard', ['r' => $r])
  @empty <div class="empty" style="flex:1">Belum ada barang temuan aktif.</div> @endforelse
</div>

<div class="poscard reveal"><span class="pin">📍</span>
  <div style="flex:1;min-width:220px"><b>Pos Security Gedung F — Lobby Utama</b>
    <div class="small" style="color:#A9C8C2">Titipkan &amp; ambil barang setiap hari 07.00–21.00 WIB. Bawa KTM saat pengambilan.</div></div>
  <a class="btn sm am" href="{{ route('laporan.cari') }}">🔎 Cari barang temuan</a></div>
@endsection