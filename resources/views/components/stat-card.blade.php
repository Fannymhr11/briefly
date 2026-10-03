@props(['label', 'value', 'icon' => 'file-text', 'tone' => 'blue', 'delta' => null, 'deltaLabel' => 'dari bulan lalu', 'sub' => null, 'invert' => false, 'href' => null])
@php
$tiles = ['blue' => 'tile-blue', 'amber' => 'tile-amber', 'green' => 'tile-green', 'red' => 'tile-red', 'purple' => 'tile-purple'];
$tints = ['blue' => '', 'amber' => 'bg-amber-500/[0.04]', 'green' => 'bg-emerald-500/[0.04]', 'red' => 'bg-rose-500/[0.04]', 'purple' => 'bg-violet-500/[0.04]'];
$good = $delta === null ? null : ($invert ? $delta <= 0 : $delta >= 0);
@endphp
<div {{ $attributes->class(['card relative overflow-hidden', 'transition hover:border-brand-300' => (bool) $href]) }}>
    <span class="pointer-events-none absolute inset-0 {{ $tints[$tone] ?? '' }}"></span>
    @if ($href)<a href="{{ $href }}" class="absolute inset-0 z-10" aria-label="{{ $label }}"></a>@endif
    <div class="relative p-4">
        <div class="flex items-center gap-3">
            <span class="tile {{ $tiles[$tone] ?? 'tile-blue' }}"><x-icon :name="$icon" class="h-5 w-5" /></span>
            <span class="text-[13px] font-medium text-muted">{{ $label }}</span>
        </div>
        <p class="mt-3 text-[28px] font-bold leading-none tracking-tight text-ink">{{ $value }}</p>
        @if ($delta !== null)
            <p class="mt-2.5 flex items-center gap-1 text-xs font-medium {{ $delta === 0 ? 'text-muted' : ($good ? 'text-emerald-500' : 'text-rose-500') }}">
                <x-icon :name="$delta >= 0 ? 'trend-up' : 'trend-down'" class="h-3.5 w-3.5" />
                {{ ($delta > 0 ? '+' : '').$delta }} {{ $deltaLabel }}
            </p>
        @elseif ($sub)
            <p class="mt-2.5 flex items-center gap-1 text-xs font-medium text-muted">{{ $sub }}</p>
        @endif
    </div>
</div>
