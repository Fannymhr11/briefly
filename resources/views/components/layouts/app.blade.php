@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}BRIEFLY</title>
    <script>
        /* Terapkan tema sebelum render agar tidak berkedip */
        (function () { try { var m = localStorage.getItem('briefly-theme') || 'light'; var d = m === 'dark' || (m === 'system' && matchMedia('(prefers-color-scheme: dark)').matches); document.documentElement.classList.toggle('dark', d); } catch (e) {} })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ drawer: false }" @keydown.escape.window="drawer = false">
    <x-sidebar />
    <div x-show="drawer" x-cloak x-transition.opacity @click="drawer = false" class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-[2px] md:hidden"></div>

    <div class="md:pl-[76px] lg:pl-60">
        <x-topbar />
        <main class="mx-auto w-full max-w-[1560px] px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
            <footer class="mt-10 flex flex-wrap items-center justify-between gap-2 text-xs text-muted">
                <span>© {{ date('Y') }} Dthree. All rights reserved.</span>
                <span>BRIEFLY · Content Monitoring System v1.0.0</span>
            </footer>
        </main>
    </div>

    <x-toast />
</body>
</html>
