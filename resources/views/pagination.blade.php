@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:20px;flex-wrap:wrap;">
        @if (method_exists($paginator, 'total'))
            <div style="font-size:13px;color:#64748b;">
                Menampilkan <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
            </div>
        @endif

        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            @if ($paginator->onFirstPage())
                <span style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;color:#94a3b8;font-size:13px;background:#f8fafc;">&laquo; Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding:6px 12px;border:1px solid #cbd5e1;border-radius:8px;color:#1e293b;font-size:13px;text-decoration:none;background:#fff;">&laquo; Sebelumnya</a>
            @endif

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span style="padding:6px 10px;color:#64748b;font-size:13px;">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" style="padding:6px 12px;border:1px solid #2563eb;border-radius:8px;background:#2563eb;color:#fff;font-weight:600;font-size:13px;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" style="padding:6px 12px;border:1px solid #cbd5e1;border-radius:8px;color:#1e293b;font-size:13px;text-decoration:none;background:#fff;">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding:6px 12px;border:1px solid #cbd5e1;border-radius:8px;color:#1e293b;font-size:13px;text-decoration:none;background:#fff;">Berikutnya &raquo;</a>
            @else
                <span style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;color:#94a3b8;font-size:13px;background:#f8fafc;">Berikutnya &raquo;</span>
            @endif
        </div>
    </nav>
@endif