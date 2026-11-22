{{-- resources/views/layouts/app.blade.php (HTML + CSS + Blade) --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('judul') | Findkom</title>
  <style>
    :root{--bg:#F7FAF9;--tx:#17252A;--br:#12676B;--ac:#2A9D8F;
      --ln:#E1EAE8;--dm:#4D6166;--er:#D64545;--ok:#2E9E5B;--wn:#F2B705;
      --soft:#EEF5F3}
    *{box-sizing:border-box;margin:0}
    body{background:var(--bg);color:var(--tx);font:14.5px/1.55 Inter,
      system-ui,sans-serif}
    h1,h2{font-family:Montserrat,sans-serif;margin:18px 0 10px}
    a{color:var(--br)}
    .top{display:flex;align-items:center;gap:20px;padding:10px 24px;
      background:#fff;border-bottom:1px solid var(--ln)}
    .top .lg img{height:34px}
    .nav{display:flex;gap:6px;flex:1}
    .nav a{padding:8px 14px;border-radius:10px;text-decoration:none;
      color:var(--dm)}
    .nav a:hover{background:var(--soft);color:var(--br)}
    .wrap{max-width:1100px;margin:0 auto;padding:20px 24px}
    .card{background:#fff;border:1px solid var(--ln);border-radius:16px;
      padding:20px;box-shadow:0 4px 14px rgba(23,37,42,.05)}
    .btn{display:inline-flex;align-items:center;gap:8px;cursor:pointer;
      border:1px solid var(--ln);background:#fff;border-radius:12px;
      padding:10px 18px;text-decoration:none;color:var(--tx)}
    .btn.pr{background:var(--br);border-color:var(--br);color:#fff}
    .btn.sm{padding:6px 12px;font-size:13px}
    .btn.rd{color:var(--er)}
    .btn:disabled{opacity:.45;cursor:not-allowed}
    .w{width:100%}
    .fl{margin-bottom:14px}
    .fl label{display:block;font-size:12.5px;font-weight:600;
      color:var(--dm);margin-bottom:6px}
    input,select,textarea{width:100%;padding:10px 13px;font:inherit;
      border:1px solid #CFDCDA;border-radius:12px;background:#fff}
    input:focus,select:focus,textarea:focus{outline:0;
      border-color:var(--br);box-shadow:0 0 0 3px rgba(18,103,107,.14)}
    .g2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .g4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
    .g12{display:grid;grid-template-columns:repeat(12,1fr);gap:20px}
    .c4{grid-column:span 4}.c8{grid-column:span 8}
    .row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .sp{justify-content:space-between}
    .mut{color:var(--dm)}.er{color:var(--er)}
    .bd{padding:3px 10px;border-radius:99px;font-size:11.5px;
      font-weight:600;background:var(--soft);color:var(--br)}
    .bn{padding:12px 24px;font-weight:600;color:#fff}
    .bn.ok{background:var(--ok)}.bn.wn{background:var(--wn);color:#000}
    .bn.er{background:var(--er)}
    .nt{border:1px solid var(--ac);background:#F1FAF8;border-radius:14px;
      padding:14px 16px;margin-bottom:14px}
    .em{border:1.5px dashed #C9D8D5;border-radius:16px;padding:30px;
      text-align:center;color:var(--dm);background:#fff}
    @media(max-width:760px){.g2,.g4,.g12{grid-template-columns:1fr}
      .c4,.c8{grid-column:auto}.top{flex-wrap:wrap}}
  </style>
  @stack('css')
</head>
<body>
@auth
@php $isSec = auth()->user()->role === 'SECURITY'; @endphp
<header class="top">
  <a href="{{ route('beranda') }}" class="lg">
    <img src="{{ asset('img/findkom-horizontal.svg') }}" alt="Findkom">
  </a>
  <nav class="nav">
    @if ($isSec)
      <a href="{{ route('dashboard') }}">Dashboard</a>
      <a href="{{ route('match.index') }}">Kandidat Kecocokan</a>
      <a href="{{ route('handover.index') }}">Serah Terima</a>
      <a href="{{ route('laporan.semua') }}">Semua Laporan</a>
      <a href="{{ route('master.index') }}">Master Data</a>
    @else
      <a href="{{ route('beranda') }}">Beranda</a>
      <a href="{{ route('laporan.buat') }}">Buat Laporan</a>
      <a href="{{ route('laporan.status') }}">Status &amp; Riwayat</a>
      <a href="{{ route('laporan.cari') }}">Cari Barang</a>
    @endif
  </nav>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <span class="mut">{{ auth()->user()->namaLengkap }}</span>
    <button class="btn sm">Keluar</button>
  </form>
</header>
@endauth

{{-- Banner notifikasi (success / warning / error) --}}
@foreach (['success' => 'ok', 'warning' => 'wn', 'error' => 'er'] as $k => $c)
  @if (session($k))
    <div class="bn {{ $c }}" role="status">{{ session($k) }}</div>
  @endif
@endforeach

<main class="wrap">@yield('konten')</main>
@stack('skrip')
</body>
</html>
