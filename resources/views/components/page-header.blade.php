@props(['title', 'subtitle' => null, 'icon' => null, 'crumbs' => []])
<div class="mb-6">
    @if (count($crumbs))
        <nav class="mb-3 flex items-center gap-1.5 text-xs text-muted" aria-label="Breadcrumb">
            @foreach ($crumbs as $label => $url)
                @if ($url)<a href="{{ $url }}" class="hover:text-ink">{{ $label }}</a>@else<span class="text-ink/80">{{ $label }}</span>@endif
                @if (! $loop->last)<x-icon name="chevron-right" class="h-3 w-3" />@endif
            @endforeach
        </nav>
    @endif
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex min-w-0 items-start gap-3.5">
            @if ($icon)<span class="tile tile-blue mt-0.5 h-11 w-11 rounded-2xl"><x-icon :name="$icon" class="h-5 w-5" /></span>@endif
            <div class="min-w-0">
                <h1 class="text-[26px] font-bold leading-tight">{{ $title }}</h1>
                @if ($subtitle)<p class="mt-1 text-[13.5px] text-muted">{{ $subtitle }}</p>@endif
            </div>
        </div>
        @if (isset($actions))<div class="flex flex-wrap items-center gap-2.5">{{ $actions }}</div>@endif
    </div>
</div>
