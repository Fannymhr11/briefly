@php
$u = auth()->user();
$nav = match ($u->role) {
    'admin' => [
        ['Dashboard', 'home', 'admin.dashboard', ['admin.dashboard']],
        ['User Management', 'users', 'admin.users.index', ['admin.users.*']],
        ['Semua Brief', 'file-text', 'admin.briefs', ['admin.briefs', 'admin.briefs.show']],
        ['Creative Tasks', 'check-square', 'admin.tasks', ['admin.tasks', 'admin.tasks.show']],
        ['History', 'history', 'admin.history', ['admin.history']],
        ['Profile', 'user', 'admin.profile', ['admin.profile*']],
    ],
    'user' => [
        ['Dashboard', 'home', 'user.dashboard', ['user.dashboard']],
        ['Upload Brief', 'upload', 'user.briefs.create', ['user.briefs.create']],
        ['Brief Saya', 'file-text', 'user.briefs.index', ['user.briefs.index', 'user.briefs.show']],
        ['History', 'history', 'user.history', ['user.history']],
    ],
    'marketing' => [
        ['Dashboard', 'home', 'marketing.dashboard', ['marketing.dashboard']],
        ['Review Brief', 'check-square', 'marketing.review', ['marketing.review']],
        ['Semua Brief', 'file-text', 'marketing.briefs', ['marketing.briefs', 'marketing.briefs.show']],
        ['Creative Tasks', 'edit', 'marketing.tasks', ['marketing.tasks', 'marketing.tasks.show']],
        ['History', 'history', 'marketing.history', ['marketing.history']],
        ['Profile', 'user', 'marketing.profile', ['marketing.profile*']],
    ],
    default => [
        ['Dashboard', 'home', 'creative.dashboard', ['creative.dashboard']],
        ['Creative Tasks', 'check-square', 'creative.tasks', ['creative.tasks', 'creative.tasks.show']],
        ['History', 'history', 'creative.history', ['creative.history']],
        ['Profile', 'user', 'creative.profile', ['creative.profile*']],
    ],
};
$promo = match ($u->role) {
    'creative' => ['Create Great Content Together', 'Dari brief, jadi karya nyata.'],
    'user' => ['Konten yang baik', 'berawal dari brief yang jelas.'],
    'admin' => ['Better Content, Bigger Impact.', 'Kelola setiap brief dengan lebih mudah dan terstruktur.'],
    default => ['Better Content, Bigger Impact', 'Kelola brief, pantau progres, wujudkan konten terbaik.'],
};
@endphp
<aside :class="drawer ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-line bg-surface transition-transform duration-200 md:w-[76px] md:translate-x-0 lg:w-60"
       aria-label="Navigasi utama">
    <div class="flex h-16 shrink-0 items-center justify-between px-5 md:justify-center lg:justify-between lg:px-5">
        <a href="{{ $u->dashboardUrl() }}" aria-label="BRIEFLY"><x-logo text-class="md:hidden lg:inline-block" /></a>
        <button type="button" class="btn-icon md:hidden" @click="drawer = false" aria-label="Tutup menu"><x-icon name="x" class="h-4 w-4" /></button>
    </div>

    @if ($u->role === 'marketing')
        <p class="px-6 pb-1 pt-2 text-xs font-semibold text-ink/80 md:hidden lg:block">Marketing Communication</p>
    @endif

    <nav class="mt-2 flex-1 space-y-1 overflow-y-auto px-3">
        @foreach ($nav as [$label, $icon, $route, $patterns])
            <a href="{{ route($route) }}" title="{{ $label }}" @class(['nav-link', 'nav-link-active' => request()->routeIs(...$patterns)]) @if (request()->routeIs(...$patterns)) aria-current="page" @endif>
                <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
                <span class="inline md:hidden lg:inline">{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <div class="space-y-2 p-3">
        <div class="relative overflow-hidden rounded-2xl border border-line bg-soft p-4 md:hidden lg:block">
            <x-icon name="send" class="h-5 w-5 text-brand-500" />
            <p class="mt-2.5 text-[13px] font-semibold leading-snug">{{ $promo[0] }}</p>
            <p class="mt-1 text-[11px] leading-snug text-muted">{{ $promo[1] }}</p>
            <svg class="pointer-events-none absolute -bottom-6 -right-6 h-24 w-24 text-brand-500/10" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true"><circle cx="60" cy="60" r="40"/><circle cx="82" cy="78" r="26"/></svg>
        </div>

        @if ($u->role === 'user')
            <a href="{{ route('user.profile') }}" class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-soft md:justify-center lg:justify-start" title="Profile">
                <x-avatar :user="$u" />
                <span class="min-w-0 flex-1 md:hidden lg:block">
                    <span class="block truncate text-[13px] font-semibold">{{ $u->name }}</span>
                    <span class="block text-xs text-muted">{{ $u->role_label }}</span>
                </span>
                <x-icon name="chevron-right" class="h-4 w-4 text-muted md:hidden lg:block" />
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-2">
            @csrf
            <button type="submit" class="nav-link w-full" title="Logout">
                <x-icon name="log-out" class="h-5 w-5 shrink-0" />
                <span class="inline md:hidden lg:inline">Logout</span>
            </button>
        </form>
    </div>
</aside>
