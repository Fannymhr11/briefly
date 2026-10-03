@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="btn-icon cursor-not-allowed opacity-40" aria-disabled="true"><x-icon name="chevron-left" class="h-4 w-4" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-icon" aria-label="Sebelumnya"><x-icon name="chevron-left" class="h-4 w-4" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-xs text-muted">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-brand-600 px-2 text-xs font-semibold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-line bg-surface px-2 text-xs font-medium text-ink transition hover:bg-soft">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-icon" aria-label="Berikutnya"><x-icon name="chevron-right" class="h-4 w-4" /></a>
        @else
            <span class="btn-icon cursor-not-allowed opacity-40" aria-disabled="true"><x-icon name="chevron-right" class="h-4 w-4" /></span>
        @endif
    </nav>
@endif
