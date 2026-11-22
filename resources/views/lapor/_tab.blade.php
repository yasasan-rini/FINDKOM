{{-- [BK+AR] Partial tab Kehilangan / Penemuan — KERANGKA, silakan percantik --}}
<div class="tabs">
  <a href="{{ route('kehilangan.form') }}" class="{{ ($aktif ?? '') === 'hilang' ? 'on lost' : '' }}">Kehilangan</a>
  <a href="{{ route('penemuan.form') }}" class="{{ ($aktif ?? '') === 'temuan' ? 'on found' : '' }}">Penemuan</a>
</div>