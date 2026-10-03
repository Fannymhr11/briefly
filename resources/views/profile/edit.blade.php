<x-layouts.app title="Profile">
    @php $role = $user->role; @endphp
    <x-page-header title="Profile" subtitle="Kelola informasi akun dan preferensi tampilan Anda." />

    <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <x-card class="h-fit p-6 text-center">
            <x-avatar :user="$user" size="mx-auto h-20 w-20 text-2xl" />
            <h2 class="mt-4 text-lg font-bold">{{ $user->name }}</h2>
            <p class="text-[13px] text-muted">{{ $user->email }}</p>
            <x-role-badge :role="$user->role" class="mt-3" />
            <dl class="mt-5 space-y-2 border-t border-line pt-4 text-left text-[13px]">
                <div class="flex justify-between"><dt class="text-muted">Bergabung</dt><dd class="font-medium">{{ $user->created_at->translatedFormat('j F Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Status</dt><dd><span class="badge badge-green badge-dot">Aktif</span></dd></div>
            </dl>
        </x-card>

        <div class="space-y-6">
            <x-card class="p-6">
                <h2 class="text-[15px] font-semibold">Edit Profile</h2>
                <form method="POST" action="{{ route($role.'.profile.update') }}" class="mt-5 space-y-4">
                    @csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="name" label="Nama" :value="$user->name" :required="true" />
                        <x-input name="email" type="email" label="Email" :value="$user->email" :required="true" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="label">Role</label><input class="input bg-soft" value="{{ $user->role_label }}" disabled></div>
                        <div><label class="label">Tanggal Bergabung</label><input class="input bg-soft" value="{{ $user->created_at->translatedFormat('j F Y') }}" disabled></div>
                    </div>
                    <p class="text-xs text-muted">Role hanya dapat diubah oleh Admin.</p>
                    <div class="flex justify-end"><x-button type="submit">Simpan Perubahan</x-button></div>
                </form>
            </x-card>

            <x-card class="p-6">
                <h2 class="text-[15px] font-semibold">Change Password</h2>
                <form method="POST" action="{{ route($role.'.profile.password') }}" class="mt-5 space-y-4">
                    @csrf @method('PUT')
                    <x-input name="current_password" type="password" label="Password Saat Ini" :required="true" autocomplete="current-password" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="password" type="password" label="Password Baru" :required="true" hint="Minimal 8 karakter." autocomplete="new-password" />
                        <x-input name="password_confirmation" type="password" label="Konfirmasi Password" :required="true" autocomplete="new-password" />
                    </div>
                    <div class="flex justify-end"><x-button type="submit">Ubah Password</x-button></div>
                </form>
            </x-card>

            <x-card class="p-6" x-data="{ mode: BrieflyTheme.get() }" @theme-changed.window="mode = $event.detail">
                <h2 class="text-[15px] font-semibold">Theme Preference</h2>
                <p class="mt-1 text-[13px] text-muted">Pilihan tema disimpan di browser Anda.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach ([['light', 'Light Mode', 'sun'], ['dark', 'Dark Mode', 'moon'], ['system', 'Ikuti Sistem', 'monitor']] as [$m, $l, $ic])
                        <button type="button" @click="BrieflyTheme.set('{{ $m }}')" :class="mode === '{{ $m }}' ? 'border-brand-500 bg-brand-500/10 text-brand-600 dark:text-brand-300' : 'border-line hover:bg-soft'"
                            class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium transition"><x-icon name="{{ $ic }}" class="h-5 w-5" />{{ $l }}
                            <x-icon name="check" class="ml-auto h-4 w-4" x-show="mode === '{{ $m }}'" x-cloak /></button>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
