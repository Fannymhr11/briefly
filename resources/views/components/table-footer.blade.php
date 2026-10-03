@props(['paginator'])
<div {{ $attributes->class(['flex flex-col items-center justify-between gap-3 pt-4 sm:flex-row']) }}>
    <p class="text-xs text-muted">
        @if ($paginator->total() > 0)Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        @else Tidak ada data @endif
    </p>
    @if ($paginator->hasPages()){{ $paginator->links() }}@endif
</div>
