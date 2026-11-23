@extends('layouts.app')
@section('judul', 'Cari Barang')
@section('konten')
<h1 class="reveal">Cari Barang</h1>
<p class="sub reveal">Telusuri laporan berdasarkan kata kunci, gedung, lantai/ruangan, kategori, dan waktu.</p>
<form class="toolbar reveal" method="GET" action="{{ route('laporan.cari') }}">
  <div class="fl wide"><label>Kata kunci</label>
    <div class="searchwrap"><input name="q" value="{{ request('q') }}" placeholder="kode / deskripsi barang…" data-live></div></div>
  <div class="fl"><label>Gedung</label><select name="locationId" data-auto>
    <option value="">Semua gedung</option>
    @foreach ($daftarGedung as $g)<option value="{{ $g->id }}" @selected(request('locationId') == $g->id)>{{ $g->name }}</option>@endforeach</select></div>
  <div class="fl"><label>Lantai / Ruangan</label><select name="room" data-auto>
    <option value="">Semua ruangan</option>
    @foreach ($daftarRuangan->groupBy('locationId') as $locId => $rooms)
      <optgroup label="{{ $rooms->first()->location?->name }}">
        @foreach ($rooms as $r)<option value="{{ $r->id }}" @selected(request('room') == $r->id)>{{ $r->name }}</option>@endforeach
      </optgroup>
    @endforeach</select></div>
  <div class="fl"><label>Kategori</label><select name="categoryId" data-auto>
    <option value="">Semua kategori</option>
    @foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected(request('categoryId') == $k->id)>{{ $k->name }}</option>@endforeach</select></div>
  <div class="fl"><label>Jenis</label><select name="type" data-auto>
    <option value="">Semua jenis</option>
    <option value="LOST" @selected(request('type') === 'LOST')>Kehilangan</option>
    <option value="FOUND" @selected(request('type') === 'FOUND')>Penemuan</option></select></div>
  <div class="fl"><label>Rentang waktu</label><select name="range" data-auto>
    <option value="">Semua waktu</option>
    @foreach ([3, 7, 30] as $r)<option value="{{ $r }}" @selected(request('range') == $r)>{{ $r }} hari terakhir</option>@endforeach</select></div>
  <div class="fl"><label>Tanggal kejadian</label><input type="date" name="tanggal" value="{{ request('tanggal') }}" data-auto></div>
  <div class="fl"><label>Urutkan</label><select name="sort" data-auto>
    <option value="new">Terbaru dibuat</option><option value="old" @selected(request('sort') === 'old')>Terlama dibuat</option>
    <option value="event" @selected(request('sort') === 'event')>Tanggal kejadian ↓</option></select></div>
  <a class="btn gh sm" href="{{ route('laporan.cari') }}">↺ Reset</a>
</form>
<p class="mut small reveal" style="margin-bottom:12px"><b>{{ $hasil->total() }}</b> laporan ditemukan</p>
<div class="rgrid">
  @forelse ($hasil as $r) @include('laporan._rcard', ['r' => $r])
  @empty <div class="empty" style="grid-column:1/-1"><b>Tidak ada laporan yang cocok</b>Coba ubah kata kunci atau longgarkan filter.
    <br><a class="btn sm" style="margin-top:12px" href="{{ route('laporan.cari') }}">↺ Reset filter</a></div> @endforelse
</div>
{{ $hasil->withQueryString()->links('pagination') }}
@endsection