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
<body class="min-h-screen bg-cream text-ink antialiased">

    <header class="border-b border-border-warm bg-warm-white/80 backdrop-blur-sm sticky top-0 z-40">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-baseline gap-0.5 group">
                <span class="font-display text-xl font-bold tracking-tight text-ink transition-colors group-hover:text-violet">cortito</span>
                <span class="font-display text-xl font-medium text-violet">.ar</span>
            </a>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                @auth
                    <span class="hidden text-sm text-graphite sm:inline">{{ auth()->user()->name }}</span>
                @endauth
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto max-w-6xl px-5 pt-4 sm:px-6">
            <div class="flex items-center gap-2.5 rounded-lg border border-mint/30 bg-mint-surface px-4 py-3 text-sm font-medium text-mint">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="mx-auto max-w-6xl px-5 py-8 sm:px-6 sm:py-10">
        @yield('content')
    </main>

    <footer class="border-t border-border-warm py-8 text-center">
        <p class="font-display text-xs font-medium tracking-wide text-graphite-light uppercase">cortito.ar &mdash; cortitos efímeros</p>
    </footer>

    <x-cookie-consent />

    @stack('scripts')
</body>
</html>
