<x-layouts.app :title="$brief->title">
    @php
    $role = auth()->user()->role;
    $back = $role === 'user' ? route('user.briefs.index') : ($role === 'marketing' && $brief->status === 'pending' ? route('marketing.review') : route($role.'.briefs'));
    $backLabel = $role === 'user' ? 'Brief Saya' : ($role === 'marketing' && $brief->status === 'pending' ? 'Review Brief' : 'Semua Brief');
    $canReview = $role === 'marketing' && $brief->status === 'pending';
    $tiles = ['green' => 'tile-green', 'red' => 'tile-red', 'purple' => 'tile-purple', 'blue' => 'tile-blue', 'amber' => 'tile-amber'];
    @endphp

    <x-page-header :title="$brief->title" :subtitle="'Dikirim oleh '.$brief->user->name.' pada '.$brief->created_at->translatedFormat('j F Y, H:i')"
        :crumbs="['Dashboard' => route($role.'.dashboard'), $backLabel => $back, 'Detail Brief' => null]">
        <x-slot:actions>
            <x-status-badge :status="$brief->status" class="px-3 py-1.5 text-[13px]" />
            @if ($canReview)
                <x-button variant="danger-soft" icon="x-circle" @click="$dispatch('open-modal', { name: 'reject-brief' })">Tolak</x-button>
                <x-button icon="check-circle" @click="$dispatch('open-modal', { name: 'approve-brief' })">Setujui</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <x-file-preview :brief="$brief" height="h-[420px] sm:h-[640px]" class="min-w-0" />

        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Informasi Brief</h2>
                <dl class="mt-4 space-y-3.5 text-[13px]">
                    <div class="flex items-center justify-between gap-4"><dt class="text-muted">Brand</dt><dd><x-brand-avatar :brand="$brief->brand" :label="true" /></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-muted">Platform</dt><dd><x-platform-icon :platform="$brief->platform" :label="true" /></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-muted">Pengirim</dt><dd class="text-right font-medium">{{ $brief->user->name }}<span class="block text-xs font-normal text-muted">{{ $brief->user->role_label }}</span></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-muted">Tanggal upload</dt><dd class="font-medium">{{ $brief->created_at->translatedFormat('j M Y, H:i') }}</dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-muted">Status</dt><dd><x-status-badge :status="$brief->status" /></dd></div>
                    @if ($brief->reviewed_at)
                        <div class="flex items-center justify-between gap-4"><dt class="text-muted">Direview oleh</dt><dd class="text-right font-medium">{{ $brief->reviewer->name ?? '—' }}<span class="block text-xs font-normal text-muted">{{ $brief->reviewed_at->translatedFormat('j M Y, H:i') }}</span></dd></div>
                    @endif
                    @if ($brief->task)
                        <div class="flex items-center justify-between gap-4"><dt class="text-muted">Task kreatif</dt><dd><x-status-badge :status="$brief->task->status" type="task" /></dd></div>
                    @endif
                </dl>
            </x-card>

            @if ($brief->status === 'rejected' && $brief->rejection_note)
                <div class="rounded-2xl border border-rose-500/25 bg-rose-500/[0.06] p-5">
                    <h2 class="flex items-center gap-2 text-[15px] font-semibold text-rose-600 dark:text-rose-400"><x-icon name="x-circle" class="h-5 w-5" />Catatan Marketing Communication</h2>
                    <p class="mt-2.5 whitespace-pre-line text-[13.5px] leading-relaxed text-ink/90">{{ $brief->rejection_note }}</p>
                </div>
            @elseif ($brief->status === 'approved')
                <div class="rounded-2xl border border-emerald-500/25 bg-emerald-500/[0.06] p-5">
                    <h2 class="flex items-center gap-2 text-[15px] font-semibold text-emerald-600 dark:text-emerald-400"><x-icon name="check-circle" class="h-5 w-5" />Brief disetujui</h2>
                    <p class="mt-2 text-[13px] text-ink/90">Brief ini sudah masuk ke daftar task Tim Kreatif.</p>
                </div>
            @elseif ($brief->status === 'pending')
                <div class="rounded-2xl border border-amber-500/25 bg-amber-500/[0.06] p-5">
                    <h2 class="flex items-center gap-2 text-[15px] font-semibold text-amber-600 dark:text-amber-400"><x-icon name="clock" class="h-5 w-5" />Menunggu review</h2>
                    <p class="mt-2 text-[13px] text-ink/90">Brief sedang menunggu ditinjau oleh Marketing Communication.</p>
                </div>
            @endif

            <x-card class="p-5">
                <h2 class="text-[15px] font-semibold">Riwayat Brief</h2>
                <div class="mt-4"><x-activity-list :items="$histories" /></div>
            </x-card>
        </div>
    </div>

    @if ($canReview)
        <x-modal name="approve-brief" title="Setujui brief?" max-width="max-w-md">
            <div class="flex items-start gap-3.5">
                <span class="tile tile-green h-11 w-11 rounded-full"><x-icon name="check-circle" class="h-6 w-6" /></span>
                <div>
                    <p class="text-[14px] font-medium">Apakah Anda yakin ingin menyetujui brief ini?</p>
                    <p class="mt-1.5 text-[13px] text-muted">Brief <strong class="text-ink">{{ $brief->title }}</strong> akan otomatis masuk ke task Tim Kreatif.</p>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'approve-brief')">Batal</button>
                <form method="POST" action="{{ route('marketing.briefs.approve', $brief) }}">@csrf<button type="submit" class="btn btn-primary">Ya, Setujui</button></form>
            </x-slot:footer>
        </x-modal>

        <x-modal name="reject-brief" title="Tolak brief" subtitle="Catatan wajib diisi dan akan terlihat oleh User." :show="$errors->has('rejection_note')">
            <form method="POST" action="{{ route('marketing.briefs.reject', $brief) }}" id="reject-form">
                @csrf
                <label for="rejection_note" class="label">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea id="rejection_note" name="rejection_note" rows="5" required minlength="5" maxlength="1000"
                    placeholder="Tuliskan alasan penolakan atau hal yang perlu direvisi…" @class(['textarea', 'input-error' => $errors->has('rejection_note')])>{{ old('rejection_note') }}</textarea>
                <x-input-error for="rejection_note" />
            </form>
            <x-slot:footer>
                <button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'reject-brief')">Batal</button>
                <button type="submit" form="reject-form" class="btn btn-danger">Tolak Brief</button>
            </x-slot:footer>
        </x-modal>
    @endif
</x-layouts.app>
