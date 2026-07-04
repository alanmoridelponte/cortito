<div x-data="cookieConsent()">
    {{-- Botón flotante para re-mostrar el banner cuando fue rechazado --}}
    <button
        x-show="hasConsent && wasDeclined"
        x-cloak
        @click="resetConsent()"
        class="fixed bottom-4 right-4 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-warm-white shadow-lg border border-border-warm text-graphite-light transition-colors hover:text-celeste hover:border-celeste/30"
        title="Configurar cookies"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/>
            <path d="M8.5 8.5v.01"/>
            <path d="M16 15.5v.01"/>
            <path d="M12 12v.01"/>
            <path d="M11 17v.01"/>
            <path d="M7 14v.01"/>
        </svg>
    </button>

    {{-- Banner de consentimiento --}}
    <div x-show="!hasConsent" x-cloak
         class="fixed bottom-4 right-4 z-50" x-transition>
        <div class="bg-warm-white rounded-xl shadow-xl p-6 max-w-sm border border-border-warm">
            <h3 class="font-display text-lg font-bold mb-2 text-ink">Cookies de propiedad</h3>
            <p class="text-sm text-graphite mb-4 leading-relaxed">
                Para poder editar o eliminar tus cortitos, necesitamos guardar una cookie
                que nos permita identificar que eres el propietario. ¿Aceptas?
            </p>
            <div class="flex gap-2">
                <button @click="accept()"
                        class="btn-press px-4 py-2 bg-sol text-ink rounded-lg hover:bg-sol-hover text-sm font-bold transition-colors shadow-sm shadow-sol/20">
                    Aceptar
                </button>
                <button @click="decline()"
                        class="px-4 py-2 bg-cream-dark text-graphite rounded-lg hover:bg-border-warm text-sm font-medium transition-colors">
                    Rechazar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cookieConsent() {
    return {
        hasConsent: localStorage.getItem('cortito_cookie_consent') !== null,
        wasDeclined: localStorage.getItem('cortito_cookie_consent') === 'declined',

        accept() {
            localStorage.setItem('cortito_cookie_consent', 'accepted');
            document.cookie = 'cortito_cookie_consent=accepted; path=/; max-age=31536000; SameSite=Lax';
            this.hasConsent = true;
            this.wasDeclined = false;
        },

        decline() {
            localStorage.setItem('cortito_cookie_consent', 'declined');
            this.hasConsent = true;
            this.wasDeclined = true;
        },

        resetConsent() {
            localStorage.removeItem('cortito_cookie_consent');
            document.cookie = 'cortito_cookie_consent=; path=/; max-age=0';
            this.hasConsent = false;
            this.wasDeclined = false;
        }
    }
}
</script>
@endpush
