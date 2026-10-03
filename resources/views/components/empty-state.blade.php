@props(['title' => 'Belum ada data', 'description' => null, 'icon' => 'inbox'])
<div {{ $attributes->class(['flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <span class="tile tile-blue mb-4 h-14 w-14 rounded-2xl"><x-icon :name="$icon" class="h-7 w-7" /></span>
    <p class="text-sm font-semibold text-ink">{{ $title }}</p>
    @if ($description)<p class="mt-1 max-w-sm text-[13px] text-muted">{{ $description }}</p>@endif
    @if (! $slot->isEmpty())<div class="mt-4">{{ $slot }}</div>@endif
</div>
