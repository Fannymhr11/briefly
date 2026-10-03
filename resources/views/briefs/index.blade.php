<x-layouts.app title="Semua Brief">
    @php $role = auth()->user()->role; @endphp
    <x-page-header title="Semua Brief" subtitle="Kelola dan pantau seluruh brief konten dari user hingga proses produksi."
        :crumbs="['Dashboard' => route($role.'.dashboard'), 'Semua Brief' => null]" />

    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Brief" icon="file-text" :value="$stats['total'][0]" :delta="$stats['total'][1]" />
        <x-stat-card label="Menunggu Review" icon="clock" tone="amber" :value="$stats['pending'][0]" :delta="$stats['pending'][1]" :invert="true" />
        <x-stat-card label="Disetujui" icon="check-circle" tone="green" :value="$stats['approved'][0]" :delta="$stats['approved'][1]" />
        <x-stat-card label="Ditolak" icon="x-circle" tone="red" :value="$stats['rejected'][0]" :delta="$stats['rejected'][1]" :invert="true" />
    </div>

    <x-card class="p-5">
        <x-brief-filters :action="route($role.'.briefs')" :filters="$filters" placeholder="Cari nama file, brand, platform, user…" />
        <div class="mt-5">
            <x-brief-table :briefs="$briefs" empty="Brief tidak ditemukan" />
            <x-table-footer :paginator="$briefs" />
        </div>
    </x-card>
</x-layouts.app>
