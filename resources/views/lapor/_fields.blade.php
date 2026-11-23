@php
  $dName = $type === 'LOST' ? 'lostDate' : 'foundDate';
  $tName = $type === 'LOST' ? 'lostTime' : 'foundTime';
  $today = now()->toDateString(); $tnow = now()->format('H:i');
  $ruanganJson = ($daftarRuangan ?? collect())->map(fn ($r) => ['l' => $r->locationId, 'n' => $r->name])->values();
@endphp
<div class="fl"><label>Pelapor</label>
  <div class="row" style="background:var(--soft);border-radius:12px;padding:9px 14px">
    <span class="ava" style="width:28px;height:28px;font-size:11px">{{ auth()->user()->initials }}</span>
    <b>{{ auth()->user()->namaLengkap }}</b><span class="mut small mono">{{ auth()->user()->nim }}</span></div></div>
<div class="g2">
  <div class="fl"><label for="locationId">Gedung {{ $type === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</label>
    <select id="locationId" name="locationId" required>
      <option value="">— Pilih gedung —</option>
      @foreach ($daftarGedung as $g)<option value="{{ $g->id }}" @selected(old('locationId', $laporan?->locationId) == $g->id)>{{ $g->name }}</option>@endforeach
    </select>@error('locationId')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="floorRoom">Lantai / Ruangan</label>
    <input id="floorRoom" name="floorRoom" maxlength="50" required list="room-list"
           data-rooms='@json($ruanganJson)' placeholder="cth. Lantai 2, Ruang F2.1"
           value="{{ old('floorRoom', $laporan?->floorRoom) }}">
    <datalist id="room-list"></datalist>
    <div class="hint">Pilih dari daftar ruangan gedung terpilih, atau ketik manual bila belum terdaftar.</div>
    @error('floorRoom')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="{{ $dName }}">Tanggal {{ $type === 'LOST' ? 'Kehilangan' : 'Ditemukan' }}</label>
    <input id="{{ $dName }}" name="{{ $dName }}" type="date" max="{{ $today }}" required
           value="{{ old($dName, $laporan?->$dName ?? $today) }}">
    @error($dName)<small class="er">{{ $message }}</small>@enderror</div>
  <div class="fl"><label for="{{ $tName }}">{{ $type === 'LOST' ? 'Perkiraan Waktu' : 'Waktu Ditemukan' }}</label>
    <input id="{{ $tName }}" name="{{ $tName }}" type="time" required
           value="{{ old($tName, $laporan ? substr($laporan->$tName, 0, 5) : $tnow) }}">
    @error($tName)<small class="er">{{ $message }}</small>@enderror</div>
</div>
<div class="fl"><label for="categoryId">Kategori Barang</label>
  <select id="categoryId" name="categoryId" required>
    <option value="">— Pilih kategori —</option>
    @foreach ($daftarKategori as $k)<option value="{{ $k->id }}" @selected(old('categoryId', $laporan?->categoryId) == $k->id)>{{ $k->name }}</option>@endforeach
  </select>@error('categoryId')<small class="er">{{ $message }}</small>@enderror</div>
<div class="fl"><label for="description">Deskripsi Barang (min. 5 karakter)</label>
  <textarea id="description" name="description" rows="4" maxlength="600" minlength="5" required data-count="cc"
    placeholder="Sebutkan warna, merek, ciri khas, isi, dan detail lain yang membedakan barang ini…">{{ old('description', $laporan?->description) }}</textarea>
  <div class="charcount"><span id="cc">0</span>/600</div>
  @error('description')<small class="er">{{ $message }}</small>@enderror</div>

@push('skrip')
<script>
(function () {
  const fr = document.getElementById('floorRoom'),
        loc = document.getElementById('locationId'),
        dl = document.getElementById('room-list');
  if (!fr || !loc || !dl) return;
  const rooms = JSON.parse(fr.dataset.rooms || '[]');
  const isi = () => {
    dl.innerHTML = '';
    rooms.filter(r => r.l === loc.value).forEach(r => {
      const o = document.createElement('option'); o.value = r.n; dl.appendChild(o);
    });
  };
  loc.addEventListener('change', isi);
  isi();
})();
</script>
@endpush