<a class="rc" href="{{ route('laporan.detail', ['type' => strtolower($r->tipe), 'code' => $r->reportCode]) }}">
  <div class="ph"><img src="{{ $r->photoSrc }}" alt="Foto {{ $r->category->name }}" loading="lazy">
    <span class="ty {{ $r->tipe === 'FOUND' ? 'found' : '' }}">{{ $r->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</span>
    <span class="bd {{ $r->badgeClass }}">{{ $r->labelStatus }}</span></div>
  <div class="body"><b>{{ $r->category->name }}</b>
    <span class="meta">📍 {{ $r->location->name }} · {{ $r->floorRoom }}</span>
    <span class="meta">📅 {{ \Illuminate\Support\Carbon::parse($r->eventDate)->translatedFormat('d M Y') }} · {{ $r->eventTime }}</span>
    <span class="code">{{ $r->reportCode }}</span></div></a>