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
  <div class="fl"><label for="floorRoomSelect">Lantai / Ruangan</label>
    <select id="floorRoomSelect" required data-rooms='@json($ruanganJson)' data-selected="{{ old('floorRoom', $laporan?->floorRoom) }}">
      <option value="">— Pilih gedung terlebih dahulu —</option>
    </select>
    <input id="floorRoom" name="floorRoom" maxlength="50" required
           value="{{ old('floorRoom', $laporan?->floorRoom) }}"
           placeholder="cth. Lantai 2, Ruang F2.1"
           style="display:none;margin-top:8px">
    <div class="hint">Pilih ruangan sesuai gedung yang dipilih.</div>
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
  const sel = document.getElementById('floorRoomSelect'),
        inp = document.getElementById('floorRoom'),
        loc = document.getElementById('locationId');
  if (!sel || !inp || !loc) return;

  const rooms = JSON.parse(sel.dataset.rooms || '[]');
  let initialVal = sel.dataset.selected || '';

  const isi = () => {
    const gedungId = String(loc.value || '');
    sel.innerHTML = '';
    inp.style.display = 'none';

    if (!gedungId) {
      sel.innerHTML = '<option value="">— Pilih gedung terlebih dahulu —</option>';
      inp.value = '';
      return;
    }

    const optDefault = document.createElement('option');
    optDefault.value = '';
    optDefault.textContent = '— Pilih lantai / ruangan —';
    sel.appendChild(optDefault);

    const daftar = rooms.filter(r => String(r.l) === gedungId);
    daftar.forEach(r => {
      const o = document.createElement('option');
      o.value = r.n;
      o.textContent = r.n;
      sel.appendChild(o);
    });

    const optManual = document.createElement('option');
    optManual.value = '__manual__';
    optManual.textContent = 'Lainnya (ketik manual)...';
    sel.appendChild(optManual);

    if (initialVal) {
      if (daftar.some(r => r.n === initialVal)) {
        sel.value = initialVal;
        inp.value = initialVal;
      } else {
        sel.value = '__manual__';
        inp.value = initialVal;
        inp.style.display = 'block';
      }
      initialVal = '';
    } else {
      inp.value = '';
    }
  };

  sel.addEventListener('change', () => {
    if (sel.value === '__manual__') {
      inp.style.display = 'block';
      inp.value = '';
      inp.focus();
    } else {
      inp.style.display = 'none';
      inp.value = sel.value;
    }
  });

  loc.addEventListener('change', isi);
  isi();
})();
</script>
@endpush