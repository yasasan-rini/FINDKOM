@extends('layouts.app')
@section('judul', $laporan ? 'Ubah Laporan Penemuan' : 'Lapor Penemuan')
@section('konten')
<h1 class="reveal">{{ $laporan ? 'Ubah Laporan '.$laporan->reportCode : 'Buat Laporan' }}</h1>
<p class="sub reveal">Foto barang <b>wajib</b>. Setelah lapor, titipkan barang fisik ke Pos Security Gedung F dengan menunjukkan kode laporan.</p>
@include('lapor._tab', ['aktif' => 'temuan'])
@include('lapor._sukses')
<form class="card reveal" style="max-width:760px" method="POST" enctype="multipart/form-data"
      action="{{ $laporan ? route('laporan.update', ['type' => 'found', 'id' => $laporan->id]) : route('penemuan.buat') }}">
  @csrf @if ($laporan) @method('PUT') @endif
  <div class="notice green" style="margin-top:0">📦<div><b>Laporan Penemuan.</b> Jangan lupa menitipkan barang ke pos satpam setelah mengisi form ini.</div></div>
  @include('lapor._fields', ['type' => 'FOUND'])
  <div class="fl"><label for="foto">Foto Barang Temuan {{ $laporan ? '(kosongkan jika tetap)' : '(wajib, maks. 2 MB)' }}</label>
    @if ($laporan?->photoUrl)<img src="{{ $laporan->photoSrc }}" class="pv" alt="Foto tersimpan">@endif
    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png" data-photo="pvFound" {{ $laporan ? '' : 'required' }}>
    <img id="pvFound" class="pv" hidden alt="Pratinjau">
    @error('foto')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="row sp"><span class="tiny faint">🛡 Identitasmu tercatat sebagai penemu resmi.</span>
    <button class="btn pr">{{ $laporan ? 'Simpan Perubahan' : 'Kirim Laporan' }}</button></div>
</form>
@endsection