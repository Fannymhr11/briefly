@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])
<div>
    @if ($label)<label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-rose-500"> *</span>@endif</label>@endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        {{ $attributes->class(['input', 'input-error' => $errors->has($name)]) }} @required($required)>
    @if ($hint)<p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>@endif
    <x-input-error :for="$name" />
</div>
