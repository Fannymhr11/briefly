@php
$u = auth()->user();
$searchRoute = match ($u->role) {
    'admin' => 'admin.briefs',
    'user' => 'user.briefs.index',
    'marketing' => 'marketing.briefs',
    default => 'creative.tasks',
};
$placeholder = match ($u->role) {
    'admin', 'marketing' => 'Cari brief, brand, platform, atau nama user…',
    'creative' => 'Cari brief, brand, platform, atau pengirim…',
    default => 'Cari brief, brand, platform…',
};
$notifs = \App\Models\ActivityHistory::with(['brief'])->visibleTo($u)->latest('created_at')->latest('id')->limit(5)->get();
$hasNew = $notifs->contains(fn ($n) => $n->created_at->gt(now()->subDay()));
@endphp
<header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-surface/85 px-4 backdrop-blur sm:px-6 lg:px-8">
    <button type="button" class="btn-icon md:hidden" @click="drawer = true" aria-label="Buka menu"><x-icon name="menu" class="h-[18px] w-[18px]" /></button>

    <form method="GET" action="{{ route($searchRoute) }}" class="search-wrap min-w-0 max-w-xl flex-1" role="search">
        <x-icon name="search" />
        <input type="search" name="q" value="{{ request()->routeIs($searchRoute) ? request('q') : '' }}" placeholder="{{ $placeholder }}" class="input h-10 rounded-full bg-soft" aria-label="Pencarian">
    </form>

    <div class="ml-auto flex items-center gap-2 sm:gap-3">
        <x-theme-toggle />

        <x-dropdown width="w-80 max-w-[calc(100vw-2rem)]">
            <x-slot:trigger>
                <button type="button" class="btn-icon relative h-9 w-9 border-0" aria-label="Notifikasi">
                    <x-icon name="bell" class="h-[18px] w-[18px]" />
                    @if ($hasNew)<span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-surface"></span>@endif
                </button>
            </x-slot:trigger>
            <div class="px-2.5 pb-1 pt-1.5 text-[13px] font-semibold">Aktivitas Terbaru</div>
            <ul class="max-h-80 divide-y divide-line overflow-y-auto">
                @forelse ($notifs as $n)
                    <li class="px-2.5 py-2.5">
                        <p class="text-[13px] leading-snug">{{ $n->description }}</p>
                        <p class="mt-0.5 text-[11px] text-muted">{{ $n->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="px-2.5 py-5 text-center text-[13px] text-muted">Belum ada aktivitas.</li>
                @endforelse
            </ul>
            <a href="{{ route($u->role.'.history') }}" class="dd-item mt-1 justify-center font-medium text-brand-600 dark:text-brand-300">Lihat semua riwayat</a>
        </x-dropdown>

        <span class="hidden h-8 w-px bg-line sm:block"></span>

        <x-dropdown width="w-56">
            <x-slot:trigger>
                <button type="button" class="flex items-center gap-2.5 rounded-xl py-1 pl-1 pr-1.5 transition hover:bg-soft" aria-label="Menu profil">
                    <x-avatar :user="$u" />
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block max-w-[140px] truncate text-[13px] font-semibold">{{ $u->name }}</span>
                        <span class="block max-w-[140px] truncate text-xs text-muted">{{ $u->role_label }}</span>
                    </span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 text-muted sm:block" />
                </button>
            </x-slot:trigger>
            <div class="border-b border-line px-3 pb-2.5 pt-1.5 sm:hidden">
                <p class="truncate text-[13px] font-semibold">{{ $u->name }}</p>
                <p class="text-xs text-muted">{{ $u->role_label }}</p>
            </div>
            <a href="{{ route($u->role.'.profile') }}" class="dd-item"><x-icon name="user" class="h-4 w-4 text-muted" />Profile</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="dd-item"><x-icon name="log-out" class="h-4 w-4 text-muted" />Logout</button>
            </form>
        </x-dropdown>
    </div>
</header>
