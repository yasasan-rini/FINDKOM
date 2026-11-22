{{-- [VP] IM01 · UI-07 Serah Terima (daftar) — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Serah Terima')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — VP</b>
  Artefak: VP-IM01 · UI-07 · PC013 · SD-08 · UC-08<br>
  File: <span class="mono">resources/views/handover/index.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: kartu laporan berstatus <b>NOTIFIED</b> (kode match, barang, pemilik + NIM),
    tombol <b>Proses Serah Terima</b> → <span class="mono">route('handover.form', $m->id)</span>,
    dan tabel riwayat serah terima yang sudah CLOSED.<br>
    Variabel: <span class="mono">$daftarMatch, $riwayat</span>.
  </div>
</div>
@endsection
