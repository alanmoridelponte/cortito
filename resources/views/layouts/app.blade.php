<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cortito - Anotadores')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight">
                cortito<span class="text-gray-400">.ar</span>
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-6xl px-4 pt-4">
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        </div>
    @endif

    <main class="mx-auto max-w-6xl px-4 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 py-6 text-center text-xs text-gray-400">
        cortito.ar &mdash; anotadores efímeros
    </footer>

    <x-cookie-consent />

    @stack('scripts')
</body>
</html>
