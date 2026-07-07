<div x-data="notificationModal()"
     x-on:notify.window="show($event.detail)"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center bg-ink/40 p-4 backdrop-blur-[2px]"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="w-full max-w-sm rounded-xl bg-warm-white p-6 shadow-2xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.away="dismiss()">

        <template x-if="type === 'error'">
            <div>
                <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-danger-light">
                    <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
                <h3 class="mb-1 mt-3 font-display text-lg font-bold text-ink">Error</h3>
                <p class="mb-5 text-sm text-graphite" x-text="message"></p>
            </div>
        </template>

        <template x-if="type === 'success'">
            <div>
                <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-mint-light">
                    <svg class="h-5 w-5 text-mint" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="mb-1 mt-3 font-display text-lg font-bold text-ink">Listo</h3>
                <p class="mb-5 text-sm text-graphite" x-text="message"></p>
            </div>
        </template>

        <div class="flex justify-end">
            <button type="button" @click="dismiss()"
                    class="rounded-lg border border-border-warm bg-warm-white px-4 py-2 text-sm font-medium text-graphite transition-colors hover:bg-cream-dark cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
function notificationModal() {
    return {
        isOpen: false,
        type: 'error',
        message: '',
        init() {
            @if (session('error'))
                this.show({ type: 'error', message: '{{ addslashes(session('error')) }}' });
            @elseif (session('success'))
                this.show({ type: 'success', message: '{{ addslashes(session('success')) }}' });
            @endif
        },
        show(detail) {
            this.type = detail.type || 'error';
            this.message = detail.message;
            this.isOpen = true;
            if (this.type === 'success') {
                setTimeout(() => { this.dismiss(); }, 4000);
            }
        },
        dismiss() {
            this.isOpen = false;
            this.message = '';
        },
    };
}
</script>
