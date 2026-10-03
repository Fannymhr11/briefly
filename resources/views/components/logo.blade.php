{{-- Placeholder logo BRIEFLY — ganti isi file ini (atau pakai <img src="{{ asset('images/logo.svg') }}">) bila logo final sudah ada. --}}
@props(['size' => 'md', 'showText' => true, 'sub' => null, 'textClass' => ''])
@php $box = $size === 'lg' ? 'h-12 w-12' : 'h-9 w-9'; $text = $size === 'lg' ? 'text-2xl' : 'text-[19px]'; @endphp
<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 40 40" class="{{ $box }} shrink-0" aria-hidden="true">
        <rect width="40" height="40" rx="11" fill="#2563EB"/>
        <path d="M12 11.5h10.2a5 5 0 0 1 3.5 1.4l2.4 2.4a5 5 0 0 1 1.4 3.5V28a1.5 1.5 0 0 1-1.5 1.5H12A1.5 1.5 0 0 1 10.5 28V13A1.5 1.5 0 0 1 12 11.5Z" fill="#fff" fill-opacity=".95"/>
        <path d="M15 20.5h10M15 24.5h6" stroke="#2563EB" stroke-width="2" stroke-linecap="round"/>
        <circle cx="29.5" cy="11" r="4.5" fill="#93B8FF"/>
    </svg>
    @if ($showText)
        <span class="leading-none {{ $textClass }}">
            <span class="{{ $text }} font-bold tracking-tight text-ink">BRIEFLY</span>
            @if ($sub)<span class="mt-1 block text-[11px] font-medium text-muted">{{ $sub }}</span>@endif
        </span>
    @endif
</span>
