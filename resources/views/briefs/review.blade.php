<x-layouts.app title="Review Brief">
    @php
    $q = fn (array $extra) => route('marketing.review', array_filter(array_merge(request()->only('q', 'platform', 'brand', 'status', 'from', 'to'), $extra), fn ($v) => $v !== null && $v !== ''));
    $statusTone = ['pending' => 'bg-amber-500', 'approved' => 'bg-emerald-500', 'rejected' => 'bg-rose-500'];
    @endphp
    <x-page-header title="Review Brief" subtitle="Kelola dan tinjau brief konten yang masuk sebelum disetujui atau ditolak." />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <x-stat-card label="Total Brief" icon="file-text" :value="$stats['total'][0]" :delta="$stats['total'][1]" />
                <x-stat-card label="Menunggu Review" icon="clock" tone="red" :value="$stats['pending'][0]" :delta="$stats['pending'][1]" :invert="true" />
                <x-stat-card label="Disetujui" icon="check-circle" tone="green" :value="$stats['approved'][0]" :delta="$stats['approved'][1]" />
                <x-stat-card label="Ditolak" icon="x-circle" tone="red" :value="$stats['rejected'][0]" :delta="$stats['rejected'][1]" :invert="true" />
            </div>

            <x-card class="p-4">
                <x-brief-filters :action="route('marketing.review')" :filters="$filters" placeholder="Cari nama brief, brand, platform…"
                    :status-options="['pending' => 'Menunggu Review', 'all' => 'Semua Status', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']" />
            </x-card>

            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Daftar Brief</h2>
                <p class="mb-4 mt-0.5 text-xs text-muted">Menampilkan {{ $briefs->count() }} dari {{ $briefs->total() }} data</p>
                <x-brief-table :briefs="$briefs" action="review" empty="Tidak ada brief yang menunggu review" />
                <x-table-footer :paginator="$briefs" />
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="flex items-center gap-2 text-[15px] font-semibold"><x-icon name="sliders" class="h-4 w-4 text-brand-500" />Filter Cepat</h2>

                <p class="mb-2 mt-5 text-xs font-semibold text-ink/80">Status Brief</p>
                <ul class="space-y-1.5">
                    @foreach (\App\Models\Brief::STATUSES as $k => $label)
                        <li><a href="{{ $q(['status' => $k, 'page' => null]) }}" @class(['flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] transition hover:bg-soft', 'bg-soft font-medium' => ($filters['status'] ?? '') === $k])>
                            <span class="h-2 w-2 rounded-full {{ $statusTone[$k] }}"></span><span class="flex-1">{{ $label }}</span><span class="font-semibold">{{ $byStatus[$k] }}</span></a></li>
                    @endforeach
                </ul>

                <hr class="my-4 border-line">
                <p class="mb-2 text-xs font-semibold text-ink/80">Platform</p>
                <ul class="space-y-1.5">
                    @foreach (\App\Models\Brief::PLATFORMS as $k => $label)
                        <li><a href="{{ $q(['platform' => $k, 'page' => null]) }}" @class(['flex items-center gap-2.5 rounded-lg px-3 py-1.5 text-[13px] transition hover:bg-soft', 'bg-soft font-medium' => ($filters['platform'] ?? '') === $k])>
                            <x-platform-icon :platform="$k" size="h-5 w-5" /><span class="flex-1">{{ $label }}</span><span class="font-semibold">{{ $byPlatform[$k] }}</span></a></li>
                    @endforeach
                </ul>

                <hr class="my-4 border-line">
                <p class="mb-2 text-xs font-semibold text-ink/80">Brand</p>
                <ul class="space-y-1.5">
                    @foreach (\App\Models\Brief::BRANDS as $k => $label)
                        <li><a href="{{ $q(['brand' => $k, 'page' => null]) }}" @class(['flex items-center gap-2.5 rounded-lg px-3 py-1.5 text-[13px] transition hover:bg-soft', 'bg-soft font-medium' => ($filters['brand'] ?? '') === $k])>
                            <x-brand-avatar :brand="$k" size="h-5 w-5" /><span class="flex-1">{{ $label }}</span><span class="font-semibold">{{ $byBrand[$k] }}</span></a></li>
                    @endforeach
                </ul>
            </x-card>

            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Aksi Cepat</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-quick-tile :href="route('marketing.briefs')" icon="file-text" title="Lihat Semua Brief" desc="Kelola semua brief konten" />
                    <x-quick-tile :href="route('marketing.tasks')" icon="check-square" title="Lihat Creative Tasks" desc="Pantau progres task kreatif" />
                    <x-quick-tile :href="route('marketing.history')" icon="history" title="Lihat History" desc="Riwayat aktivitas sistem" />
                    <x-quick-tile :href="route('marketing.profile')" icon="user" title="Profile" desc="Kelola profil akun" />
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
