<div class="relative overflow-hidden border-b border-celeste/15 bg-gradient-to-br from-celeste/10 via-warm-white to-sol/10 -mx-4 mb-10 sm:-mx-6 lg:-mx-8">
    {{-- Sol de Mayo — slow-spinning, faint, behind the content --}}
    <x-sol-de-mayo class="pointer-events-none absolute -right-20 -top-20 h-80 w-80 text-sol opacity-[0.12] animate-spin-slow" />

    <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-10 px-6 py-12 sm:px-10 sm:py-16 lg:grid-cols-2 lg:px-14 lg:py-20">
        {{-- Copy --}}
        <div class="text-center sm:text-left">
            <div class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-celeste-light px-3 py-1 text-xs font-semibold text-celeste-text">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                cortito.ar &mdash; hecho en Argentina
            </div>

            <h1 class="font-display text-4xl font-bold leading-[1.05] tracking-tight text-ink sm:text-5xl lg:text-6xl">
                Links y notas que duran
                <span class="text-celeste">lo justo</span>.
            </h1>

            <p class="mt-4 text-lg leading-relaxed text-graphite sm:mt-5 sm:text-xl sm:leading-relaxed">
                24 horas y chau.
                <br class="hidden sm:inline" />
                Sin cuenta, sin rastro, <strong class="text-ink">gratis.</strong>
            </p>

            <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:items-center">
                <button
                    type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                    class="btn-press inline-flex items-center gap-2 rounded-xl bg-sol px-6 py-3.5 text-base font-bold text-ink shadow-lg shadow-sol/25 transition-all duration-150 hover:bg-sol-hover hover:shadow-xl hover:shadow-sol/30 focus:outline-none focus-ring-celeste">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Creá tu primer cortito
                </button>
                <p class="text-xs text-graphite-light">
                    Ni tarjeta ni mail. Nada.
                </p>
            </div>
        </div>

        {{-- Live preview mock: a fake cortito whose alias cycles through examples --}}
        <div
            class="mx-auto w-full max-w-sm lg:max-w-md"
            x-data="{ aliases: ['tp-historia', 'boleta-luz', 'link-quincho', 'wifi-abuela', 'fotos-asado'], i: 0 }"
            x-init="if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(() => i = (i + 1) % aliases.length, 2500)">
            <div class="overflow-hidden rounded-xl border border-border-warm bg-warm-white shadow-xl shadow-celeste/10 animate-float-slow">
                {{-- Browser chrome --}}
                <div class="flex items-center gap-2 border-b border-border-light bg-cream-dark/60 px-4 py-2.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-danger/40"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-sol/50"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-mint/40"></span>
                    <div class="ml-2 flex flex-1 items-center gap-1.5 truncate rounded-md bg-warm-white px-2.5 py-1 text-xs text-graphite">
                        <svg class="h-3 w-3 shrink-0 text-mint" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="truncate font-mono">cortito.ar/<template x-for="a in [aliases[i]]" :key="i"><span
                                    class="font-semibold text-celeste-text"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-text="a"></span></template></span>
                    </div>
                </div>
                {{-- Body --}}
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-celeste-light px-2.5 py-1 text-xs font-semibold text-celeste-text">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Enlace
                        </span>
                        <span class="expiry-soon inline-flex items-center gap-1 text-xs">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            vence en 24hs
                        </span>
                    </div>
                    <div class="mt-4 h-2.5 w-4/5 rounded-full bg-cream-dark"></div>
                    <div class="mt-2.5 h-2.5 w-3/5 rounded-full bg-cream-dark"></div>
                    <div class="mt-5 flex items-center justify-between border-t border-border-light pt-4">
                        <span class="inline-flex items-center gap-1.5 text-xs text-graphite">
                            <svg class="h-3.5 w-3.5 text-graphite-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="font-semibold text-ink" x-text="128 + i * 7"></span> vistas
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-celeste-text">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copiar
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating decorative blobs --}}
    <div class="pointer-events-none absolute -bottom-8 -right-8 h-48 w-48 rounded-full bg-celeste/5 blur-3xl animate-float-slow"></div>
    <div class="pointer-events-none absolute -top-8 -left-8 h-36 w-36 rounded-full bg-sol/10 blur-3xl animate-float-slower"></div>
</div>
