@props(['role'])
@php
$map = [
    'admin' => ['Admin', 'badge-blue', 'user'],
    'user' => ['User', 'badge-amber', 'user'],
    'marketing' => ['Marketing Communication', 'badge-purple', 'send'],
    'creative' => ['Tim Kreatif', 'badge-green', 'palette'],
];
[$label, $cls, $icon] = $map[$role] ?? [$role, 'badge-gray', 'user'];
@endphp
<span {{ $attributes->class(['badge', $cls]) }}><x-icon :name="$icon" class="h-3.5 w-3.5" />{{ $label }}</span>
