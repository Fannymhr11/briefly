@props(['platform', 'size' => 'h-6 w-6', 'label' => false])
@php $isTiktok = $platform === 'tiktok'; @endphp
<span {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    @if ($isTiktok)
        <svg viewBox="0 0 24 24" class="{{ $size }} shrink-0" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#0B0B0F"/><path d="M13 6.2v7.4a2.5 2.5 0 1 1-2.5-2.5" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round"/><path d="M13 6.2c.3 1.7 1.4 2.9 3.2 3.1" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round"/></svg>
    @else
        <svg viewBox="0 0 24 24" class="{{ $size }} shrink-0" aria-hidden="true"><defs><linearGradient id="ig-grad" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#FEDA75"/><stop offset=".35" stop-color="#FA7E1E"/><stop offset=".62" stop-color="#D62976"/><stop offset="1" stop-color="#4F5BD5"/></linearGradient></defs><rect width="24" height="24" rx="6" fill="url(#ig-grad)"/><rect x="5.5" y="5.5" width="13" height="13" rx="4" fill="none" stroke="#fff" stroke-width="1.6"/><circle cx="12" cy="12" r="3.1" fill="none" stroke="#fff" stroke-width="1.6"/><circle cx="16" cy="8" r=".9" fill="#fff"/></svg>
    @endif
    @if ($label)<span class="text-[13px] text-ink/90">{{ \App\Models\Brief::PLATFORMS[$platform] ?? $platform }}</span>@endif
</span>
