@extends('layouts.app')
@section('judul', 'Dashboard Security')
@section('konten')
<div class="hero reveal"><div><h1>Dashboard Security</h1>
  <p class="mut">Ringkasan operasional FINDKOM — {{ now()->translatedFormat('l, d F Y') }}</p></div>
  <a class="btn pr" href="{{ route('match.index') }}">✨ Tinjau {{ $stat['kandidat'] }} kandidat</a></div>
<div class="g4" style="margin-bottom:22px">
  <a class="stat reveal" style="--sc:#B57E00" href="{{ route('match.index') }}"><b data-count="{{ $stat['kandidat'] }}">0</b><span>Kandidat menunggu</span></a>
  <a class="stat reveal" style="--sc:#C53A3A" href="{{ route('laporan.semua', ['tipe' => 'LOST']) }}"><b data-count="{{ $stat['hilang'] }}">0</b><span>Kehilangan aktif</span></a>
  <a class="stat reveal" style="--sc:#1D5CA0" href="{{ route('receipt.index') }}"><b data-count="{{ $stat['pos'] }}">0</b><span>Barang di pos</span></a>
  <a class="stat reveal" style="--sc:#1F7A43" href="{{ route('handover.index') }}"><b data-count="{{ $stat['selesai'] }}">0</b><span>Selesai diserahkan</span></a>
</div>
<div class="dashgrid">
  <div>
    <div class="card reveal">
      <div class="row sp"><h3>Perlu Tindakan</h3><span class="tiny faint">kandidat terlama di atas</span></div>
      <div style="margin-top:12px">
        @forelse ($mendesak as $m)
          @php $siap = $m->foundReport->receivedAt !== null; @endphp
          <div class="urgent {{ $m->umurHari > 3 ? 'hot' : '' }}">
            <div class="row sp"><div class="row"><b class="mono small">{{ $m->lostReport->reportCode }} ↔ {{ $m->foundReport->reportCode }}</b>
              <span class="age">{{ $m->umurHari === 0 ? 'hari ini' : $m->umurHari.' hari menunggu' }}</span></div>
              <span class="bd {{ $m->badgeClass }}">{{ $m->labelStatus }}</span></div>
            <div class="row sp" style="margin-top:7px">
              <span class="mut small">{{ $m->lostReport->category->name }} · {{ $m->lostReport->location->name }} · skor <b>{{ $m->skor['total'] }}%</b>
                @if (! $siap)<span class="tiny" style="color:var(--coral2);font-weight:800"> · fisik belum di pos</span>@endif</span>
              <a class="btn sm {{ $siap ? 'pr' : 'am' }}" href="{{ $siap ? route('match.detail', $m->id) : route('receipt.index', ['id' => $m->foundReportId]) }}">{{ $siap ? 'Tinjau' : 'Terima fisik' }}</a></div></div>
        @empty <div class="empty"><b>Semua kandidat sudah ditinjau</b>Tidak ada yang menunggu verifikasi. Kerja bagus!</div> @endforelse
      </div>
    </div>
    <div class="card reveal" style="margin-top:18px">
      <h3>Laporan masuk — 7 hari terakhir</h3>
      @php $W = 340; $base = 132; $gw = $W / 7; @endphp
      <svg class="chart" viewBox="0 0 {{ $W }} 168" style="margin-top:10px">
        @for ($g = 0; $g <= 2; $g++)
          <line x1="8" y1="{{ $base - $g * 52 }}" x2="{{ $W - 8 }}" y2="{{ $base - $g * 52 }}"
                stroke="#E1EAE8" stroke-width="1" @if ($g) stroke-dasharray="3 4" @endif />
        @endfor
        @foreach ($chart as $i => $d)
          @php $x = $i * $gw + $gw / 2; $hl = $d['lost'] / $chartMax * 104; $hf = $d['found'] / $chartMax * 104; @endphp
          <rect class="bar" x="{{ $x - 15 }}" y="{{ $base - max($hl, 2) }}" width="13" height="{{ max($hl, 2) }}" rx="4" fill="#E4573D" style="animation-delay:{{ $i * .06 }}s"/>
          <rect class="bar" x="{{ $x + 2 }}" y="{{ $base - max($hf, 2) }}" width="13" height="{{ max($hf, 2) }}" rx="4" fill="#2A9D8F" style="animation-delay:{{ $i * .06 + .03 }}s"/>
          @if ($d['lost'])<text x="{{ $x - 8 }}" y="{{ $base - $hl - 5 }}" text-anchor="middle" font-size="9" font-weight="800" fill="#C53A3A">{{ $d['lost'] }}</text>@endif
          @if ($d['found'])<text x="{{ $x + 8 }}" y="{{ $base - $hf - 5 }}" text-anchor="middle" font-size="9" font-weight="800" fill="#1F7A43">{{ $d['found'] }}</text>@endif
          <text x="{{ $x }}" y="{{ $base + 16 }}" text-anchor="middle" font-size="9.5" font-weight="700" fill="#7D8F93">{{ $d['label'] }}</text>
          <text x="{{ $x }}" y="{{ $base + 29 }}" text-anchor="middle" font-size="8.5" fill="#B0BFBD">{{ $d['tgl'] }}</text>
        @endforeach
      </svg>
      <div class="legend"><span><i style="background:#E4573D"></i>Kehilangan</span><span><i style="background:#2A9D8F"></i>Penemuan</span></div>
    </div>
  </div>
  <div>
    <div class="card reveal">
      <h3 style="margin-bottom:14px">Kategori teratas</h3>
      @php $warna = ['#12676B', '#F2B705', '#E4573D', '#2D7DD2', '#7B5EA7']; $maxK = max(1, $kategori->max('jumlah')); @endphp
      @foreach ($kategori as $i => $k)
        <div class="catbar"><div class="lbl"><span>{{ $k['nama'] }}</span><b>{{ $k['jumlah'] }}</b></div>
          <div class="tr"><i style="width:{{ $k['jumlah'] / $maxK * 100 }}%;background:{{ $warna[$i] }};animation-delay:{{ $i * .08 }}s"></i></div></div>
      @endforeach
    </div>
    <div class="card reveal" style="margin-top:18px">
      <h3 style="margin-bottom:14px">Aktivitas terbaru</h3>
      <ul class="feed">
        @foreach ($aktivitas as $a)
          <li style="--fc:{{ $a['warna'] }}">{{ $a['teks'] }}<small>{{ \Illuminate\Support\Carbon::parse($a['waktu'])->diffForHumans() }}</small></li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endsection