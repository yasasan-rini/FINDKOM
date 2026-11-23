@extends('layouts.app')
@section('judul', 'Serah Terima')
@section('konten')
<h1 class="reveal">Serah Terima Barang</h1>
<p class="sub reveal">Laporan terverifikasi yang pemiliknya belum mengambil. Wajib <b>2 foto bukti</b> (pemilik memegang barang + KTM) sebelum laporan ditutup.</p>
@if ($daftarMatch->count())
  <div class="mgrid">
    @foreach ($daftarMatch as $m)
      <article class="mc reveal" style="border-top-color:var(--blue)">
        <div class="row sp"><b class="mono">{{ $m->matchCode }}</b><span class="bd wait">Menunggu Diambil</span></div>
        <div class="thumbs"><img src="{{ $m->foundReport->photoSrc }}" alt="">
          <div style="width:50%;display:flex;flex-direction:column;justify-content:center;gap:3px;padding:4px">
            <b style="font-size:13px">{{ $m->lostReport->category->name }}</b>
            <span class="tiny mut">{{ $m->foundReport->location->name }}</span>
            <span class="mono tiny faint">{{ $m->lostReport->reportCode }} ↔ {{ $m->foundReport->reportCode }}</span></div></div>
        <div class="row" style="gap:8px"><span class="ava" style="width:30px;height:30px;font-size:11px">{{ $m->lostReport->reporter->initials }}</span>
          <div><b style="font-size:13px;display:block;line-height:1.2">{{ $m->lostReport->reporter->namaLengkap }}</b>
            <span class="tiny faint mono">{{ $m->lostReport->reporter->nim }}</span></div></div>
        @if ($m->evidence)<span class="tiny ok" style="font-weight:800">✓ Bukti foto lengkap — siap ditutup</span>
        @else<span class="tiny faint">Terverifikasi {{ \Illuminate\Support\Carbon::parse($m->verifiedAt)->diffForHumans() }}</span>@endif
        <a class="btn pr sm w" style="margin-top:auto" href="{{ route('handover.form', $m->id) }}">🤝 Proses Serah Terima</a>
      </article>
    @endforeach
  </div>
@else
  <div class="empty reveal"><b>Belum ada barang menunggu diambil</b>Verifikasi kandidat kecocokan dulu — setelah itu laporannya muncul di sini.
    <br><a href="{{ route('match.index') }}" class="btn pr sm" style="margin-top:14px">✨ Ke Kandidat Kecocokan</a></div>
@endif
<h2 class="reveal">Riwayat serah terima</h2>
<div class="tblwrap reveal">
  <table><thead><tr><th>Kode</th><th>Barang</th><th>Pemilik</th><th>Petugas</th><th>Waktu Serah</th><th>Bukti</th></tr></thead><tbody>
    @forelse ($riwayat as $m)
      <tr class="cl" onclick="location='{{ route('handover.form', $m->id) }}'">
        <td class="mono">{{ $m->matchCode }}</td>
        <td>{{ $m->lostReport->category->name }} <span class="tiny faint">({{ $m->lostReport->reportCode }})</span></td>
        <td>{{ $m->lostReport->reporter->namaLengkap }}</td>
        <td>{{ $m->evidence?->security?->namaLengkap ?? '—' }}</td>
        <td>{{ $m->evidence ? \Illuminate\Support\Carbon::parse($m->evidence->handoverAt)->translatedFormat('d M Y H:i') : '—' }}</td>
        <td>@if ($m->evidence)<span class="row" style="gap:5px">
          <img src="{{ $m->evidence->photoHoldingSrc }}" style="width:40px;height:28px;object-fit:cover;border-radius:6px" alt="">
          <img src="{{ $m->evidence->photoKtmSrc }}" style="width:40px;height:28px;object-fit:cover;border-radius:6px" alt=""></span>@else — @endif</td></tr>
    @empty <tr><td colspan="6"><div class="empty" style="border:0">Belum ada serah terima yang selesai.</div></td></tr> @endforelse
  </tbody></table>
</div>
@endsection