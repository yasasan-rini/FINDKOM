{{-- [VP] UI-06 Konfirmasi Penerimaan Barang Fisik — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Konfirmasi Penerimaan')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — VP</b>
  Artefak: UI-06 · PC010–PC012 · SD-06 · UC-06<br>
  File: <span class="mono">resources/views/receipt/index.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: input pencarian (kode laporan / nama pelapor), daftar hasil,
    panel detail + foto barang untuk dicocokkan dengan fisik,
    textarea catatan ketidaksesuaian (opsional),
    tombol <b>Konfirmasi Diterima</b> → <span class="mono">route('receipt.konfirmasi', $id)</span>,
    dan daftar barang yang sudah ada di pos.<br>
    Variabel: <span class="mono">$hasil, $terpilih, $diPos, $keyword</span>.
  </div>
</div>
@endsection