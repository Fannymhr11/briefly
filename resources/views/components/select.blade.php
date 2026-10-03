@props(['name', 'options' => [], 'label' => null, 'selected' => null, 'placeholder' => null, 'required' => false, 'auto' => false])
@php $current = old($name, $selected); @endphp
<div>
    @if ($label)<label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-rose-500"> *</span>@endif</label>@endif
    <select id="{{ $name }}" name="{{ $name }}" @if ($auto) onchange="this.form.submit()" @endif
        {{ $attributes->class(['select', 'input-error' => $errors->has($name)]) }} @required($required)>
        @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected((string) $current === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>
    <x-input-error :for="$name" />
</div>
