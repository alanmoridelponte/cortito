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

    @php
        // Immersive landing (no cortitos yet): the hero is "el inicio", so the navbar
        // stays hidden until the user scrolls. Once cortitos exist it's a normal sticky bar.
        $immersiveHeader = isset($snippets) && $snippets->isEmpty();
    @endphp

    <header
        x-data="{ scrolled: false }"
        x-init="scrolled = window.scrollY > 10"
        @scroll.window.passive="scrolled = window.scrollY > 10"
        class="top-0 z-40 border-b border-border-warm bg-warm-white/80 backdrop-blur-sm transition-transform duration-300 {{ $immersiveHeader ? 'fixed inset-x-0' : 'sticky' }}"
        @if($immersiveHeader)
            style="transform: translateY(-100%)"
            :style="scrolled ? 'transform: translateY(0)' : 'transform: translateY(-100%)'"
        @endif
    >
        <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="group relative flex items-baseline gap-0.5">
                <span class="font-display text-xl font-bold tracking-tight text-ink transition-colors group-hover:text-celeste">cortito</span>
                <span class="font-display text-xl font-medium text-celeste-text">.ar</span>
                <span class="absolute -bottom-1 left-0 h-0.5 w-0 rounded-full bg-gradient-to-r from-celeste to-sol transition-all duration-300 group-hover:w-full"></span>
            </a>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                @auth
                    <span class="hidden text-sm text-graphite sm:inline">{{ auth()->user()->name }}</span>
                @endauth
            </div>
        </div>
    </header>

    <main class="px-4 sm:px-6 lg:px-8 {{ $immersiveHeader ? 'pb-6 sm:pb-10' : 'py-6 sm:py-10' }}">
        @yield('content')
    </main>

    <footer class="border-t border-border-warm py-8 text-center">
        <x-sol-de-mayo class="mx-auto mb-2 h-5 w-5 text-sol/60" />
        <p class="font-display text-xs font-medium tracking-wide text-graphite-light uppercase">cortito.ar</p>
        <p class="mt-1 text-[11px] font-medium tracking-wide text-border-warm">Cortito y al pie &mdash; hecho en Argentina 🇦🇷</p>
    </footer>

    <x-cookie-consent />

    <x-notification-modal />

    @stack('scripts')
</body>
</html>
