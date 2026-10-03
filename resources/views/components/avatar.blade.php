@props(['user', 'size' => 'h-9 w-9 text-xs'])
<span {{ $attributes->class(['inline-flex shrink-0 items-center justify-center rounded-full bg-brand-600 font-semibold text-white', $size]) }}>{{ $user->initials }}</span>
