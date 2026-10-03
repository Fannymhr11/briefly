<x-layouts.app title="Creative Tasks">
    @php
    $role = auth()->user()->role;
    $t = $selected;
    $list = route($role.'.tasks');
    $order = ['pending', 'in_progress', 'completed'];
    $idx = $t ? array_search($t->status, $order) : 0;
    $next = $t && $idx < 2 ? $order[$idx + 1] : null;
    @endphp
    <x-page-header title="Creative Tasks" subtitle="Kelola dan kerjakan tugas konten yang telah disetujui. Update status sesuai progres pengerjaan."
        :crumbs="['Dashboard' => route($role.'.dashboard'), 'Creative Tasks' => null]" />

    <div class="grid gap-6 {{ $t ? '2xl:grid-cols-[minmax(0,1fr)_380px]' : '' }}">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <x-stat-card label="Total Task" icon="file-text" :value="$counts['total'][0]" :delta="$counts['total'][1]" deltaLabel="dari bulan lalu" />
                <x-stat-card label="Belum Dikerjakan" icon="clock" tone="amber" :value="$counts['pending'][0]" :delta="$counts['pending'][1]" :invert="true" />
                <x-stat-card label="Sedang Diproses" icon="loader" :value="$counts['in_progress'][0]" :delta="$counts['in_progress'][1]" />
                <x-stat-card label="Sudah Selesai" icon="check-circle" tone="green" :value="$counts['completed'][0]" :delta="$counts['completed'][1]" />
            </div>
            <x-card class="p-5">
                <x-task-filters :action="$list" :filters="$filters" />
                <div class="mt-5">
                    <x-task-table :tasks="$tasks" :assignee="$role !== 'creative'" :selected-id="$t?->id" />
                    <x-table-footer :paginator="$tasks" />
                </div>
            </x-card>
        </div>

        @if ($t)
            <aside class="space-y-4 2xl:sticky 2xl:top-20 2xl:self-start" aria-label="Detail task">
                <x-card class="p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-[15px] font-semibold"><a href="{{ $list }}" class="btn-icon border-0" aria-label="Kembali"><x-icon name="arrow-left" class="h-4 w-4" /></a>Detail Task</h2>
                        <a href="{{ $list }}" class="btn-icon border-0" aria-label="Tutup"><x-icon name="x" class="h-4 w-4" /></a>
                    </div>
                    <div class="mt-4 flex items-start justify-between gap-3">
                        <h3 class="text-[17px] font-bold leading-snug">{{ $t->brief->title }}</h3>
                        <x-status-badge :status="$t->status" type="task" />
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-4 text-[13px]">
                        <div><dt class="text-xs text-muted">Brand</dt><dd class="mt-1"><x-brand-avatar :brand="$t->brief->brand" :label="true" size="h-7 w-7" /></dd></div>
                        <div><dt class="text-xs text-muted">Platform</dt><dd class="mt-1"><x-platform-icon :platform="$t->brief->platform" :label="true" size="h-7 w-7" /></dd></div>
                        <div><dt class="text-xs text-muted">Pengirim</dt><dd class="mt-1 font-medium">{{ $t->brief->user->name }}</dd></div>
                        <div><dt class="text-xs text-muted">Approved</dt><dd class="mt-1 font-medium">{{ $t->created_at->translatedFormat('j M Y') }}</dd></div>
                    </dl>
                </x-card>

                <x-card class="p-5">
                    <h3 class="text-[14px] font-semibold">File Brief</h3>
                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-line p-3">
                        <span class="tile h-10 w-10 bg-rose-500/10 text-rose-500"><x-icon name="file-text" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1"><p class="truncate text-[13px] font-medium">{{ $t->brief->original_filename }}</p><p class="text-xs text-muted">{{ $t->brief->size_label }}</p></div>
                        <a href="{{ route('briefs.preview', $t->brief) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><x-icon name="eye" class="h-3.5 w-3.5" />Lihat</a>
                        <a href="{{ route('briefs.download', $t->brief) }}" class="btn-icon" aria-label="Unduh"><x-icon name="download" class="h-4 w-4" /></a>
                    </div>
                </x-card>

                <x-card class="p-5">
                    <h3 class="text-[14px] font-semibold">Progress Pengerjaan</h3>
                    <ol class="mt-4 flex items-start justify-between">
                        @foreach ($order as $i => $s)
                            <li class="relative flex flex-1 flex-col items-center text-center">
                                @if ($i > 0)<span class="absolute right-1/2 top-3.5 h-0.5 w-full {{ $i <= $idx ? 'bg-brand-500' : 'bg-line' }}"></span>@endif
                                <span class="relative z-10 flex h-7 w-7 items-center justify-center rounded-full border-2 {{ $i <= $idx ? 'border-brand-500 bg-brand-500 text-white' : 'border-line bg-surface text-muted' }}">
                                    @if ($i < $idx || $s === 'completed' && $i === $idx)<x-icon name="check" class="h-3.5 w-3.5" />@else<span class="h-2 w-2 rounded-full {{ $i === $idx ? 'bg-white' : 'bg-line' }}"></span>@endif
                                </span>
                                <span class="mt-2 text-[11px] font-medium {{ $i === $idx ? 'text-brand-600 dark:text-brand-300' : 'text-muted' }}">{{ \App\Models\CreativeTask::STATUSES[$s] }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if ($t->assignee)<p class="mt-4 text-xs text-muted">Dikerjakan oleh <span class="font-medium text-ink">{{ $t->assignee->name }}</span></p>@endif
                </x-card>

                <x-card class="p-5">
                    <x-card-header title="Aktivitas Terbaru" />
                    <div class="mt-4"><x-activity-list :items="$taskHistories" /></div>
                </x-card>

                @if ($canUpdate)
                    <x-card class="p-4">
                        <form method="POST" action="{{ route('creative.tasks.status', $t) }}" class="space-y-3">
                            @csrf @method('PATCH')
                            <x-select name="status" label="Ubah Status" :options="\App\Models\CreativeTask::STATUSES" :selected="$next ?? $t->status" />
                            <button type="submit" class="btn btn-primary h-11 w-full"><x-icon name="arrow-right" class="h-4 w-4" />Ubah Status</button>
                        </form>
                    </x-card>
                @else
                    <p class="card-soft px-4 py-3 text-xs text-muted">Hanya Tim Kreatif yang dapat mengubah status task.</p>
                @endif
            </aside>
        @endif
    </div>
</x-layouts.app>
