{{-- Buka dengan: $dispatch('open-modal', { name: 'nama', data: {...} }). Data tersedia sebagai `payload` di dalam slot. --}}
@props(['name', 'title' => null, 'titleBind' => null, 'subtitle' => null, 'maxWidth' => 'max-w-lg', 'show' => false, 'payload' => []])
<div x-data="{ open: @js((bool) $show), payload: @js($payload) }"
     x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') { payload = $event.detail.data || {}; open = true }"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}' || ($event.detail && $event.detail.name === '{{ $name }}')) open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
    <div x-show="open" x-transition.scale.95.opacity
         class="relative flex max-h-[92vh] w-full {{ $maxWidth }} flex-col overflow-hidden rounded-t-2xl border border-line bg-surface shadow-pop sm:rounded-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
            <div>
                <h3 class="text-base font-semibold" @if ($titleBind) x-text="{{ $titleBind }}" @endif>{{ $title }}</h3>
                @if ($subtitle)<p class="mt-0.5 text-[13px] text-muted">{{ $subtitle }}</p>@endif
            </div>
            <button type="button" @click="open = false" class="btn-icon -mr-1 -mt-1 border-0" aria-label="Tutup"><x-icon name="x" class="h-4 w-4" /></button>
        </div>
        <div class="overflow-y-auto px-5 py-5">{{ $slot }}</div>
        @if (isset($footer))<div class="flex flex-wrap items-center justify-end gap-2.5 border-t border-line bg-soft/50 px-5 py-3.5">{{ $footer }}</div>@endif
    </div>
</div>
