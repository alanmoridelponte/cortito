<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cortito - Cortitos')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="Acortá links y compartí notas que se borran solas. 24 horas y chau: sin cuenta, sin rastro y gratis. Cortito, hecho en Argentina.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#75AADB">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Cortito">
    <meta property="og:title" content="Cortito — Links y notas que duran lo justo">
    <meta property="og:description" content="Acortá links y compartí notas que se borran solas. 24 horas y chau: sin cuenta, sin rastro y gratis.">
    <meta property="og:image" content="{{ asset('web-app-manifest-512x512.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Cortito — Links y notas que duran lo justo">
    <meta name="twitter:description" content="Acortá links y compartí notas que se borran solas. Sin cuenta, sin rastro y gratis.">
    <meta name="twitter:image" content="{{ asset('web-app-manifest-512x512.png') }}">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Cortito" />
    <link rel="manifest" href="/site.webmanifest" />
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
        <p class="font-display text-xs font-semibold tracking-wide text-graphite uppercase">cortito.ar</p>
        <p class="mt-1 text-xs font-medium text-graphite">Cortito y al pie &mdash; hecho en Argentina 🇦🇷</p>
    </footer>

    <x-cookie-consent />

    <x-notification-modal />

    @stack('scripts')
</body>
</html>
