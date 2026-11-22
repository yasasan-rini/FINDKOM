{{-- [BK+AR] Partial field bersama form laporan — KERANGKA MINIMAL.
     TODO BK & AR: lengkapi validasi tampilan, pratinjau foto, penghitung karakter,
     dan datalist ruangan per gedung.
     Variabel: $type ('LOST'|'FOUND'), $laporan (nullable),
               $daftarGedung, $daftarKategori, $daftarRuangan. --}}
@php
  $dName = $type === 'LOST' ? 'lostDate' : 'foundDate';
  $tName = $type === 'LOST' ? 'lostTime' : 'foundTime';
@endphp
<div class="g2">
  <div class="fl"><label for="locationId">Gedung</label>
    <select id="locationId" name="locationId" required>
      <option value="">— Pilih gedung —</option>
      @foreach ($daftarGedung as $g)
        <option value="{{ $g->id }}" @selected(old('locationId', $laporan?->locationId) == $g->id)>{{ $g->name }}</option>
      @endforeach
    </select>@error('locationId')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="floorRoom">Lantai / Ruangan</label>
    <input id="floorRoom" name="floorRoom" maxlength="50" required value="{{ old('floorRoom', $laporan?->floorRoom) }}">
    @error('floorRoom')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="{{ $dName }}">Tanggal</label>
    <input id="{{ $dName }}" name="{{ $dName }}" type="date" max="{{ now()->toDateString() }}" required
           value="{{ old($dName, $laporan?->$dName ?? now()->toDateString()) }}">
    @error($dName)<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="{{ $tName }}">Waktu</label>
    <input id="{{ $tName }}" name="{{ $tName }}" type="time" required
           value="{{ old($tName, $laporan ? substr($laporan->$tName, 0, 5) : now()->format('H:i')) }}">
    @error($tName)<small class="er">{{ $message }}</small>@enderror</div>
</div>
<div class="fl"><label for="categoryId">Kategori Barang</label>
  <select id="categoryId" name="categoryId" required>
    <option value="">— Pilih kategori —</option>
    @foreach ($daftarKategori as $k)
      <option value="{{ $k->id }}" @selected(old('categoryId', $laporan?->categoryId) == $k->id)>{{ $k->name }}</option>
    @endforeach
  </select>@error('categoryId')<small class="er">{{ $message }}</small>@enderror</div>
<div class="fl"><label for="description">Deskripsi Barang (min. 5 karakter)</label>
  <textarea id="description" name="description" rows="4" maxlength="600" required>{{ old('description', $laporan?->description) }}</textarea>
  @error('description')<small class="er">{{ $message }}</small>@enderror</div>