@extends('layouts.app')
@section('judul', 'Serah Terima '.$match->matchCode)
@section('konten')
<div class="row reveal" style="margin-bottom:14px">
  <a class="btn gh sm" href="{{ route('handover.index') }}">← Daftar terverifikasi</a>
  <b class="mono" style="font-size:16px">{{ $match->matchCode }}</b>
  <span class="bd {{ $match->badgeClass }}">{{ $match->labelStatus }}</span></div>
<h1 class="reveal">Proses Serah Terima</h1>
<p class="sub reveal">Cocokkan identitas KTM fisik dengan data, unggah 2 foto bukti, lalu tutup laporan.</p>
<div class="ho-grid">
  <div>
    <div class="idcard reveal">
      <span class="lbl">KARTU TANDA MAHASISWA — DATA LAPORAN</span>
      <div class="nm">{{ $pelapor->namaLengkap }}</div><div class="nim">{{ $pelapor->nim }}</div>
      <div class="fak">Fakultas Ilmu Komputer · Universitas Brawijaya<br>📞 {{ $pelapor->kontak }}</div>
    </div>
    <div class="card reveal" style="margin-top:16px;padding:16px 18px">
      <h3 style="margin-bottom:10px">Ringkasan barang</h3>
      <img src="{{ $found->photoSrc }}" alt="" style="width:100%;height:130px;object-fit:cover;border-radius:11px;border:1px solid var(--line)">
      <div class="kvgrid" style="grid-template-columns:1fr;margin:12px 0 0">
        <div class="kv"><span>Barang</span><b>{{ $lost->category->name }}</b></div>
        <div class="kv"><span>Ditemukan di</span><b>{{ $found->location->name }} · {{ $found->floorRoom }}</b></div>
        <div class="kv"><span>Deskripsi</span><b style="font-weight:600;font-size:12.5px">{{ $lost->description }}</b></div>
      </div>
    </div>
  </div>
  <div class="card reveal">
    <h3 style="margin-bottom:4px">Bukti Serah Terima <span class="mut small" style="font-family:var(--fb)">— wajib 2 foto</span></h3>
    <p class="tiny faint" style="margin-bottom:14px">Tombol penutupan hanya aktif setelah bukti lengkap &amp; identitas KTM terkonfirmasi.</p>
    @if ($match->evidence)
      <div class="g2" style="gap:14px">
        <div class="evslot"><h4>📷 Pemilik memegang barang</h4>
          <div class="done-img"><img src="{{ $match->evidence->photoHoldingSrc }}" alt=""><span class="okb">✓ Tersimpan</span></div></div>
        <div class="evslot"><h4>🪪 Foto KTM pemilik</h4>
          <div class="done-img"><img src="{{ $match->evidence->photoKtmSrc }}" alt=""><span class="okb">✓ Tersimpan</span></div></div>
      </div>
      <p class="tiny mut" style="margin:10px 0 0">🛡 Diunggah oleh {{ $match->evidence->security?->namaLengkap }} · {{ \Illuminate\Support\Carbon::parse($match->evidence->handoverAt)->translatedFormat('d M Y H:i') }}</p>
    @elseif ($match->status === 'NOTIFIED')
      <form method="POST" enctype="multipart/form-data" action="{{ route('handover.bukti', $match->id) }}">@csrf
        <div class="g2" style="gap:14px">
          <div class="evslot"><h4>1 · Pemilik memegang barang</h4>
            <input class="tin" name="fotoPemegangBarang" type="file" accept="image/*" required data-photo="pv1">
            <img id="pv1" class="pv" hidden alt="Pratinjau 1">
            @error('fotoPemegangBarang')<small class="er">{{ $message }}</small>@enderror</div>
          <div class="evslot"><h4>2 · Foto KTM pemilik</h4>
            <input class="tin" name="fotoKTM" type="file" accept="image/*" required data-photo="pv2">
            <img id="pv2" class="pv" hidden alt="Pratinjau 2">
            @error('fotoKTM')<small class="er">{{ $message }}</small>@enderror</div>
        </div>
        <button class="btn pr w" style="margin-top:16px">⬆ Simpan Bukti Foto</button>
      </form>
    @endif
    @if ($match->status === 'NOTIFIED')
      <hr style="border:0;border-top:1px dashed var(--line2);margin:18px 0">
      <form method="POST" action="{{ route('handover.selesai', $match->id) }}"
            data-confirm="Selesaikan & tutup laporan? Barang ditandai telah diserahkan kepada {{ $pelapor->namaLengkap }}. Status menjadi CLOSED beserta jejak audit.">
        @csrf
        <label class="checkline"><input type="checkbox" class="cb" id="ktm-check" name="ktm_confirmed" value="1" {{ $match->evidence ? '' : 'disabled' }}>
          <span>Saya telah <b>mencocokkan nama &amp; NIM pada KTM fisik</b> dengan data laporan di samping, dan keduanya sesuai.</span></label>
        @error('ktm_confirmed')<small class="er">{{ $message }}</small>@enderror
        <button class="btn pr w" id="close-btn" data-bukti="{{ $match->evidence ? '1' : '0' }}" disabled>✓ Selesaikan &amp; Tutup Laporan</button>
      </form>
      <div class="row sp" style="margin-top:10px">
        <span class="tiny faint">{{ $match->evidence ? 'Centang konfirmasi KTM untuk mengaktifkan tombol.' : 'Lengkapi 2 foto bukti terlebih dahulu.' }}</span>
      </div>
      <details class="rev"><summary>⏸ Tunda serah terima (KTM tidak sesuai)</summary>
        <form method="POST" action="{{ route('handover.tunda', $match->id) }}" style="margin-top:10px">@csrf
          <div class="fl"><label>Alasan penundaan</label>
            <input name="alasan" maxlength="300" required placeholder="cth. Nama pada KTM berbeda dengan data laporan"></div>
          <button class="btn sm dno">Tunda &amp; minta verifikasi ulang</button></form>
      </details>
    @endif
  </div>
</div>
@endsection