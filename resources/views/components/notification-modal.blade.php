<div x-data="notificationToast()"
     x-on:notify.window="add($event.detail)"
     x-cloak
     class="pointer-events-none fixed top-4 right-4 z-[100] flex w-full max-w-sm flex-col gap-2">

    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="toast.isVisible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-4 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-4 opacity-0"
             class="pointer-events-auto w-full rounded-xl border border-border-warm bg-warm-white p-4 shadow-2xl"
             :class="toast.type === 'error' ? 'border-l-4 border-l-danger' : 'border-l-4 border-l-mint'">
            <div class="flex items-start gap-3">
                <template x-if="toast.type === 'error'">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-danger-light">
                        <svg class="h-4 w-4 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'success'">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-mint-light">
                        <svg class="h-4 w-4 text-mint" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </template>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-ink" x-text="toast.type === 'error' ? 'Error' : 'Listo'"></p>
                    <p class="mt-0.5 text-sm leading-snug text-graphite" x-text="toast.message"></p>
                </div>
                <button @click="remove(toast.id)" class="flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-md text-graphite-light transition-colors hover:bg-cream-dark hover:text-ink">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function notificationToast() {
    return {
        toasts: [],
        nextId: 0,

        init() {
            @if (session('error'))
                this.add({ type: 'error', message: '{{ addslashes(session('error')) }}' });
            @elseif (session('success'))
                this.add({ type: 'success', message: '{{ addslashes(session('success')) }}' });
            @endif
        },

        add(detail) {
            const id = this.nextId++;
            const toast = {
                id,
                type: detail.type || 'error',
                message: detail.message,
                isVisible: true,
            };

            this.toasts.push(toast);

            const delay = toast.type === 'error' ? 6000 : 4000;
            setTimeout(() => this.remove(id), delay);
        },

        remove(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (!toast) return;

            toast.isVisible = false;

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        },
    };
}
</script>
