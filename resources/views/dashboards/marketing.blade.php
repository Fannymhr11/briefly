<x-layouts.app title="Dashboard">
    @php $first = \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first(); @endphp
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[26px] font-bold leading-tight">Selamat datang, {{ $first }}! <span aria-hidden="true">👋</span></h1>
            <p class="mt-1 text-[13.5px] text-muted">Berikut adalah ringkasan aktivitas dan performa konten di Dthree.</p>
        </div>
        <span class="inline-flex items-center gap-2 text-[13px] text-muted"><x-icon name="calendar" class="h-4 w-4" />{{ now()->translatedFormat('l, j F Y') }}</span>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 2xl:grid-cols-5">
                <x-stat-card label="Total Brief" icon="file-text" :value="$stats['briefs'][0]" :delta="$stats['briefs'][1]" :href="route('marketing.briefs')" />
                <x-stat-card label="Menunggu Review" icon="clock" tone="amber" :value="$stats['pending'][0]" :delta="$stats['pending'][1]" :invert="true" :href="route('marketing.review')" />
                <x-stat-card label="Disetujui" icon="check-circle" tone="green" :value="$stats['approved'][0]" :delta="$stats['approved'][1]" :href="route('marketing.briefs', ['status' => 'approved'])" />
                <x-stat-card label="Ditolak" icon="x-circle" tone="red" :value="$stats['rejected'][0]" :delta="$stats['rejected'][1]" :invert="true" :href="route('marketing.briefs', ['status' => 'rejected'])" />
                <x-stat-card label="Task Kreatif" icon="check-square" tone="green" :value="$stats['tasks'][0]" :delta="$stats['tasks'][1]" :href="route('marketing.tasks')" />
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                <x-card class="p-5 lg:col-span-3">
                    <x-card-header title="Tren Brief Konten"><span class="badge badge-gray">7 Hari Terakhir</span></x-card-header>
                    <div class="mt-3"><x-charts.line :labels="$trend['labels']" :values="$trend['values']" /></div>
                </x-card>
                <x-card class="p-5 lg:col-span-2">
                    <x-card-header title="Status Brief" />
                    <x-charts.donut class="mt-5" :segments="$segments" :total="$total" />
                </x-card>
            </div>

            <x-card class="p-5">
                <x-card-header title="Brief Menunggu Review" :href="route('marketing.review')" />
                <div class="mt-4">
                    @if ($waiting->isEmpty())
                        <x-empty-state title="Tidak ada brief yang menunggu review" description="Semua brief sudah ditinjau. Kerja bagus!" icon="check-circle" class="py-8" />
                    @else
                        <x-brief-table :briefs="$waiting" action="review" :file-sub="false" />
                    @endif
                </div>
            </x-card>

            <x-card class="p-5">
                <x-card-header title="Brief Terbaru" :href="route('marketing.briefs')" />
                <div class="mt-4"><x-brief-table :briefs="$recentBriefs" action="detail" :file-sub="false" /></div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-5">
                <x-card-header title="Aktivitas Terbaru" :href="route('marketing.history')" />
                <div class="mt-4"><x-activity-list :items="$activities" /></div>
            </x-card>
            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Aksi Cepat</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-quick-tile :href="route('marketing.review')" icon="upload" title="Review Brief" desc="Kelola brief yang menunggu review" />
                    <x-quick-tile :href="route('marketing.briefs')" icon="file-text" title="Lihat Semua Brief" desc="Kelola semua brief konten" />
                    <x-quick-tile :href="route('marketing.tasks')" icon="check-square" title="Lihat Creative Tasks" desc="Pantau progres task kreatif" />
                    <x-quick-tile :href="route('marketing.history')" icon="history" title="Lihat History" desc="Riwayat aktivitas sistem" />
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
