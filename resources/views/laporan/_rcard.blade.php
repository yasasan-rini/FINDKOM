{{-- [AR] Partial kartu laporan (dipakai Cari & Beranda) — BELUM DIKERJAKAN --}}
{{-- TODO AR: terima $r (LostReport|FoundReport). Tampilkan foto ($r->photoSrc),
     chip jenis ($r->tipe), lencana ($r->badgeClass / $r->labelStatus),
     kategori, lokasi, tanggal, dan kode laporan; tautkan ke
     route('laporan.detail', ['type' => strtolower($r->tipe), 'code' => $r->reportCode]).
     Class CSS .rc / .ph / .body sudah tersedia di layout. --}}
<div class="empty">Kartu laporan belum dibuat (AR).</div>