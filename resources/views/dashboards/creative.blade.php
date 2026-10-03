<x-layouts.app title="Dashboard">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[26px] font-bold leading-tight">Selamat datang, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}! <span aria-hidden="true">👋</span></h1>
            <p class="mt-1 text-[13.5px] text-muted">Berikut adalah ringkasan tugas konten yang perlu kamu kerjakan.</p>
        </div>
        <span class="inline-flex items-center gap-2 text-[13px] text-muted"><x-icon name="calendar" class="h-4 w-4" />{{ now()->translatedFormat('l, j F Y') }}</span>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <x-stat-card label="Total Task" icon="file-text" :value="$counts['total']" sub="Semua task approved" :href="route('creative.tasks')" />
                <x-stat-card label="Belum Dikerjakan" icon="clock" tone="amber" :value="$counts['pending']" sub="Menunggu dikerjakan" :href="route('creative.tasks', ['status' => 'pending'])" />
                <x-stat-card label="Sedang Diproses" icon="loader" :value="$counts['in_progress']" sub="Sedang berjalan" :href="route('creative.tasks', ['status' => 'in_progress'])" />
                <x-stat-card label="Sudah Selesai" icon="check-circle" tone="green" :value="$counts['completed']" sub="Selesai dikerjakan" :href="route('creative.tasks', ['status' => 'completed'])" />
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-card class="p-5">
                    <x-card-header title="Progress Pengerjaan" />
                    <x-charts.donut class="mt-5" :segments="$segments" :center="$percent.'%'" label="Selesai" />
                </x-card>
                <x-card class="p-5">
                    <x-card-header title="Task Terbaru" :href="route('creative.tasks')" />
                    <ul class="mt-4 divide-y divide-line">
                        @forelse ($recentTasks as $t)
                            <li>
                                <a href="{{ route('creative.tasks.show', $t) }}" class="flex items-center gap-3 py-3 transition first:pt-0 last:pb-0 hover:opacity-80">
                                    <x-platform-icon :platform="$t->brief->platform" />
                                    <span class="min-w-0 flex-1 truncate text-[13px] font-medium">{{ $t->brief->title }}</span>
                                    <x-brand-avatar :brand="$t->brief->brand" class="hidden sm:inline-flex" />
                                    <span class="hidden whitespace-nowrap text-xs text-muted md:block">{{ $t->created_at->translatedFormat('j M Y') }}</span>
                                    <x-status-badge :status="$t->status" type="task" />
                                </a>
                            </li>
                        @empty
                            <li class="py-6 text-center text-[13px] text-muted">Belum ada task.</li>
                        @endforelse
                    </ul>
                </x-card>
            </div>

            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Daftar Creative Tasks</h2>
                <x-task-filters class="mt-4" :action="route('creative.dashboard')" :filters="$filters" />
                <div class="mt-4">
                    <x-task-table :tasks="$tasks" />
                    <x-table-footer :paginator="$tasks" />
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="flex items-center gap-2 text-[15px] font-semibold"><x-icon name="trend-up" class="h-4 w-4 text-brand-500" />Quick Stats</h2>
                <ul class="mt-4 space-y-3.5">
                    @foreach ([['Total Task', $counts['total'], 'file-text', 'tile-blue'], ['Belum Dikerjakan', $counts['pending'], 'clock', 'tile-amber'], ['Sedang Diproses', $counts['in_progress'], 'loader', 'tile-blue'], ['Sudah Selesai', $counts['completed'], 'check-circle', 'tile-green']] as [$l, $v, $ic, $tile])
                        <li class="flex items-center gap-3"><span class="tile h-10 w-10 {{ $tile }}"><x-icon :name="$ic" class="h-5 w-5" /></span><span class="flex-1 text-[13px] text-ink/90">{{ $l }}</span><span class="text-xl font-bold">{{ $v }}</span></li>
                    @endforeach
                </ul>
            </x-card>
            <x-card class="p-5">
                <x-card-header title="Aktivitas Terbaru" :href="route('creative.history')" />
                <div class="mt-4"><x-activity-list :items="$activities" /></div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
