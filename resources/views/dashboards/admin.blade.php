<x-layouts.app title="Dashboard">
    @php $first = \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first(); @endphp
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[26px] font-bold leading-tight">Selamat datang, {{ $first }}! <span aria-hidden="true">👋</span></h1>
            <p class="mt-1 text-[13.5px] text-muted">Pantau aktivitas dan perkembangan konten BRIEFLY.</p>
        </div>
        <span class="inline-flex items-center gap-2 text-[13px] text-muted"><x-icon name="calendar" class="h-4 w-4" />{{ now()->translatedFormat('l, j F Y') }}</span>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 2xl:grid-cols-6">
                <x-stat-card label="Total User" icon="users" :value="$stats['users'][0]" :delta="$stats['users'][1]" :href="route('admin.users.index')" />
                <x-stat-card label="Total Brief" icon="file-text" :value="$stats['briefs'][0]" :delta="$stats['briefs'][1]" :href="route('admin.briefs')" />
                <x-stat-card label="Menunggu Review" icon="clock" tone="amber" :value="$stats['pending'][0]" :delta="$stats['pending'][1]" :invert="true" :href="route('admin.briefs', ['status' => 'pending'])" />
                <x-stat-card label="Disetujui" icon="check-circle" tone="green" :value="$stats['approved'][0]" :delta="$stats['approved'][1]" :href="route('admin.briefs', ['status' => 'approved'])" />
                <x-stat-card label="Ditolak" icon="x-circle" tone="red" :value="$stats['rejected'][0]" :delta="$stats['rejected'][1]" :invert="true" :href="route('admin.briefs', ['status' => 'rejected'])" />
                <x-stat-card label="Task Kreatif" icon="check-square" :value="$stats['tasks'][0]" :sub="$stats['tasks'][1].' selesai'" :href="route('admin.tasks')" />
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                <x-card class="p-5 lg:col-span-3">
                    <x-card-header title="Tren Brief Mingguan"><span class="badge badge-gray">7 Hari Terakhir</span></x-card-header>
                    <div class="mt-3"><x-charts.line :labels="$trend['labels']" :values="$trend['values']" /></div>
                </x-card>
                <x-card class="p-5 lg:col-span-2">
                    <x-card-header title="Status Brief" />
                    <x-charts.donut class="mt-5" :segments="$segments" :total="$total" />
                </x-card>
            </div>

            <x-card class="p-5">
                <x-card-header title="Brief Terbaru" :href="route('admin.briefs')" />
                <div class="mt-4"><x-brief-table :briefs="$recentBriefs" /></div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-5">
                <x-card-header title="Aktivitas Terbaru" :href="route('admin.history')" />
                <div class="mt-4"><x-activity-list :items="$activities" /></div>
            </x-card>
            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Quick Actions</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-quick-tile :href="route('admin.users.index', ['tambah' => 1])" icon="users" title="Tambah User" desc="Kelola akun pengguna sistem" />
                    <x-quick-tile :href="route('admin.briefs')" icon="file-text" title="Lihat Semua Brief" desc="Kelola dan pantau brief" />
                    <x-quick-tile :href="route('admin.tasks')" icon="check-square" title="Lihat Creative Tasks" desc="Pantau progres task kreatif" />
                    <x-quick-tile :href="route('admin.history')" icon="history" title="Lihat History" desc="Riwayat aktivitas sistem" />
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
