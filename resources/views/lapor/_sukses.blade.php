@if (session('kodeLaporan'))
<div class="success-panel">
  <div class="row sp">
    <div><b style="font-size:16px">✅ Laporan Terkirim!</b>
      <div class="mut small">Simpan kode laporan ini sebagai bukti digital.</div>
      <div class="code">{{ session('kodeLaporan') }}</div></div>
    <span class="bd {{ \App\Support\ReportStatus::BADGE[session('statusLaporan')] ?? '' }}">{{ \App\Support\ReportStatus::LABEL[session('statusLaporan')] ?? session('statusLaporan') }}</span>
  </div>
  @if (session('jumlahKandidat') > 0)
    <div class="notice">✨<div><b>{{ session('jumlahKandidat') }} kandidat kecocokan ditemukan otomatis!</b> Security akan meninjau dan kamu akan menerima notifikasi.</div></div>
  @endif
  @if (session('tipeSukses') === 'FOUND')
    <div class="notice blue">📍<div><b>Langkah berikutnya:</b> serahkan barang fisik ke <b>Pos Security Gedung F</b> sambil menunjukkan kode <span class="mono">{{ session('kodeLaporan') }}</span>.</div></div>
  @endif
  <a class="btn pr sm" href="{{ route('laporan.status') }}">Lihat Status &amp; Riwayat →</a>
</div>
@endif