<x-layouts.app title="Brief Saya">
    <x-page-header title="Brief Saya" subtitle="Lihat semua brief konten yang sudah Anda kirimkan beserta statusnya.">
        <x-slot:actions><x-button icon="upload" :href="route('user.briefs.create')">Upload Brief</x-button></x-slot:actions>
    </x-page-header>

    <div class="card-soft mb-6 flex items-center gap-4 p-5">
        <span class="tile tile-blue h-14 w-14 rounded-2xl bg-surface"><x-icon name="file-text" class="h-7 w-7" /></span>
        <div><p class="text-[13px] font-semibold">Total Brief Anda</p><p class="text-3xl font-bold leading-tight">{{ $total }}</p><p class="text-xs text-muted">brief telah dikirim</p></div>
        <p class="ml-auto hidden text-right text-[13px] italic text-muted sm:block">Terus berkarya<br>dan buat konten terbaik!</p>
    </div>

    <x-card class="p-5">
        <x-brief-filters :action="route('user.briefs.index')" :filters="$filters" :dates="false" placeholder="Cari brief…" />
        <div class="mt-5">
            <x-brief-table :briefs="$briefs" :sender="false" :note="true" empty="Belum ada brief" />
            <x-table-footer :paginator="$briefs" />
        </div>
    </x-card>
</x-layouts.app>
