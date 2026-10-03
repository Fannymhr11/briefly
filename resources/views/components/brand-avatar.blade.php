@props(['brand', 'size' => 'h-6 w-6', 'label' => false])
@php
$styles = ['dthree' => 'bg-brand-600', 'hurim' => 'bg-slate-500', 'asfara' => 'bg-blue-900'];
$name = \App\Models\Brief::BRANDS[$brand] ?? $brand;
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    <span class="{{ $size }} {{ $styles[$brand] ?? 'bg-slate-500' }} inline-flex shrink-0 items-center justify-center rounded-full text-[11px] font-bold text-white">{{ strtoupper(substr($name, 0, 1)) }}</span>
    @if ($label)<span class="text-[13px] text-ink/90">{{ $name }}</span>@endif
</span>
