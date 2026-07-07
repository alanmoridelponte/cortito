<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Demasiadas solicitudes</title>
    @vite(['resources/css/app.css'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-cream text-ink antialiased">

    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 sm:px-6">
        <a href="{{ route('home') }}" class="flex items-baseline gap-0.5 group">
            <span class="font-display text-xl font-bold tracking-tight text-ink transition-colors group-hover:text-celeste">cortito</span>
            <span class="font-display text-xl font-medium text-celeste">.ar</span>
        </a>
    </div>

    <main class="mx-auto max-w-6xl px-5 py-8 sm:px-6 sm:py-10">
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border-warm bg-warm-white py-24 px-6 text-center">
            <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-celeste-light">
                <svg class="h-8 w-8 text-celeste" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
            </div>
            <h1 class="font-display text-4xl font-bold tracking-tight text-ink mb-2">429</h1>
            <h2 class="font-display text-xl font-semibold text-ink mb-2">Demasiadas solicitudes</h2>
            <p class="text-graphite max-w-md mb-8">Hiciste demasiadas solicitudes en poco tiempo. Esperá un momento y volvé a intentar.</p>
            <a href="{{ route('home') }}"
               class="btn-press inline-flex items-center gap-2 rounded-lg bg-sol px-5 py-2.5 text-sm font-bold text-ink shadow-sm shadow-sol/20 transition-all duration-150 hover:bg-sol-hover hover:shadow-md hover:shadow-sol/25">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Volver al inicio
            </a>
        </div>
    </main>

    <footer class="border-t border-border-warm py-8 text-center">
        <p class="font-display text-xs font-medium tracking-wide text-graphite-light uppercase">cortito.ar &mdash; cortitos efímeros</p>
    </footer>

</body>
</html>
