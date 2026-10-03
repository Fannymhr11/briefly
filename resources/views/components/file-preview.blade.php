@props(['brief', 'height' => 'h-[560px]'])
<div {{ $attributes->class(['card overflow-hidden']) }}>
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-3">
        <div class="flex min-w-0 items-center gap-3">
            <span class="tile h-9 w-9 bg-rose-500/10 text-rose-500"><x-icon name="file-text" class="h-[18px] w-[18px]" /></span>
            <div class="min-w-0">
                <p class="truncate text-[13px] font-semibold">{{ $brief->original_filename }}</p>
                <p class="text-xs text-muted">PDF • {{ $brief->size_label }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-button variant="outline" size="sm" icon="eye" :href="route('briefs.preview', $brief)" target="_blank" rel="noopener">Buka</x-button>
            <x-button variant="soft" size="sm" icon="download" :href="route('briefs.download', $brief)">Unduh</x-button>
        </div>
    </div>
    <iframe src="{{ route('briefs.preview', $brief) }}#view=FitH" title="Preview {{ $brief->original_filename }}" class="block w-full bg-soft {{ $height }}" loading="lazy"></iframe>
</div>
