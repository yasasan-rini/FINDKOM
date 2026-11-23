@extends('layouts.app')
@section('judul', 'Semua Laporan')
@section('konten')
<div class="hero reveal"><div><h1>Semua Laporan</h1><p class="mut">Arsip lengkap kehilangan &amp; penemuan — klik baris (atau Enter) untuk detail.</p></div>
  <a class="btn" href="{{ route('laporan.export', request()->query()) }}">⬇ Ekspor CSV ({{ $laporan->total() }} baris sesuai filter)</a></div>
<form class="toolbar reveal" method="GET" action="{{ route('laporan.semua') }}">
  <div class="fl wide"><label>Cari</label>
    <div class="searchwrap"><input name="q" value="{{ request('q') }}" placeholder="kode / deskripsi / pelapor…" data-live></div></div>
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
  <div class="fl"><label>Jenis</label><select name="tipe" data-auto>
    <option value="">Semua jenis</option>
    <option value="LOST" @selected(request('tipe') === 'LOST')>Kehilangan</option>
    <option value="FOUND" @selected(request('tipe') === 'FOUND')>Penemuan</option></select></div>
  <div class="fl"><label>Status</label><select name="status" data-auto>
    <option value="">Semua status</option>
    @foreach (\App\Support\ReportStatus::LABEL as $kode => $nama)
      <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $nama }}</option>@endforeach</select></div>
  <div class="fl"><label>Urutkan</label><select name="sort" data-auto>
    <option value="new">Terbaru</option><option value="old" @selected(request('sort') === 'old')>Terlama</option>
    <option value="code" @selected(request('sort') === 'code')>Kode A→Z</option></select></div>
  <a class="btn gh sm" href="{{ route('laporan.semua') }}">↺ Reset</a>
</form>
<div class="tblwrap reveal">
  <table><thead><tr><th>Kode</th><th>Jenis</th><th>Barang</th><th>Gedung / Ruangan</th><th>Tanggal</th><th>Pelapor</th><th>Status</th></tr></thead><tbody>
    @forelse ($laporan as $l)
      <tr class="cl" onclick="location='{{ route('laporan.detail', ['type' => strtolower($l->tipe), 'code' => $l->reportCode]) }}'">
        <td class="mono">{{ $l->reportCode }}</td>
        <td><span class="ty {{ $l->tipe === 'FOUND' ? 'found' : '' }}">{{ $l->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</span></td>
        <td><b>{{ $l->category->name }}</b><div class="tiny faint" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $l->description }}</div></td>
        <td>{{ $l->location->name }}<div class="tiny faint">{{ $l->floorRoom }}</div></td>
        <td>{{ \Illuminate\Support\Carbon::parse($l->eventDate)->format('d/m/Y') }}<div class="tiny faint">{{ $l->eventTime }}</div></td>
        <td>{{ $l->reporter?->namaLengkap ?? $l->reporterName }}</td>
        <td><span class="bd {{ $l->badgeClass }}">{{ $l->labelStatus }}</span></td></tr>
    @empty <tr><td colspan="7"><div class="empty" style="border:0">Tidak ada laporan yang cocok.</div></td></tr> @endforelse
  </tbody></table>
</div>
{{ $laporan->withQueryString()->links('pagination') }}
@endsection