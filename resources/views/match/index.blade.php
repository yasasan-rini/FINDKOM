@extends('layouts.app')
@section('judul', 'Kandidat Kecocokan')
@section('konten')
<h1 class="reveal">Kandidat Kecocokan</h1>
<p class="sub reveal">Hasil pencocokan otomatis (gedung + kategori + waktu ±4 hari). Bandingkan deskripsi &amp; foto sebelum memutuskan.</p>
<div class="tabs reveal">
  <a href="{{ route('match.index') }}" class="{{ request('tab') !== 'baru' ? 'on' : '' }}">Semua</a>
  <a href="{{ route('match.index', ['tab' => 'baru']) }}" class="{{ request('tab') === 'baru' ? 'on' : '' }}">✨ Baru (24 jam)</a>
</div>
<form class="toolbar reveal" method="GET" action="{{ route('match.index') }}">
  @if (request('tab'))<input type="hidden" name="tab" value="{{ request('tab') }}">@endif
  <div class="fl"><label>Gedung (laporan kehilangan)</label><select name="locationId" data-auto>
    <option value="">Semua gedung</option>
    @foreach ($daftarGedung as $g)<option value="{{ $g->id }}" @selected(request('locationId') == $g->id)>{{ $g->name }}</option>@endforeach</select></div>
  <div class="fl"><label>Tanggal kejadian</label><input type="date" name="tanggal" value="{{ request('tanggal') }}" data-auto></div>
  <div class="fl"><label>Urutkan</label><select name="sort" data-auto>
    <option value="old">Terlama menunggu</option><option value="new" @selected(request('sort') === 'new')>Terbaru</option>
    <option value="score" @selected(request('sort') === 'score')>Skor tertinggi</option></select></div>
  <a class="btn gh sm" href="{{ route('match.index') }}">↺ Reset</a>
</form>
<p class="mut small" style="margin-bottom:12px"><b>{{ $daftarKandidat->total() }}</b> kandidat menunggu verifikasi</p>
<div class="mgrid">
  @forelse ($daftarKandidat as $m)
    @php $s = $m->skor; $hot = $m->umurHari > 3; @endphp
    <article class="mc {{ $hot ? 'hot' : '' }} reveal">
      <div class="row sp"><b class="mono" style="font-size:14px">{{ $m->matchCode }}</b>
        @if ($hot)<span class="age" style="color:var(--coral2);background:var(--coralsoft)">{{ $m->umurHari }} hari!</span>
        @else<span class="bd cand">{{ $m->labelStatus }}</span>@endif</div>
      <div class="pair"><span style="color:var(--coral2)">{{ $m->lostReport->reportCode }}</span><span style="color:var(--amber2)">↔</span>
        <span style="color:var(--green2)">{{ $m->foundReport->reportCode }}</span>
        <span class="mut" style="font-family:var(--fb);font-weight:600">· {{ $m->lostReport->category->name }}</span></div>
      <div class="thumbs"><img src="{{ $m->lostReport->photoSrc }}" alt="kehilangan"><img src="{{ $m->foundReport->photoSrc }}" alt="penemuan"></div>
      <div class="scoreline">
        <svg class="ring" viewBox="0 0 72 72"><circle class="bgc" cx="36" cy="36" r="30"/>
          <circle class="fgc" cx="36" cy="36" r="30" stroke="{{ $s['total'] >= 90 ? '#2E9E5B' : ($s['total'] >= 75 ? '#2A9D8F' : '#F2B705') }}"
            stroke-dasharray="188.5" stroke-dashoffset="{{ 188.5 * (1 - $s['total'] / 100) }}"/>
          <text x="36" y="38" text-anchor="middle">{{ $s['total'] }}</text><text class="pct" x="36" y="50" text-anchor="middle">COCOK</text></svg>
        <div class="mini">
          @foreach (['Kategori' => [$s['kategori'], '#2A9D8F'], 'Lokasi' => [$s['lokasi'], '#2D7DD2'], 'Waktu' => [$s['waktu'], '#F2B705']] as $lbl => [$v, $c])
            <div class="m"><span style="width:44px">{{ $lbl }}</span><span class="tr"><i style="width:{{ $v }}%;background:{{ $c }}"></i></span><span>{{ $v }}</span></div>
          @endforeach
        </div></div>
      <span class="tiny faint">📍 {{ $m->lostReport->location->name }} · selisih {{ $s['selisih'] }} hari · {{ \Illuminate\Support\Carbon::parse($m->createdAt)->diffForHumans() }}</span>
      @if ($m->revisionNote)<span class="tiny" style="color:#1D5CA0">✉ Himbauan revisi terkirim</span>@endif
      <div class="row" style="margin-top:auto">
        <a class="btn sm" style="flex:1" href="{{ route('match.detail', $m->id) }}">Tinjau Detail</a>
        <form method="POST" action="{{ route('match.cocok', $m->id) }}" data-confirm="Verifikasi kecocokan {{ $m->matchCode }}? Pelapor akan menerima notifikasi.">@csrf<button class="btn sm pr">✓</button></form>
        <form method="POST" action="{{ route('match.tolak', $m->id) }}" data-confirm="Tandai bukan barang yang sama? Kedua laporan dibuka kembali.">@csrf<button class="btn sm dno">✕</button></form>
      </div>
    </article>
  @empty <div class="empty" style="grid-column:1/-1"><b>Tidak ada kandidat saat ini</b>Kandidat muncul otomatis saat ada laporan yang cocok.</div> @endforelse
</div>
{{ $daftarKandidat->withQueryString()->links('pagination') }}
@endsection
