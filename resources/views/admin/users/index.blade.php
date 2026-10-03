<x-layouts.app title="User Management">
    @php
    $openAdd = request()->boolean('tambah') || (old('_mode') === 'create' && $errors->any());
    $editId = old('_mode') === 'edit' ? old('_id') : null;
    @endphp
    <x-page-header title="User Management" subtitle="Kelola semua user yang terdaftar dalam sistem."
        :crumbs="['Dashboard' => route('admin.dashboard'), 'User Management' => null]">
        <x-slot:actions><x-button icon="plus" @click="$dispatch('open-modal', { name: 'user-form', data: { mode: 'create', action: '{{ route('admin.users.store') }}', id: null, name: '', email: '', role: 'user', active: true } })">Tambah User</x-button></x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5">
        <x-stat-card label="Total User" icon="users" :value="$counts['total'][0]" :delta="$counts['total'][1]" />
        <x-stat-card label="Admin" icon="user" :value="$counts['admin'][0]" :delta="$counts['admin'][1]" />
        <x-stat-card label="Marketing Communication" icon="send" tone="purple" :value="$counts['marketing'][0]" :delta="$counts['marketing'][1]" />
        <x-stat-card label="Tim Kreatif" icon="palette" tone="green" :value="$counts['creative'][0]" :delta="$counts['creative'][1]" />
        <x-stat-card label="User" icon="user" tone="amber" :value="$counts['user'][0]" :delta="$counts['user'][1]" />
    </div>

    <x-card class="p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="search-wrap min-w-[200px] flex-1 lg:max-w-xs"><x-icon name="search" /><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, email, atau role…" class="input"></div>
            <div class="flex flex-1 flex-wrap items-center justify-end gap-2.5">
                <select name="role" class="select w-full sm:w-auto" onchange="this.form.submit()"><option value="">Semua Role</option>@foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected(($filters['role'] ?? '') === $k)>{{ $l }}</option>@endforeach</select>
                <select name="active" class="select w-full sm:w-auto" onchange="this.form.submit()"><option value="">Semua Status</option><option value="1" @selected(($filters['active'] ?? '') === '1')>Aktif</option><option value="0" @selected(($filters['active'] ?? '') === '0')>Nonaktif</option></select>
                @if (collect($filters)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty())<a href="{{ route('admin.users.index') }}" class="btn btn-outline"><x-icon name="x" class="h-4 w-4" />Reset</a>@endif
            </div>
        </form>

        <div class="mt-5">
            @if ($users->isEmpty())
                <x-empty-state title="User tidak ditemukan" description="Coba ubah kata kunci atau filter." icon="users" />
            @else
            <x-table>
                <x-slot:head><th class="w-12">No</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Dibuat Pada</th><th class="text-right">Aksi</th></x-slot:head>
                @foreach ($users as $i => $u)
                    @php $row = ['mode' => 'edit', 'action' => route('admin.users.update', $u), 'id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role, 'active' => $u->is_active, 'self' => $u->id === auth()->id()]; @endphp
                    <tr>
                        <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                        <td><span class="flex items-center gap-3"><x-avatar :user="$u" class="h-8 w-8 text-[11px]" /><span class="whitespace-nowrap font-medium">{{ $u->name }}</span></span></td>
                        <td class="text-ink/80">{{ $u->email }}</td>
                        <td><x-role-badge :role="$u->role" /></td>
                        <td>@if ($u->is_active)<span class="badge badge-green badge-dot">Aktif</span>@else<span class="badge badge-red badge-dot">Nonaktif</span>@endif</td>
                        <td class="whitespace-nowrap text-ink/80">{{ $u->created_at->translatedFormat('j M Y H:i') }}</td>
                        <td class="text-right">
                            <div class="inline-flex gap-1.5">
                                <button type="button" class="btn-icon-soft" title="Lihat" aria-label="Lihat {{ $u->name }}"
                                    @click="$dispatch('open-modal', { name: 'user-view', data: {{ \Illuminate\Support\Js::from($row + ['role_label' => $u->role_label, 'joined' => $u->created_at->translatedFormat('j F Y H:i'), 'briefs' => $u->briefs_count, 'activities' => $u->histories_count]) }} })"><x-icon name="eye" class="h-4 w-4" /></button>
                                <button type="button" class="btn-icon" title="Edit" aria-label="Edit {{ $u->name }}" @click="$dispatch('open-modal', { name: 'user-form', data: {{ \Illuminate\Support\Js::from($row) }} })"><x-icon name="pencil" class="h-4 w-4" /></button>
                                @if ($u->id !== auth()->id())
                                    <button type="button" class="btn-icon text-rose-500 hover:bg-rose-500/10 hover:text-rose-500" title="Hapus" aria-label="Hapus {{ $u->name }}"
                                        @click="$dispatch('open-modal', { name: 'user-delete', data: {{ \Illuminate\Support\Js::from($row) }} })"><x-icon name="trash" class="h-4 w-4" /></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
            @endif
            <x-table-footer :paginator="$users" />
        </div>
    </x-card>

    {{-- Tambah / Edit --}}
    <x-modal name="user-form" title="User" title-bind="payload.mode === 'edit' ? 'Edit User' : 'Tambah User'" max-width="max-w-xl" :show="$openAdd || (bool) $editId"
        :payload="$openAdd ? ['mode' => 'create', 'action' => route('admin.users.store')] : ($editId ? ['mode' => 'edit', 'action' => route('admin.users.update', $editId), 'id' => (int) $editId, 'self' => (int) $editId === auth()->id()] : [])">
        <form method="POST" :action="payload.action" id="user-form" class="space-y-4">
            @csrf
            <template x-if="payload.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
            <input type="hidden" name="_mode" :value="payload.mode"><input type="hidden" name="_id" :value="payload.id">
            <div>
                <label for="uf-name" class="label">Nama <span class="text-rose-500">*</span></label>
                <input id="uf-name" name="name" required maxlength="100" :value="payload.name ?? {{ \Illuminate\Support\Js::from(old('name', '')) }}" @class(['input', 'input-error' => $errors->has('name')])>
                <x-input-error for="name" />
            </div>
            <div>
                <label for="uf-email" class="label">Email <span class="text-rose-500">*</span></label>
                <input id="uf-email" name="email" type="email" required :value="payload.email ?? {{ \Illuminate\Support\Js::from(old('email', '')) }}" @class(['input', 'input-error' => $errors->has('email')])>
                <x-input-error for="email" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="uf-role" class="label">Role <span class="text-rose-500">*</span></label>
                    <select id="uf-role" name="role" class="select" :disabled="payload.self" x-effect="$el.value = payload.role ?? {{ \Illuminate\Support\Js::from(old('role', 'user')) }}">
                        @foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
                    </select>
                    <template x-if="payload.self"><input type="hidden" name="role" value="admin"></template>
                    <x-input-error for="role" />
                </div>
                <div>
                    <label class="label">Status</label>
                    <label class="flex h-10 cursor-pointer items-center gap-2.5 text-[13px]">
                        <input type="hidden" name="is_active" value="0" :disabled="payload.self">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-500/30" :checked="payload.active ?? true" :disabled="payload.self"> Akun aktif
                    </label>
                </div>
            </div>
            <div>
                <label for="uf-pw" class="label"><span x-text="payload.mode === 'edit' ? 'Password Baru' : 'Password'"></span> <span class="text-rose-500" x-show="payload.mode !== 'edit'">*</span></label>
                <input id="uf-pw" name="password" type="password" minlength="8" :required="payload.mode !== 'edit'" autocomplete="new-password" @class(['input', 'input-error' => $errors->has('password')])>
                <p class="mt-1.5 text-xs text-muted" x-show="payload.mode === 'edit'">Kosongkan jika tidak ingin mengubah password.</p>
                <x-input-error for="password" />
            </div>
        </form>
        <x-slot:footer>
            <button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'user-form')">Batal</button>
            <button type="submit" form="user-form" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-modal>

    {{-- Lihat --}}
    <x-modal name="user-view" title="Detail User" max-width="max-w-md">
        <div class="text-center">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-600 text-xl font-semibold text-white" x-text="(payload.name || '?').split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase()"></span>
            <p class="mt-3 text-lg font-bold" x-text="payload.name"></p><p class="text-[13px] text-muted" x-text="payload.email"></p>
        </div>
        <dl class="mt-5 space-y-3 border-t border-line pt-4 text-[13px]">
            <div class="flex justify-between"><dt class="text-muted">Role</dt><dd class="font-medium" x-text="payload.role_label"></dd></div>
            <div class="flex justify-between"><dt class="text-muted">Status</dt><dd class="font-medium" x-text="payload.active ? 'Aktif' : 'Nonaktif'"></dd></div>
            <div class="flex justify-between"><dt class="text-muted">Bergabung</dt><dd class="font-medium" x-text="payload.joined"></dd></div>
            <div class="flex justify-between"><dt class="text-muted">Brief dikirim</dt><dd class="font-medium" x-text="payload.briefs"></dd></div>
            <div class="flex justify-between"><dt class="text-muted">Aktivitas tercatat</dt><dd class="font-medium" x-text="payload.activities"></dd></div>
        </dl>
    </x-modal>

    {{-- Hapus --}}
    <x-modal name="user-delete" title="Hapus user?" max-width="max-w-md">
        <div class="flex items-start gap-3.5">
            <span class="tile tile-red h-11 w-11 rounded-full"><x-icon name="alert" class="h-6 w-6" /></span>
            <div><p class="text-[14px] font-medium">Hapus <span x-text="payload.name"></span>?</p>
            <p class="mt-1.5 text-[13px] text-muted">Seluruh brief milik user ini beserta file PDF-nya ikut terhapus. Tindakan ini tidak dapat dibatalkan.</p></div>
        </div>
        <x-slot:footer>
            <button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'user-delete')">Batal</button>
            <form method="POST" :action="payload.action">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Ya, Hapus</button></form>
        </x-slot:footer>
    </x-modal>
</x-layouts.app>
