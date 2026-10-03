@props(['items', 'empty' => 'Belum ada aktivitas.'])
@php $tiles = ['green' => 'tile-green', 'red' => 'tile-red', 'purple' => 'tile-purple', 'blue' => 'tile-blue', 'amber' => 'tile-amber']; @endphp
<ul class="divide-y divide-line">
    @forelse ($items as $h)
        <li class="flex items-start gap-3 py-3.5 first:pt-0 last:pb-0">
            <span class="tile h-9 w-9 {{ $tiles[$h->tone] ?? 'tile-blue' }}"><x-icon :name="$h->icon" class="h-[18px] w-[18px]" /></span>
            <div class="min-w-0 flex-1">
                <p class="text-[13px] font-medium leading-snug text-ink">{{ $h->description }}</p>
                @if ($h->brief)<p class="mt-0.5 truncate text-xs text-muted">{{ $h->brief->platform_label }} • {{ $h->brief->brand_label }}</p>@endif
            </div>
            <time class="shrink-0 pt-0.5 text-[11px] text-muted" datetime="{{ $h->created_at->toIso8601String() }}">{{ $h->created_at->translatedFormat('j M, H:i') }}</time>
        </li>
    @empty
        <li class="py-6 text-center text-[13px] text-muted">{{ $empty }}</li>
    @endforelse
</ul>
