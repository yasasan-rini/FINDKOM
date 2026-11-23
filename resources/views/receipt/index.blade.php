@extends('layouts.app')
@section('judul', 'Konfirmasi Penerimaan')
@section('konten')
<h1 class="reveal">Konfirmasi Penerimaan Barang Fisik</h1>
<p class="sub reveal">Penemu menyerahkan barang di pos? Cari laporannya, cocokkan fisik dengan deskripsi, lalu catat penerimaan resmi.</p>
<div class="dashgrid">
  <div class="card reveal">
    <form method="GET" action="{{ route('receipt.index') }}">
      <div class="fl"><label>Cari laporan penemuan</label>
        <div class="searchwrap"><input name="q" value="{{ $keyword }}" placeholder="kode laporan (F-0009) atau nama pelapor…" data-live></div></div>
    </form>
    @forelse ($hasil as $r)
      <a class="receipt-item {{ $terpilih?->id === $r->id ? 'on' : '' }}" href="{{ route('receipt.index', ['id' => $r->id, 'q' => $keyword]) }}">
        <img src="{{ $r->photoSrc }}" alt="">
        <div class="in"><b>{{ $r->category->name }} <span class="mono tiny mut">{{ $r->reportCode }}</span></b>
          <span class="tiny mut">{{ $r->reporterName }} · {{ $r->location->name }} · {{ \Illuminate\Support\Carbon::parse($r->foundDate)->format('d M') }}</span></div>→</a>
    @empty
      <div class="empty"><b>Laporan tidak ditemukan</b>{{ $keyword !== '' ? 'Arahkan pelapor membuat laporan penemuan terlebih dahulu.' : 'Semua barang temuan sudah diterima di pos. 🎉' }}</div>
    @endforelse
  </div>
  <div>
    <div class="card reveal">
      @if ($terpilih)
        <div class="row sp" style="margin-bottom:12px"><div class="row"><span class="ty found">Penemuan</span><b class="mono">{{ $terpilih->reportCode }}</b></div>
          <span class="bd {{ $terpilih->badgeClass }}">{{ $terpilih->labelStatus }}</span></div>
        <img src="{{ $terpilih->photoSrc }}" alt="" style="width:100%;height:190px;object-fit:cover;border-radius:13px;border:1px solid var(--line)">
        <div class="kvgrid">
          <div class="kv"><span>Pelapor</span><b>{{ $terpilih->reporterName }}</b><div class="tiny faint mono">{{ $terpilih->reporter?->kontak }}</div></div>
          <div class="kv"><span>Kategori</span><b>{{ $terpilih->category->name }}</b></div>
          <div class="kv"><span>Lokasi</span><b>{{ $terpilih->location->name }} · {{ $terpilih->floorRoom }}</b></div>
          <div class="kv"><span>Ditemukan</span><b>{{ \Illuminate\Support\Carbon::parse($terpilih->foundDate)->format('d M Y') }} · {{ $terpilih->eventTime }}</b></div>
        </div>
        <div class="descbox">{{ $terpilih->description }}</div>
        <form method="POST" action="{{ route('receipt.konfirmasi', $terpilih->id) }}">@csrf
          <div class="fl"><label>Catatan ketidaksesuaian (opsional)</label>
            <textarea name="catatan" rows="2" placeholder="cth. Goresan di sudut tidak terlihat pada foto…"></textarea></div>
          <button class="btn pr w">✓ Konfirmasi Diterima</button>
          <p class="tiny faint" style="text-align:center;margin-top:8px">Mencatat petugas penerima &amp; waktu penerimaan resmi.</p>
        </form>
      @else
        <div class="empty"><b>Pilih laporan di sebelah kiri</b>Detail laporan &amp; foto akan tampil untuk dicocokkan dengan barang fisik.</div>
      @endif
    </div>
    <h2 class="reveal">Barang di pos saat ini</h2>
    @forelse ($diPos as $r)
      <a class="receipt-item reveal" href="{{ route('laporan.detail', ['type' => 'found', 'code' => $r->reportCode]) }}">
        <img src="{{ $r->photoSrc }}" alt="">
        <div class="in"><b>{{ $r->category->name }} <span class="mono tiny mut">{{ $r->reportCode }}</span></b>
          <span class="tiny mut">Diterima {{ \Illuminate\Support\Carbon::parse($r->receivedAt)->diffForHumans() }} · {{ $r->receiver?->namaLengkap }}</span></div>
        <span class="bd {{ $r->badgeClass }}">{{ $r->labelStatus }}</span></a>
    @empty <div class="empty">Belum ada barang titipan di pos.</div> @endforelse
  </div>
</div>
@endsection