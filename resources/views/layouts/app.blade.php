<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cortito - Cortitos')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-1">
                <span class="text-2xl font-bold tracking-tight text-gray-900">cortito</span>
                <span class="text-2xl font-light text-indigo-500">.ar</span>
            </a>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                @auth
                    <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
                @endauth
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-6 pt-4">
            <div class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-6 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-gray-100 py-8 text-center text-xs text-gray-400">
        cortito.ar &mdash; cortitos efímeros
    </footer>

    <x-cookie-consent />

    @stack('scripts')
</body>
</html>
