@extends('layouts.app')
@section('judul', 'Master Data')
@section('konten')
<h1 class="reveal">Master Data</h1>
<p class="sub reveal">Kelola pilihan <b>gedung, lantai/ruangan, dan kategori</b> pada form pelaporan &amp; filter pencarian (SCF-SRS-10/12). Data yang masih dipakai laporan tidak dapat dihapus.</p>
<div class="tabs reveal">
  <a href="{{ route('master.index', ['jenis' => 'gedung']) }}" class="{{ $jenis === 'gedung' ? 'on' : '' }}">📍 Gedung ({{ $jumlah['gedung'] }})</a>
  <a href="{{ route('master.index', ['jenis' => 'ruangan']) }}" class="{{ $jenis === 'ruangan' ? 'on' : '' }}">🚪 Lantai/Ruangan ({{ $jumlah['ruangan'] }})</a>
  <a href="{{ route('master.index', ['jenis' => 'kategori']) }}" class="{{ $jenis === 'kategori' ? 'on' : '' }}">🏷 Kategori ({{ $jumlah['kategori'] }})</a>
</div>

<form class="card reveal" style="max-width:760px;padding:16px 18px" method="POST" action="{{ route('master.tambah') }}">
  @csrf<input type="hidden" name="jenis" value="{{ $jenis }}">
  <div class="row" style="flex-wrap:nowrap;align-items:flex-start">
    @if ($jenis === 'ruangan')
      <div class="fl" style="flex:1;margin:0">
        <select name="locationId" required>
          <option value="">— Gedung induk —</option>
          @foreach ($daftarGedung as $g)<option value="{{ $g->id }}" @selected(old('locationId') == $g->id)>{{ $g->name }}</option>@endforeach
        </select>
        @error('locationId')<small class="er">{{ $message }}</small>@enderror
      </div>
    @endif
    <div class="fl" style="flex:1.4;margin:0">
      <input name="name" maxlength="50" required value="{{ old('name') }}"
             placeholder="{{ $jenis === 'ruangan' ? 'cth. Lantai 2, Ruang F2.1' : ($jenis === 'gedung' ? 'cth. Gedung H' : 'cth. Powerbank') }}">
      @error('name')<small class="er">{{ $message }}</small>@enderror
    </div>
    <button class="btn pr">+ Tambah</button>
  </div>
</form>

<div class="tblwrap reveal" style="margin-top:16px;max-width:960px">
  <table style="min-width:600px">
    <thead><tr>
      <th style="width:44px">No</th>
      @if ($jenis === 'ruangan')<th style="width:180px">Gedung</th>@endif
      <th>Nama</th><th style="width:110px">Dipakai</th><th style="width:330px">Aksi</th>
    </tr></thead>
    <tbody>
      @forelse ($data as $d)
        <tr>
          <td class="mut">{{ $loop->iteration }}</td>
          @if ($jenis === 'ruangan')<td><b>{{ $d->location?->name ?? '—' }}</b></td>@endif
          <td><b>{{ $d->name }}</b></td>
          <td><span class="usage">{{ $d->usage }} laporan</span></td>
          <td>
            <div class="row" style="gap:6px;flex-wrap:nowrap">
              <form method="POST" action="{{ route('master.ubah', $d->id) }}" class="row" style="gap:6px;flex-wrap:nowrap;flex:1">
                @csrf @method('PUT')<input type="hidden" name="jenis" value="{{ $jenis }}">
                @if ($jenis === 'ruangan')
                  <select class="tin" name="locationId" required style="padding:6px 10px;font-size:13px;max-width:130px">
                    @foreach ($daftarGedung as $g)<option value="{{ $g->id }}" @selected($d->locationId == $g->id)>{{ $g->name }}</option>@endforeach
                  </select>
                @endif
                <input class="tin" name="name" value="{{ $d->name }}" maxlength="50" required style="padding:6px 10px;font-size:13px">
                <button class="btn xs">Simpan</button>
              </form>
              <form method="POST" action="{{ route('master.hapus', $d->id) }}"
                    data-confirm="Hapus {{ $d->name }}? {{ $d->usage > 0 ? 'Masih dipakai '.$d->usage.' laporan — sistem akan menolak.' : 'Data tidak dipakai laporan mana pun.' }}">
                @csrf @method('DELETE')<input type="hidden" name="jenis" value="{{ $jenis }}">
                <button class="btn xs dno">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ $jenis === 'ruangan' ? 5 : 4 }}"><div class="empty" style="border:0">Belum ada data.</div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection