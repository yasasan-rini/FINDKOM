@extends('layouts.app')
@section('judul', 'Detail '.$laporan->reportCode)
@section('konten')
<div class="row reveal" style="margin-bottom:14px">
  <a class="btn gh sm" href="{{ url()->previous() }}">← Kembali</a>
  <b class="mono" style="font-size:16px">{{ $laporan->reportCode }}</b>
  <span class="bd {{ $laporan->badgeClass }}">{{ $laporan->labelStatus }}</span></div>
<div class="dashgrid">
  <div class="card reveal">
    <div class="dhero"><img src="{{ $laporan->photoSrc }}" alt="">
      <div class="ov"><span class="ty {{ $laporan->tipe === 'FOUND' ? 'found' : '' }}">{{ $laporan->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</span>
        <span class="mono">{{ $laporan->reportCode }}</span></div></div>
    @include('laporan._timeline', ['l' => $laporan])
    <div class="kvgrid">
      <div class="kv"><span>Pelapor</span><b>{{ $own || $isSec ? ($laporan->reporter?->namaLengkap ?? $laporan->reporterName) : \Illuminate\Support\Str::before($laporan->reporter?->namaLengkap ?? $laporan->reporterName, ' ').' ***' }}</b>
        @if ($own || $isSec)<div class="tiny faint mono">{{ $laporan->reporter?->nim }} · {{ $laporan->reporter?->kontak }}</div>@endif</div>
      <div class="kv"><span>Kategori</span><b>{{ $laporan->category->name }}</b></div>
      <div class="kv"><span>Lokasi</span><b>{{ $laporan->location->name }}</b><div class="tiny faint">{{ $laporan->floorRoom }}</div></div>
      <div class="kv"><span>{{ $laporan->tipe === 'LOST' ? 'Hilang' : 'Ditemukan' }}</span>
        <b>{{ \Illuminate\Support\Carbon::parse($laporan->eventDate)->translatedFormat('d M Y') }} · {{ $laporan->eventTime }}</b></div>
    </div>
    <div class="descbox">{{ $laporan->description }}</div>
    @if ($laporan->tipe === 'FOUND' && $laporan->receivedAt)
      <div class="notice green">📦<div><b>Fisik diterima di pos</b> — {{ \Illuminate\Support\Carbon::parse($laporan->receivedAt)->translatedFormat('d M Y H:i') }}
        oleh {{ $laporan->receiver?->namaLengkap }}.@if ($laporan->receiptNote)<br><span class="small">Catatan: {{ $laporan->receiptNote }}</span>@endif</div></div>
    @endif
    @if ($laporan->status === 'NOTIFIED')
      <div class="notice">✨<div><b>Menunggu diambil pemilik.</b> {{ $isSec ? 'Proses di menu Serah Terima.' : 'Bawa KTM ke Pos Security Gedung F.' }}</div></div>
    @endif
    <div class="row" style="margin-top:18px">
      @if ($own && $laporan->isEditable)
        <a class="btn sm" href="{{ route('laporan.edit', ['type' => strtolower($laporan->tipe), 'id' => $laporan->id]) }}">Ubah Laporan</a>
        <form method="POST" action="{{ route('laporan.destroy', ['type' => strtolower($laporan->tipe), 'id' => $laporan->id]) }}"
              data-confirm="Hapus laporan {{ $laporan->reportCode }}?">@csrf @method('DELETE')<button class="btn sm dno">Hapus</button></form>
      @endif
      @if ($isSec && $laporan->tipe === 'FOUND' && ! $laporan->receivedAt && $laporan->status !== 'CLOSED')
        <a class="btn sm pr" href="{{ route('receipt.index', ['id' => $laporan->id]) }}">📦 Konfirmasi Penerimaan</a>
      @endif
      @if ($isSec && ($mN = $related->firstWhere('status', 'NOTIFIED')))
        <a class="btn sm pr" href="{{ route('handover.form', $mN->id) }}">🤝 Proses Serah Terima</a>
      @endif
    </div>
  </div>
  <div>
    <div class="card reveal">
      <h3 style="margin-bottom:10px">Kecocokan terkait</h3>
      @forelse ($related as $m)
        @php $lawan = $m->lostReportId === $laporan->id ? $m->foundReport : $m->lostReport; @endphp
        <a class="receipt-item" href="{{ $isSec ? route('match.detail', $m->id) : route('laporan.detail', ['type' => strtolower($lawan->tipe), 'code' => $lawan->reportCode]) }}">
          <div class="in"><b class="mono small">{{ $m->matchCode }} ↔ {{ $lawan->reportCode }}</b>
            <span class="tiny mut">{{ $lawan->category->name }} · skor {{ $m->skor['total'] }}%</span></div>
          <span class="bd {{ $m->badgeClass }}">{{ $m->labelStatus }}</span></a>
      @empty <div class="empty" style="padding:22px"><b>Belum ada kandidat</b>Sistem mencocokkan otomatis saat ada laporan yang sesuai.</div> @endforelse
    </div>
    @if ($evidence)
      <div class="card reveal" style="margin-top:16px">
        <h3 style="margin-bottom:10px">Bukti serah terima</h3>
        <div class="g2" style="gap:10px">
          <img src="{{ $evidence->photoHoldingSrc }}" style="border-radius:11px;border:1px solid var(--line);height:110px;width:100%;object-fit:cover" alt="">
          <img src="{{ $evidence->photoKtmSrc }}" style="border-radius:11px;border:1px solid var(--line);height:110px;width:100%;object-fit:cover" alt=""></div>
        <p class="tiny faint" style="margin-top:8px">🛡 {{ \Illuminate\Support\Carbon::parse($evidence->handoverAt)->translatedFormat('d M Y H:i') }} · petugas {{ $evidence->security?->namaLengkap }}</p>
      </div>
    @endif
  </div>
</div>
@endsection