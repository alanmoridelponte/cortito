<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-celeste/10 via-warm-white to-sol/10 border border-celeste/15 mb-10">
    <div class="relative z-10 px-6 py-12 sm:px-10 sm:py-16 lg:px-14 lg:py-20">
        <div class="mx-auto max-w-2xl text-center sm:text-left">
            <div class="mb-2 inline-flex items-center gap-1.5 rounded-full bg-celeste-light px-3 py-1 text-xs font-semibold text-celeste-text">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                cortito.ar &mdash; hecho en Argentina
            </div>

            <h1 class="font-display text-4xl font-bold tracking-tight text-ink sm:text-5xl lg:text-6xl leading-[1.1]">
                Links y notas que duran
                <br class="hidden sm:inline" />
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
                    class="btn-press inline-flex items-center gap-2 rounded-xl bg-sol px-6 py-3.5 text-base font-bold text-ink shadow-lg shadow-sol/25 transition-all duration-150 hover:bg-sol-hover hover:shadow-xl hover:shadow-sol/30">
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
    </div>

    <div class="absolute -bottom-8 -right-8 h-48 w-48 rounded-full bg-celeste/5 blur-3xl"></div>
    <div class="absolute -top-8 -left-8 h-36 w-36 rounded-full bg-sol/10 blur-3xl"></div>
</div>
