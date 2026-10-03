@props(['segments' => [], 'total' => 0, 'label' => 'Total Brief', 'center' => null])
@php
$sum = collect($segments)->sum('value');
$C = 2 * M_PI * 46;
$offset = 0;
@endphp
<div {{ $attributes->class(['flex flex-wrap items-center justify-center gap-x-8 gap-y-5']) }}>
    <div class="relative h-40 w-40 shrink-0">
        <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90" role="img" aria-label="Grafik donut {{ $label }}">
            <circle cx="60" cy="60" r="46" fill="none" stroke-width="15" class="stroke-soft"/>
            @foreach ($segments as $s)
                @if ($s['value'] > 0 && $sum > 0)
                    @php $len = $s['value'] / $sum * $C; @endphp
                    <circle cx="60" cy="60" r="46" fill="none" stroke-width="15" stroke="{{ $s['color'] }}"
                        stroke-dasharray="{{ round(max($len - 1.5, 0.1), 2) }} {{ round($C, 2) }}" stroke-dashoffset="{{ round(-$offset, 2) }}"/>
                    @php $offset += $len; @endphp
                @endif
            @endforeach
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold leading-none">{{ $center ?? $total }}</span>
            <span class="mt-1 text-xs text-muted">{{ $label }}</span>
        </div>
    </div>
    <ul class="min-w-[170px] flex-1 space-y-3">
        @foreach ($segments as $s)
            <li class="flex items-center gap-2.5 text-[13px]">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $s['color'] }}"></span>
                <span class="flex-1 truncate text-ink/90">{{ $s['label'] }}</span>
                <span class="whitespace-nowrap font-medium text-ink">{{ $s['value'] }} <span class="text-muted">({{ $sum ? round($s['value'] / $sum * 100) : 0 }}%)</span></span>
            </li>
        @endforeach
    </ul>
</div>
