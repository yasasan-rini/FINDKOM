{{-- [VP] IM02 · UI-08 Master Data — BELUM DIKERJAKAN --}}
@extends('layouts.app')
@section('judul', 'Master Data')
@section('konten')
<div class="empty">
  <b>Belum dikerjakan — VP</b>
  Artefak: VP-IM02 · UI-08 · PC018–PC022 · SCF-SRS-10/11/12<br>
  File: <span class="mono">resources/views/master/index.blade.php</span>
  <div class="small mut" style="margin-top:12px;text-align:left;max-width:560px;margin-inline:auto">
    Elemen wajib: tab <b>Gedung / Lantai-Ruangan / Kategori</b>,
    form tambah (nama + gedung induk bila ruangan) → <span class="mono">route('master.tambah')</span>,
    tabel daftar + jumlah pemakaian, aksi ubah → <span class="mono">route('master.ubah', $d->id)</span>,
    aksi hapus dengan konfirmasi → <span class="mono">route('master.hapus', $d->id)</span>,
    dan pesan "Data sudah terdaftar" / "masih digunakan laporan".<br>
    Variabel: <span class="mono">$jenis, $data, $daftarGedung, $jumlah</span>.
  </div>
</div>
@endsection