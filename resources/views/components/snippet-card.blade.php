@props(['snippet'])

@php
    $typeConfig = [
        'text' => [
            'accent' => 'card-accent-text',
            'bg' => 'bg-mint-light',
            'text' => 'text-mint',
            'label' => 'Texto',
            'icon' => 'note',
        ],
        'url' => [
            'accent' => 'card-accent-url',
            'bg' => 'bg-coral-light',
            'text' => 'text-coral',
            'label' => 'Enlace acortado',
            'icon' => 'link',
        ],
    ];
    $config = $typeConfig[$snippet->content_type] ?? $typeConfig['text'];
    $preview = $snippet->content_type === 'url'
        ? Str::limit($snippet->content, 120)
        : Str::limit(strip_tags($snippet->content), 120);
    $isUrl = $snippet->content_type === 'url';

    $expiryClass = 'expiry-normal';
    $expiryLabel = '';
    if ($snippet->expires_at) {
        $minutesLeft = now()->diffInMinutes($snippet->expires_at, false);
        if ($minutesLeft < 0) {
            $expiryClass = 'expiry-urgent';
            $expiryLabel = 'Vencido';
        } elseif ($minutesLeft < 1440) {
            $hoursRemaining = (int) ceil($minutesLeft / 60);
            $expiryClass = 'expiry-urgent';
            $expiryLabel = 'Vence en ' . $hoursRemaining . 'h';
        } elseif ($minutesLeft < 2880) {
            $expiryClass = 'expiry-soon';
            $expiryLabel = 'Vence manana';
        } else {
            $expiryLabel = 'Expira ' . $snippet->expires_at->diffForHumans();
        }
    }
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-xl border border-border-warm bg-warm-white shadow-sm transition-all duration-200 hover:border-graphite-light/30 hover:shadow-md {{ $config['accent'] }}"
     x-data="{ showDeleteConfirm: false, copied: false, copyContent: @js($snippet->content_type === 'text' ? strip_tags($snippet->content) : null) }">

    {{-- Click area --}}
    <a href="{{ route('snippets.show', $snippet->alias) }}" @if($isUrl) target="_blank" @endif class="flex flex-1 flex-col p-5 sm:p-6">

        {{-- Top: type indicator + badges --}}
        <div class="mb-4 flex items-center gap-x-2 gap-y-1.5 flex-wrap">
            {{-- Type chip --}}
            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                @if($config['icon'] === 'note')
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @else
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                @endif
                {{ $config['label'] }}
            </span>

            @if($snippet->language)
                <span class="rounded-md bg-cream-dark px-1.5 py-0.5 text-[11px] font-medium text-graphite">
                    {{ $snippet->language }}
                </span>
            @endif
            @if($snippet->isProtected())
                <span class="inline-flex items-center gap-0.5 rounded-md bg-amber-light px-1.5 py-0.5 text-[11px] font-medium text-amber">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Protegido
                </span>
            @endif
            <div class="ml-auto flex items-center gap-1 text-xs text-graphite-light shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ $snippet->views_count }}
            </div>
        </div>

        {{-- Alias (the star of the card) --}}
        <h3 class="mb-2 font-mono text-base sm:text-lg font-bold tracking-tight text-ink leading-tight line-clamp-1 group-hover:text-celeste transition-colors">
            {{ $snippet->alias }}
        </h3>

        {{-- Preview --}}
        @if($preview)
            <p class="mb-5 flex-1 text-sm leading-relaxed text-graphite line-clamp-2">{{ $preview }}</p>
        @else
            <div class="mb-5 flex-1"></div>
        @endif

    </a>

    {{-- Footer --}}
    <div class="flex items-center border-t border-border-light bg-cream/40 px-5 py-3 sm:py-3.5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between grow gap-1.5 text-xs text-graphite-light">
            @if($snippet->canBeEditedBy(request()))
            <div class="flex flex-row gap-1 w-full sm:w-auto sm:shrink-0">
                <a href="{{ route('snippets.edit', $snippet->alias) }}"
                   onclick="event.preventDefault(); event.stopPropagation();"
                   x-on:click.prevent="$dispatch('open-edit-modal', '{{ $snippet->alias }}')"
                   class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 rounded-lg border border-border-warm bg-cream-dark/70 px-3 py-1.5 font-medium transition-all text-xs duration-150 btn-press h-8 cursor-pointer text-graphite-light hover:text-celeste hover:bg-celeste-light/50 hover:border-celeste/30"
                   title="Editar">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Editar</span>
                </a>
                <button
                    type="button"
                    @click.stop="showDeleteConfirm = true"
                    class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 rounded-lg border border-border-warm bg-cream-dark/70 px-3 py-1.5 font-medium transition-all text-xs duration-150 btn-press h-8 cursor-pointer text-graphite-light hover:bg-danger-light hover:text-danger hover:border-danger/40"
                    title="Quitar">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Quitar</span>
                </button>
            </div>
            @endif
            <button type="button"
                    class="inline-flex w-full sm:w-auto sm:shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-border-warm bg-cream-dark/70 px-3 py-1.5 font-medium transition-all text-xs duration-150 btn-press h-8 cursor-pointer"
                    :class="copied ? 'bg-mint-light text-mint border-mint/40' : 'text-graphite-light hover:text-celeste hover:bg-celeste-light/50 hover:border-celeste/30'"
                    @click="
                        const text = copyContent || '{{ route('snippets.show', $snippet->alias) }}';
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(() => { copied = true; setTimeout(() => copied = false, 2000); });
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = text;
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            document.body.removeChild(ta);
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        }
                    ">
                <svg x-show="!copied" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <svg x-show="copied" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="copied ? 'Copiado!' : 'Copiar Enlace'"></span>
            </button>
        </div>
    </div>

    {{-- Timestamp + expiry --}}
    <div class="flex items-center justify-between gap-2 border-t border-border-light px-5 py-2 sm:px-6 text-xs text-graphite-light">
        <span>{{ ucfirst($snippet->created_at->diffForHumans()) }}</span>
        @if($expiryLabel)
            <span class="text-border-warm">&middot;</span>
            <span class="{{ $expiryClass }}">{{ $expiryLabel }}</span>
        @endif
    </div>

    {{-- Delete confirmation modal --}}
    <div x-show="showDeleteConfirm" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="w-full max-w-sm rounded-xl bg-warm-white p-6 shadow-2xl" @click.away="showDeleteConfirm = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-danger-light">
                <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="mb-1 mt-3 font-display text-lg font-bold text-ink">Eliminar anotador</h3>
            <p class="mb-5 text-sm text-graphite">Se eliminará permanentemente <strong class="font-mono font-semibold text-ink">{{ $snippet->alias }}</strong>. Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-2">
                <button type="button" @click="showDeleteConfirm = false"
                        class="rounded-lg border border-border-warm bg-warm-white px-4 py-2 text-sm font-medium text-graphite transition-colors hover:bg-cream-dark cursor-pointer">
                    Cancelar
                </button>
                <form action="{{ route('snippets.destroy', $snippet->alias) }}" method="POST" @click.stop>
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn-press rounded-lg bg-danger px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-danger-hover cursor-pointer">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
