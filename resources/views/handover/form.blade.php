{{-- [VP] IM01 · UI-06/UI-07 Form Serah Terima — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Proses Serah Terima')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — VP</b>
  Artefak: VP-IM01 · UI-06 / UI-07 · PC014–PC017 · SCF-SRS-14/15<br>
  File: <span class="mono">resources/views/handover/form.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: kartu identitas pelapor (nama, NIM, kontak) untuk dicocokkan dengan KTM,
    ringkasan barang, <b>2 unggah foto</b> (pemilik memegang barang + KTM) →
    <span class="mono">route('handover.bukti', $match->id)</span>,
    checkbox konfirmasi KTM, tombol <b>Selesaikan &amp; Tutup Laporan</b>
    (disabled sampai bukti lengkap + KTM dicentang) →
    <span class="mono">route('handover.selesai', $match->id)</span>,
    serta opsi <b>Tunda</b> bila identitas tidak sesuai.<br>
    Variabel: <span class="mono">$match, $lost, $found, $pelapor</span>.
    JS gerbang tombol: beri id <span class="mono">#ktm-check</span> dan
    <span class="mono">#close-btn</span> dengan <span class="mono">data-bukti="1|0"</span> (sudah ditangani layout).
  </div>
</div>
@endsection