<div x-data="cookieConsent()">
    {{-- Botón flotante para re-mostrar el banner cuando fue rechazado --}}
    <button
        x-show="hasConsent && wasDeclined"
        x-cloak
        @click="resetConsent()"
        class="fixed bottom-4 right-4 z-40 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-lg border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition-colors"
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
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm border border-gray-200">
            <h3 class="text-lg font-bold mb-2">Cookies de propiedad</h3>
            <p class="text-sm text-gray-600 mb-4">
                Para poder editar o eliminar tus anotadores, necesitamos guardar una cookie
                que nos permita identificar que eres el propietario. ¿Aceptas?
            </p>
            <div class="flex gap-2">
                <button @click="accept()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                    Aceptar
                </button>
                <button @click="decline()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
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
