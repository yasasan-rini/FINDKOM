@extends('layouts.app')
@section('judul', $laporan ? 'Ubah Laporan Kehilangan' : 'Lapor Kehilangan')
@section('konten')
<h1 class="reveal">{{ $laporan ? 'Ubah Laporan '.$laporan->reportCode : 'Buat Laporan' }}</h1>
<p class="sub reveal">Isi data selengkap mungkin — sistem otomatis mencocokkan berdasarkan <b>gedung, kategori, dan rentang waktu ±4 hari</b>.</p>
@include('lapor._tab', ['aktif' => 'hilang'])
@include('lapor._sukses')
<form class="card reveal" style="max-width:760px" method="POST" enctype="multipart/form-data"
      action="{{ $laporan ? route('laporan.update', ['type' => 'lost', 'id' => $laporan->id]) : route('kehilangan.buat') }}">
  @csrf @if ($laporan) @method('PUT') @endif
  <div class="notice" style="margin-top:0">🔍<div><b>Laporan Kehilangan.</b> Foto referensi bersifat opsional, namun sangat membantu verifikasi Security.</div></div>
  @include('lapor._fields', ['type' => 'LOST'])
  <div class="fl"><label for="photo">Foto Referensi (opsional, JPG/PNG maks. 2 MB)</label>
    @if ($laporan?->photoUrl)<img src="{{ $laporan->photoSrc }}" class="pv" alt="Foto tersimpan">@endif
    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png" data-photo="pvLost">
    <img id="pvLost" class="pv" hidden alt="Pratinjau">
    @error('photo')<small class="er">{{ $message }}</small>@enderror</div>
  <div class="row sp"><span class="tiny faint">🛡 Data hanya digunakan untuk proses pencocokan &amp; verifikasi.</span>
    <button class="btn dn">{{ $laporan ? 'Simpan Perubahan' : 'Kirim Laporan' }}</button></div>
</form>
@endsection