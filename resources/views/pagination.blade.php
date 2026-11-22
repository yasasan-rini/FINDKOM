@if ($paginator->hasPages())
<nav class="pager">
  @if ($paginator->onFirstPage()) <span class="btn sm" style="opacity:.4">← Sebelumnya</span>
  @else <a class="btn sm" href="{{ $paginator->previousPageUrl() }}">← Sebelumnya</a> @endif
  <span class="info">Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} · {{ $paginator->total() }} data</span>
  @if ($paginator->hasMorePages()) <a class="btn sm" href="{{ $paginator->nextPageUrl() }}">Berikutnya →</a>
  @else <span class="btn sm" style="opacity:.4">Berikutnya →</span> @endif
</nav>
@endif