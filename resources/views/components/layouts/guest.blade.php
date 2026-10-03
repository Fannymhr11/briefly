@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}BRIEFLY</title>
    <script>
        (function () { try { var m = localStorage.getItem('briefly-theme') || 'light'; var d = m === 'dark' || (m === 'system' && matchMedia('(prefers-color-scheme: dark)').matches); document.documentElement.classList.toggle('dark', d); } catch (e) {} })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{ $slot }}
    <x-toast />
</body>
</html>
