@php
$toasts = collect([
    ['success', session('success')],
    ['error', session('toast_error')],
    ['info', session('toast_info')],
])->filter(fn ($t) => $t[1]);
if ($errors->any() && ! request()->routeIs('login')) {
    $toasts->push(['error', 'Periksa kembali isian formulir Anda.']);
}
$styles = ['success' => ['check-circle', 'text-emerald-500'], 'error' => ['x-circle', 'text-rose-500'], 'info' => ['info', 'text-brand-500']];
@endphp
@if ($toasts->isNotEmpty())
    <div class="pointer-events-none fixed right-4 top-4 z-[60] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2.5" role="status" aria-live="polite">
        @foreach ($toasts as [$type, $text])
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                 class="pointer-events-auto flex items-start gap-3 rounded-xl border border-line bg-surface p-3.5 shadow-pop">
                <x-icon :name="$styles[$type][0]" class="mt-0.5 h-5 w-5 shrink-0 {{ $styles[$type][1] }}" />
                <p class="flex-1 text-[13px] font-medium text-ink">{{ $text }}</p>
                <button type="button" @click="show = false" class="text-muted hover:text-ink" aria-label="Tutup"><x-icon name="x" class="h-4 w-4" /></button>
            </div>
        @endforeach
    </div>
@endif
