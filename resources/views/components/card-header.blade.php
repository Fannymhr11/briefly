@props(['title', 'href' => null, 'linkLabel' => 'Lihat Semua'])
<div {{ $attributes->class(['flex items-center justify-between gap-3']) }}>
    <h2 class="text-[15px] font-semibold">{{ $title }}</h2>
    @if ($href)
        <a href="{{ $href }}" class="inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:underline dark:text-brand-300">{{ $linkLabel }} <x-icon name="arrow-right" class="h-3.5 w-3.5" /></a>
    @endif
    {{ $slot }}
</div>
