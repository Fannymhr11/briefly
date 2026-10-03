@props(['align' => 'right', 'width' => 'w-56'])
<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
    <div @click="open = !open">{{ $trigger }}</div>
    <div x-show="open" x-cloak x-transition.origin.top.opacity
         class="absolute z-40 mt-2 {{ $width }} {{ $align === 'left' ? 'left-0' : 'right-0' }} rounded-xl border border-line bg-surface p-1.5 shadow-pop">
        {{ $slot }}
    </div>
</div>
