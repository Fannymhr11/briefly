{{-- Harus berada di dalam <form method="GET"> --}}
@props(['from' => null, 'to' => null])
@php
$label = ($from || $to)
    ? (($from ? \Illuminate\Support\Carbon::parse($from)->translatedFormat('j M Y') : '…').' – '.($to ? \Illuminate\Support\Carbon::parse($to)->translatedFormat('j M Y') : '…'))
    : 'Semua Tanggal';
@endphp
<x-dropdown align="right" width="w-72">
    <x-slot:trigger>
        <button type="button" class="btn btn-outline w-full justify-between font-normal sm:w-auto">
            <span class="flex items-center gap-2"><x-icon name="calendar" class="h-4 w-4 text-muted" />{{ $label }}</span>
            <x-icon name="chevron-down" class="h-4 w-4 text-muted" />
        </button>
    </x-slot:trigger>
    <div class="space-y-3 p-2.5">
        <div><label class="label text-xs">Dari tanggal</label><input type="date" name="from" value="{{ $from }}" class="input"></div>
        <div><label class="label text-xs">Sampai tanggal</label><input type="date" name="to" value="{{ $to }}" class="input"></div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-1">Terapkan</button>
            <button type="button" class="btn btn-outline btn-sm flex-1" onclick="const f=this.closest('form');f.querySelector('[name=from]').value='';f.querySelector('[name=to]').value='';f.submit()">Reset</button>
        </div>
    </div>
</x-dropdown>
