@props(['href', 'icon', 'title', 'desc' => null])
<a href="{{ $href }}" class="group rounded-2xl bg-soft p-4 transition hover:bg-brand-500/10">
    <span class="tile tile-blue h-10 w-10 rounded-full bg-surface"><x-icon :name="$icon" class="h-5 w-5" /></span>
    <span class="mt-3 block text-[13px] font-semibold leading-snug">{{ $title }}</span>
    @if ($desc)<span class="mt-0.5 block text-[11.5px] leading-snug text-muted">{{ $desc }}</span>@endif
</a>
