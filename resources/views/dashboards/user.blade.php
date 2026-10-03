<x-layouts.app title="Dashboard">
    <div class="mb-6 grid gap-5 lg:grid-cols-[1fr_minmax(0,460px)] lg:items-center">
        <div>
            <p class="text-[15px] text-ink/80">Selamat datang,</p>
            <h1 class="text-[28px] font-bold leading-tight">{{ auth()->user()->name }} <span aria-hidden="true">👋</span></h1>
            <p class="mt-1 text-[13.5px] text-muted">Kelola brief konten Anda dengan mudah dan pantau statusnya di sini.</p>
        </div>
        <a href="{{ route('user.briefs.create') }}" class="group relative flex items-center gap-4 overflow-hidden rounded-2xl border border-line bg-soft p-4 transition hover:border-brand-300">
            <span class="tile tile-blue h-12 w-12 rounded-2xl bg-surface"><x-icon name="file-text" class="h-6 w-6" /></span>
            <span class="min-w-0 flex-1"><span class="block font-semibold">Upload Brief Konten</span><span class="block text-[12.5px] text-muted">Kirim brief konten Anda dalam bentuk file PDF</span></span>
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white transition group-hover:translate-x-0.5"><x-icon name="arrow-right" class="h-4 w-4" /></span>
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="min-w-0 space-y-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <x-stat-card label="Total Brief" icon="file-text" :value="$stats['briefs'][0]" :delta="$stats['briefs'][1]" :href="route('user.briefs.index')" />
                <x-stat-card label="Menunggu Review" icon="clock" tone="amber" :value="$stats['pending'][0]" :delta="$stats['pending'][1]" :href="route('user.briefs.index', ['status' => 'pending'])" />
                <x-stat-card label="Disetujui" icon="check-circle" tone="green" :value="$stats['approved'][0]" :delta="$stats['approved'][1]" :href="route('user.briefs.index', ['status' => 'approved'])" />
                <x-stat-card label="Ditolak" icon="x-circle" tone="red" :value="$stats['rejected'][0]" :delta="$stats['rejected'][1]" :invert="true" :href="route('user.briefs.index', ['status' => 'rejected'])" />
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                <x-card class="p-5 lg:col-span-3">
                    <x-card-header title="Tren Brief Saya"><span class="badge badge-gray">7 Hari Terakhir</span></x-card-header>
                    <div class="mt-3"><x-charts.line :labels="$trend['labels']" :values="$trend['values']" /></div>
                </x-card>
                <x-card class="p-5 lg:col-span-2">
                    <x-card-header title="Status Brief" />
                    <x-charts.donut class="mt-5" :segments="$segments" :total="$total" />
                </x-card>
            </div>

            <x-card class="p-5">
                <x-card-header title="Brief Terbaru" :href="route('user.briefs.index')" />
                <div class="mt-4"><x-brief-table :briefs="$recentBriefs" :sender="false" :note="true" :file-sub="false" empty="Anda belum mengirim brief" /></div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Proses Brief</h2>
                @php
                $steps = [
                    ['Upload Brief Konten', 'Kirim file brief dalam format PDF', true],
                    ['Review oleh Marketing Communication', 'Brief akan diperiksa dan diberi catatan', false],
                    ['Disetujui / Ditolak', 'Jika disetujui, masuk ke task tim kreatif', false],
                    ['Task Tim Kreatif', 'Proses pengerjaan konten', false],
                ];
                @endphp
                <ol class="relative mt-5 space-y-6">
                    <span class="absolute bottom-3 left-[13px] top-3 w-px bg-line" aria-hidden="true"></span>
                    @foreach ($steps as $i => [$t, $d, $done])
                        <li class="relative flex items-start gap-3.5">
                            <span @class(['relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold', 'bg-brand-600 text-white' => $i < 2, 'bg-soft text-muted' => $i >= 2])>{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1"><p class="text-[13px] font-semibold leading-snug">{{ $t }}</p><p class="mt-0.5 text-xs text-muted">{{ $d }}</p></div>
                            @if ($done)<x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />@endif
                        </li>
                    @endforeach
                </ol>
            </x-card>
            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Quick Access</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-quick-tile :href="route('user.briefs.create')" icon="upload" title="Upload Brief" desc="Kirim brief baru" />
                    <x-quick-tile :href="route('user.briefs.index')" icon="file-text" title="Brief Saya" desc="Lihat brief yang dikirim" />
                    <x-quick-tile :href="route('user.history')" icon="history" title="History" desc="Lihat riwayat aktivitas" />
                    <x-quick-tile :href="route('user.profile')" icon="user" title="Profile" desc="Kelola akun Anda" />
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
