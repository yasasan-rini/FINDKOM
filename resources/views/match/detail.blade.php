@extends('layouts.app')
@section('judul', 'Verifikasi '.$match->matchCode)
@section('konten')
@php $s = $match->skor;
  $sameLoc = $lost->locationId === $found->locationId;
  $sameCat = $lost->categoryId === $found->categoryId;
  $sameRoom = strtolower(trim($lost->floorRoom)) === strtolower(trim($found->floorRoom));
  $siap = $found->receivedAt !== null; @endphp
<div class="row reveal" style="margin-bottom:14px">
  <a class="btn gh sm" href="{{ route('match.index') }}">← Daftar kandidat</a>
  <b class="mono" style="font-size:16px">{{ $match->matchCode }}</b>
  <span class="bd {{ $match->badgeClass }}">{{ $match->labelStatus }}</span></div>
<h1 class="reveal">Verifikasi Kecocokan</h1>
<p class="sub reveal">Bandingkan kedua laporan secara manual — deskripsi, foto, lokasi, dan waktu — sebelum memutuskan.</p>
<div class="card reveal">
  <div class="row" style="align-items:center;gap:16px">
    <svg class="ring" width="72" height="72" viewBox="0 0 72 72"><circle class="bgc" cx="36" cy="36" r="30"/>
      <circle class="fgc" cx="36" cy="36" r="30" stroke="{{ $s['total'] >= 90 ? '#2E9E5B' : ($s['total'] >= 75 ? '#2A9D8F' : '#F2B705') }}"
        stroke-dasharray="188.5" stroke-dashoffset="{{ 188.5 * (1 - $s['total'] / 100) }}"/>
      <text x="36" y="38" text-anchor="middle">{{ $s['total'] }}</text><text class="pct" x="36" y="50" text-anchor="middle">COCOK</text></svg>
    <div class="scorebars">
      @foreach (['Kategori' => [$s['kategori'], '#2A9D8F'], 'Lokasi' => [$s['lokasi'], '#2D7DD2'], 'Waktu' => [$s['waktu'], '#F2B705']] as $lbl => [$v, $c])
        <div class="sb"><span>{{ $lbl }}</span><span class="tr"><i style="width:{{ $v }}%;background:{{ $c }}"></i></span><b style="text-align:right">{{ $v }}%</b></div>
      @endforeach
    </div></div>
  @if (! $sameRoom)<p class="tiny mut" style="margin-top:8px">ⓘ Lokasi ruangan berbeda — periksa dengan lebih teliti.</p>@endif
  <div class="cmp">
    @foreach ([['lost', $lost, 'Laporan Kehilangan'], ['found', $found, 'Laporan Penemuan']] as [$cls, $r, $judul])
      <div class="side {{ $cls }}"><div class="sh"><span>{{ $judul }}</span><span class="mono">{{ $r->reportCode }}</span></div>
        <img class="hero" src="{{ $r->photoSrc }}" alt="">
        <div class="kv">
          <div class="k">👤<span><b>{{ $r->reporter?->namaLengkap }}</b> <span class="mut tiny">({{ $r->reporter?->nim ?? '—' }} · {{ $r->reporter?->kontak }})</span></span></div>
          <div class="k">📍<span><b>{{ $r->location->name }}</b> · {{ $r->floorRoom }}@if ($cls === 'found' && $sameLoc)<span class="same">✓ gedung sama</span>@endif</span></div>
          <div class="k">📅<span>{{ \Illuminate\Support\Carbon::parse($r->eventDate)->translatedFormat('d M Y') }} · {{ $r->eventTime }}@if ($cls === 'found')<span class="same">selisih {{ $s['selisih'] }} hari</span>@endif</span></div>
          <div class="k">🏷<span>{{ $r->category->name }}@if ($cls === 'found' && $sameCat)<span class="same">✓ kategori sama</span>@endif</span></div>
          <div class="k">📝<span>{{ $r->description }}</span></div>
          @if ($cls === 'found')<div class="k">📦<span>@if ($r->receivedAt)<b class="ok">Fisik diterima di pos</b> · {{ \Illuminate\Support\Carbon::parse($r->receivedAt)->translatedFormat('d M H:i') }}@else<b class="er">Fisik belum diserahkan ke pos</b>@endif</span></div>@endif
        </div></div>
    @endforeach
  </div>
  @if ($match->revisionNote)<div class="notice blue">✉<div><b>Himbauan revisi terakhir:</b> {{ $match->revisionNote }}</div></div>@endif
  @if ($match->status === 'MATCH_CANDIDATE')
    @if (! $siap)
      <div class="notice">📦<div><b>Barang fisik belum diterima di pos.</b> Sesuai UC-07, konfirmasi penerimaan (UC-06) dulu sebelum verifikasi.
        <a href="{{ route('receipt.index', ['id' => $found->id]) }}"><b>Konfirmasi sekarang →</b></a></div></div>
    @endif
    <div class="row" style="justify-content:flex-end;margin-top:14px">
      <form method="POST" action="{{ route('match.tolak', $match->id) }}" data-confirm="Tandai bukan barang yang sama? Kedua laporan dibuka kembali agar bisa dicocokkan ulang.">
        @csrf<button class="btn dno">✕ Bukan Barang yang Sama</button></form>
      <form method="POST" action="{{ route('match.cocok', $match->id) }}" data-confirm="Verifikasi kecocokan ini (skor {{ $s['total'] }}%)? Status menjadi Menunggu Diambil dan pelapor menerima notifikasi.">
        @csrf<button class="btn pr" @disabled(! $siap)>✓ Verifikasi Cocok</button></form>
    </div>
    <details class="rev"><summary>✉ Kirim Himbauan Revisi (data kurang jelas)</summary>
      <form method="POST" action="{{ route('match.revisi', $match->id) }}" style="margin-top:10px">@csrf
        <div class="fl"><label>Catatan untuk pelapor kehilangan</label>
          <textarea name="catatan" rows="2" maxlength="500" required placeholder="cth. Mohon lengkapi ciri khusus dompet atau unggah foto yang lebih jelas…"></textarea>
          @error('catatan')<small class="er">{{ $message }}</small>@enderror</div>
        <button class="btn sm pr">Kirim Himbauan</button></form>
    </details>
  @else
    <p class="mut small" style="margin-top:12px">{{ $match->status === 'NOTIFIED' ? 'Terverifikasi — menunggu diambil pemilik.' : ($match->status === 'CLOSED' ? 'Selesai & terarsip.' : 'Kandidat ditolak.') }}</p>
  @endif
</div>
@endsection