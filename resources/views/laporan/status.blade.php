@extends('layouts.app')
@section('judul', 'Status & Riwayat')
@section('konten')
<h1 class="reveal">Status &amp; Riwayat</h1>
<p class="sub reveal">Pantau perkembangan laporanmu tanpa perlu bolak-balik bertanya ke satpam.</p>
<div class="row reveal" style="margin-bottom:18px">
  @foreach (['all' => 'Semua', 'aktif' => 'Aktif', 'wait' => 'Menunggu Diambil', 'done' => 'Selesai'] as $k => $l)
    <a class="chip {{ $filter === $k ? 'on' : '' }}" href="{{ route('laporan.status', ['filter' => $k]) }}">{{ $l }} · {{ $counts[$k] }}</a>
  @endforeach
<<<<<<< HEAD
</div>
@forelse ($laporan as $l)
  <article class="status-card reveal">
    <div class="row sp">
      <div class="row"><span class="ty {{ $l->tipe === 'FOUND' ? 'found' : '' }}">{{ $l->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</span>
        <b class="mono" style="font-size:15px">{{ $l->reportCode }}</b>
        <span class="mut small">{{ $l->category->name }} · {{ $l->location->name }}</span></div>
      <span class="bd {{ $l->badgeClass }}">{{ $l->labelStatus }}</span>
    </div>
    @include('laporan._timeline', ['l' => $l])
    @if ($l->status === 'NOTIFIED')
      <div class="notice">✨<div><b>Barang menunggu diambil di Pos Security Gedung F.</b> Bawa KTM sebagai bukti kepemilikan (07.00–21.00 WIB).</div></div>
    @elseif ($l->status === 'CLOSED')
      <div class="notice green">✓<div>Selesai — barang telah diserahkan. Bukti serah terima terarsip.</div></div>
    @endif
    @if ($l->tipe === 'FOUND' && $l->receivedAt)
      <div class="notice green">📦<div>Fisik barang telah diterima di pos pada {{ \Illuminate\Support\Carbon::parse($l->receivedAt)->translatedFormat('d M Y H:i') }}.</div></div>
    @endif
    @php $revisi = $l->matches->firstWhere('status', 'MATCH_CANDIDATE')?->revisionNote; @endphp
    @if ($revisi)<div class="notice blue">✉<div><b>Catatan dari Security:</b> {{ $revisi }}</div></div>@endif
    <div class="row sp" style="margin-top:10px">
      <span class="tiny faint">🕐 Dibuat {{ \Illuminate\Support\Carbon::parse($l->createdAt)->diffForHumans() }}</span>
      <div class="row">
        <a class="btn sm" href="{{ route('laporan.detail', ['type' => strtolower($l->tipe), 'code' => $l->reportCode]) }}">Detail</a>
        @if ($l->isEditable)
          <a class="btn sm" href="{{ route('laporan.edit', ['type' => strtolower($l->tipe), 'id' => $l->id]) }}">Ubah</a>
          <form method="POST" action="{{ route('laporan.destroy', ['type' => strtolower($l->tipe), 'id' => $l->id]) }}"
                data-confirm="Hapus laporan {{ $l->reportCode }}? Kandidat kecocokan terkait ikut dihapus.">
            @csrf @method('DELETE')<button class="btn sm dno">Hapus</button></form>
        @endif
      </div>
    </div>
  </article>
@empty
  <div class="empty reveal"><b>Belum ada laporan di kategori ini</b>
    <a href="{{ route('kehilangan.form') }}" class="btn pr sm" style="margin-top:12px">+ Buat laporan pertama</a></div>
@endforelse

<h2 class="reveal">Notifikasi</h2>
<div class="card reveal">
  @forelse ($notifikasi as $n)
    <a class="notif {{ $n->isRead ? '' : 'unread' }}" href="{{ route('notifikasi.baca', $n->id) }}">
      <div><p>{{ $n->message }}</p><small>{{ \Illuminate\Support\Carbon::parse($n->createdAt)->diffForHumans() }}</small></div></a>
  @empty <div class="empty" style="border:0"><b>Belum ada notifikasi</b>Perkembangan laporanmu akan muncul di sini.</div> @endforelse
=======
>>>>>>> 8d19054 (Update controllers dan views sesuai Modul 3)
</div>
@forelse ($laporan as $l)
  <article class="status-card reveal">
    <div class="row sp">
      <div class="row"><span class="ty {{ $l->tipe === 'FOUND' ? 'found' : '' }}">{{ $l->tipe === 'LOST' ? 'Kehilangan' : 'Penemuan' }}</span>
        <b class="mono" style="font-size:15px">{{ $l->reportCode }}</b>
        <span class="mut small">{{ $l->category->name }} · {{ $l->location->name }}</span></div>
      <span class="bd {{ $l->badgeClass }}">{{ $l->labelStatus }}</span>
    </div>
    @include('laporan._timeline', ['l' => $l])
    @if ($l->status === 'NOTIFIED')
      <div class="notice">✨<div><b>Barang menunggu diambil di Pos Security Gedung F.</b> Bawa KTM sebagai bukti kepemilikan (07.00–21.00 WIB).</div></div>
    @elseif ($l->status === 'CLOSED')
      <div class="notice green">✓<div>Selesai — barang telah diserahkan. Bukti serah terima terarsip.</div></div>
    @endif
    @php $revisi = $l->matches->firstWhere('status', 'MATCH_CANDIDATE')?->revisionNote; @endphp
    @if ($revisi)<div class="notice blue">✉<div><b>Catatan dari Security:</b> {{ $revisi }}</div></div>@endif
    <div class="row sp" style="margin-top:10px">
      <span class="tiny faint">🕐 Dibuat {{ \Illuminate\Support\Carbon::parse($l->createdAt)->diffForHumans() }}</span>
      <div class="row">
        <a class="btn sm" href="{{ route('laporan.detail', ['type' => strtolower($l->tipe), 'code' => $l->reportCode]) }}">Detail</a>
        @if ($l->isEditable)
          <a class="btn sm" href="{{ route('laporan.edit', ['type' => strtolower($l->tipe), 'id' => $l->id]) }}">Ubah</a>
          <form method="POST" action="{{ route('laporan.destroy', ['type' => strtolower($l->tipe), 'id' => $l->id]) }}"
                data-confirm="Hapus laporan {{ $l->reportCode }}? Kandidat kecocokan terkait ikut dihapus.">
            @csrf @method('DELETE')<button class="btn sm dno">Hapus</button></form>
        @endif
      </div>
    </div>
  </article>
@empty
  <div class="empty reveal"><b>Belum ada laporan di kategori ini</b>
    <a href="{{ route('kehilangan.form') }}" class="btn pr sm" style="margin-top:12px">+ Buat laporan pertama</a></div>
@endforelse
@endsection