@props(['variant' => 'primary', 'href' => null, 'icon' => null, 'type' => 'button', 'size' => null])
@php
$v = ['primary' => 'btn-primary', 'outline' => 'btn-outline', 'soft' => 'btn-soft', 'danger' => 'btn-danger', 'danger-soft' => 'btn-danger-soft'][$variant] ?? 'btn-primary';
$cls = trim('btn '.$v.' '.($size === 'sm' ? 'btn-sm' : ''));
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$cls]) }}>@if ($icon)<x-icon :name="$icon" class="h-4 w-4" />@endif{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$cls]) }}>@if ($icon)<x-icon :name="$icon" class="h-4 w-4" />@endif{{ $slot }}</button>
@endif
