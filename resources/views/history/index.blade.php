<x-layouts.app title="History">
    @php $role = auth()->user()->role; $tiles = ['green' => 'tile-green', 'red' => 'tile-red', 'purple' => 'tile-purple', 'blue' => 'tile-blue', 'amber' => 'tile-amber']; @endphp
    <x-page-header title="History" icon="history" subtitle="Lihat seluruh aktivitas, mulai dari upload brief, proses review, hingga penyelesaian tugas." />

    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($summary as [$label, $value, $icon])
            @php [$ic, $tn] = match ($icon) { 'clock-amber' => ['clock', 'amber'], 'check-green' => ['check-circle', 'green'], 'x-red' => ['x-circle', 'red'], 'loader' => ['loader', 'blue'], default => ['file-text', 'blue'] }; @endphp
            <x-stat-card :label="$label" :value="$value" :icon="$ic" :tone="$tn" />
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        <x-card class="h-fit p-5">
            <h2 class="flex items-center gap-2 text-[15px] font-semibold"><x-icon name="sliders" class="h-4 w-4 text-brand-500" />Filter</h2>
            <form method="GET" action="{{ route($role.'.history') }}" class="mt-4 space-y-4">
                <div class="search-wrap"><x-icon name="search" /><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari aktivitas…" class="input"></div>
                <div><label class="label text-xs">Jenis Aktivitas</label>
                    <select name="action" class="select" onchange="this.form.submit()"><option value="">Semua Aktivitas</option>@foreach ($actions as $k => $l)<option value="{{ $k }}" @selected(($filters['action'] ?? '') === $k)>{{ $l }}</option>@endforeach</select></div>
                @if ($users->isNotEmpty())
                    <div><label class="label text-xs">User</label>
                        <select name="user" class="select" onchange="this.form.submit()"><option value="">Semua User</option>@foreach ($users as $u)<option value="{{ $u->id }}" @selected((string) ($filters['user'] ?? '') === (string) $u->id)>{{ $u->name }}</option>@endforeach</select></div>
                @endif
                <div><label class="label text-xs">Platform</label>
                    <select name="platform" class="select" onchange="this.form.submit()"><option value="">Semua Platform</option>@foreach (\App\Models\Brief::PLATFORMS as $k => $l)<option value="{{ $k }}" @selected(($filters['platform'] ?? '') === $k)>{{ $l }}</option>@endforeach</select></div>
                <div><label class="label text-xs">Brand</label>
                    <select name="brand" class="select" onchange="this.form.submit()"><option value="">Semua Brand</option>@foreach (\App\Models\Brief::BRANDS as $k => $l)<option value="{{ $k }}" @selected(($filters['brand'] ?? '') === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="label text-xs">Dari</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input px-2.5 text-xs" onchange="this.form.submit()"></div>
                    <div><label class="label text-xs">Sampai</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input px-2.5 text-xs" onchange="this.form.submit()"></div>
                </div>
                <a href="{{ route($role.'.history') }}" class="btn btn-outline w-full"><x-icon name="loader" class="h-4 w-4" />Reset Filter</a>
            </form>
        </x-card>

        <x-card class="p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-[15px] font-semibold"><x-icon name="history" class="h-4 w-4 text-brand-500" />Riwayat Aktivitas</h2>
                <span class="text-xs text-muted">Menampilkan {{ $histories->count() }} dari {{ $histories->total() }} data</span>
            </div>
            @if ($histories->isEmpty())
                <x-empty-state title="Belum ada aktivitas" description="Coba ubah atau reset filter pencarian." icon="history" />
            @else
                <ul class="relative mt-5 divide-y divide-line overflow-hidden rounded-2xl border border-line">
                    @foreach ($histories as $h)
                        <li class="flex flex-wrap items-center gap-x-4 gap-y-2 p-4 sm:flex-nowrap">
                            <span class="tile h-10 w-10 rounded-full {{ $tiles[$h->tone] ?? 'tile-blue' }}"><x-icon :name="$h->icon" class="h-5 w-5" /></span>
                            <div class="min-w-0 flex-1 basis-60">
                                <p class="text-[13.5px] font-semibold leading-snug">{{ $h->description }}</p>
                                <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted">
                                    @if ($h->user)<span>{{ $h->user->name }}</span>@endif
                                    @if ($h->brief)<x-platform-icon :platform="$h->brief->platform" size="h-4 w-4" :label="true" class="text-xs" /><x-brand-avatar :brand="$h->brief->brand" size="h-4 w-4" :label="true" />@endif
                                </div>
                            </div>
                            @if ($h->brief)<x-status-badge :status="$h->brief->status" class="hidden md:inline-flex" />@endif
                            <time class="flex shrink-0 items-center gap-2 text-xs text-muted" datetime="{{ $h->created_at->toIso8601String() }}"><x-icon name="calendar" class="h-4 w-4" /><span>{{ $h->created_at->translatedFormat('j M Y') }}<br>{{ $h->created_at->format('H:i') }}</span></time>
                        </li>
                    @endforeach
                </ul>
            @endif
            <x-table-footer :paginator="$histories" />
        </x-card>
    </div>
</x-layouts.app>
