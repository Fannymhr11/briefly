<x-layouts.app title="Upload Brief">
    <x-page-header title="Upload Brief Konten" icon="file-text" subtitle="Kirim brief konten Anda dalam bentuk file PDF. Pastikan semua data yang diperlukan sudah sesuai." />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <x-card class="p-5 sm:p-6">
            <form method="POST" action="{{ route('user.briefs.store') }}" enctype="multipart/form-data" x-data="pdfDropzone()" class="space-y-6">
                @csrf
                <div>
                    <label class="label text-[15px]">File Brief PDF <span class="text-rose-500">*</span></label>
                    <p class="mb-3 text-[13px] text-muted">Unggah file brief dalam format PDF (maks. 10MB).</p>
                    <label for="file" @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="drop($event)"
                        :class="over ? 'dropzone-active' : ''"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-brand-300/60 bg-soft/50 px-4 py-12 text-center transition hover:border-brand-500">
                        <x-icon name="cloud-upload" class="h-12 w-12 text-brand-500" />
                        <span class="mt-3 text-sm font-medium">Klik untuk mengunggah file atau drag &amp; drop</span>
                        <span class="mt-1 text-xs text-muted">PDF (maks. 10MB)</span>
                    </label>
                    <input x-ref="input" id="file" name="file" type="file" accept="application/pdf,.pdf" class="sr-only" @change="pick($event.target.files)">

                    <div x-show="file" x-cloak class="mt-3 flex items-center gap-3 rounded-xl border border-line bg-surface p-3">
                        <span class="tile h-10 w-10 bg-rose-500/10 text-rose-500"><x-icon name="file-text" class="h-5 w-5" /></span>
                        <div class="min-w-0 flex-1"><p class="truncate text-[13px] font-medium" x-text="file?.name"></p><p class="text-xs text-muted" x-text="file?.size"></p></div>
                        <button type="button" @click="clear()" class="btn-icon border-0" aria-label="Hapus file"><x-icon name="x" class="h-4 w-4" /></button>
                    </div>
                    <p x-show="error" x-cloak x-text="error" class="mt-1.5 text-xs font-medium text-rose-500"></p>
                    <x-input-error for="file" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-select name="platform" label="Platform" :options="\App\Models\Brief::PLATFORMS" placeholder="Pilih platform" :required="true" />
                    <x-select name="brand" label="Brand" :options="\App\Models\Brief::BRANDS" placeholder="Pilih brand" :required="true" />
                </div>

                <div class="rounded-2xl border border-line bg-soft p-4">
                    <p class="flex items-center gap-2 text-[13px] font-semibold"><x-icon name="info" class="h-4 w-4 text-brand-500" />Informasi Penting</p>
                    <ul class="mt-2 list-disc space-y-1 pl-9 text-[13px] text-muted">
                        <li>File harus berformat PDF</li><li>Ukuran file maksimal 10MB</li><li>Platform dan brand wajib dipilih</li>
                    </ul>
                </div>

                <div class="flex justify-end"><x-button type="submit" icon="send">Kirim Brief</x-button></div>
            </form>
        </x-card>

        <div class="space-y-6">
            <x-card class="p-5">
                <div class="rounded-2xl bg-soft p-4">
                    <p class="flex items-center gap-2 text-[14px] font-semibold"><x-icon name="file-text" class="h-4 w-4 text-brand-500" />Proses Setelah Upload</p>
                    <p class="mt-1.5 text-[12.5px] text-muted">Setelah brief dikirim, status akan berubah menjadi</p>
                    <x-status-badge status="pending" class="mt-2.5" />
                </div>
                <p class="mb-2 mt-5 flex items-center gap-2 text-[14px] font-semibold"><x-icon name="upload" class="h-4 w-4 text-brand-500" />Pilihan Platform</p>
                <ul class="space-y-2">@foreach (\App\Models\Brief::PLATFORMS as $k => $l)<li class="flex items-center gap-3 rounded-xl border border-line px-3 py-2.5 text-[13px] font-medium"><x-platform-icon :platform="$k" />{{ $l }}</li>@endforeach</ul>
                <p class="mb-2 mt-5 flex items-center gap-2 text-[14px] font-semibold"><x-icon name="tag" class="h-4 w-4 text-brand-500" />Pilihan Brand</p>
                <ul class="space-y-2">@foreach (\App\Models\Brief::BRANDS as $k => $l)<li class="flex items-center gap-3 rounded-xl border border-line px-3 py-2.5 text-[13px] font-medium"><x-brand-avatar :brand="$k" size="h-7 w-7" />{{ $l }}</li>@endforeach</ul>
            </x-card>
            <div class="card-soft p-5 text-center"><x-icon name="send" class="mx-auto h-7 w-7 text-brand-500" /><p class="mt-2 text-[13px] italic text-muted">Konten yang baik berawal dari brief yang jelas.</p></div>
        </div>
    </div>
</x-layouts.app>
