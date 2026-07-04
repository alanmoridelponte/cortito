@props(['snippet'])

@php
    $typeColors = [
        'text' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Texto'],
        'url' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Acortador'],
    ];
    $colors = $typeColors[$snippet->content_type] ?? $typeColors['text'];
    $preview = $snippet->content_type === 'url'
        ? Str::limit($snippet->content, 120)
        : Str::limit(strip_tags($snippet->content), 120);
    $isUrl = $snippet->content_type === 'url';
@endphp

<div class="group relative flex flex-col rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md"
     x-data="{ showDeleteConfirm: false, copied: false, copyContent: @js($snippet->content_type === 'text' ? strip_tags($snippet->content) : null) }">

    {{-- Click area --}}
    @if($isUrl)
        <div class="flex flex-1 flex-col p-5 cursor-pointer" @click="
            const url = '{{ route('snippets.show', $snippet->alias) }}';
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => { copied = true; setTimeout(() => copied = false, 2000); });
            } else {
                const ta = document.createElement('textarea');
                ta.value = url;
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
    @else
        <a href="{{ route('snippets.show', $snippet->alias) }}" class="flex flex-1 flex-col p-5">
    @endif
        {{-- Badges row --}}
        <div class="mb-3 flex flex-wrap items-center gap-1.5">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                {{ $colors['label'] }}
            </span>
            @if($snippet->language)
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600">
                    {{ $snippet->language }}
                </span>
            @endif
            @if($snippet->is_edited)
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-[11px] font-medium text-yellow-700">
                    editado
                </span>
            @endif
            @if($snippet->isProtected())
                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-700">
                    <svg class="mr-0.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    protegido
                </span>
            @endif
            @if($snippet->expires_at)
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-500">
                    Expira {{ $snippet->expires_at->diffForHumans() }}
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="mb-1 text-base font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-1">
            {{ $snippet->alias }}
        </h3>

        {{-- Preview --}}
        @if($preview)
            <p class="mb-4 flex-1 text-sm leading-relaxed text-gray-500 line-clamp-2">{{ $preview }}</p>
        @else
            <div class="mb-4 flex-1"></div>
        @endif
    @if($isUrl)
        </div>
    @else
        </a>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between border-t border-gray-100 px-5 py-3">
        <span class="text-xs text-gray-400">{{ $snippet->created_at->diffForHumans() }}</span>
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <div class="flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ $snippet->views_count }}
            </div>
            <span class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 transition-colors cursor-pointer"
                  :class="copied ? 'text-green-600' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50'"
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
                <svg x-show="!copied" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <svg x-show="copied" x-cloak class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
            </span>
        </div>
    </div>

    {{-- Action buttons (visible on hover) --}}
    @if($snippet->canBeEditedBy(request()))
        <div class="absolute right-3 top-3 flex items-center gap-1 rounded-lg border border-gray-100 bg-white p-1 shadow-sm opacity-0 transition-opacity duration-150 group-hover:opacity-100">
            <a href="{{ route('snippets.edit', $snippet->alias) }}"
               onclick="event.preventDefault(); event.stopPropagation();"
               x-on:click.prevent="$dispatch('open-edit-modal', '{{ $snippet->alias }}')"
               class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-indigo-600 cursor-pointer"
               title="Editar">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <button
                type="button"
                @click.stop="showDeleteConfirm = true"
                class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 hover:bg-red-50 hover:text-red-600 cursor-pointer"
                title="Eliminar">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div x-show="showDeleteConfirm" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-2xl" @click.away="showDeleteConfirm = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="mb-1 mt-3 text-lg font-bold text-gray-900">Eliminar anotador</h3>
            <p class="mb-5 text-sm text-gray-500">Se eliminará permanentemente <strong class="font-mono text-gray-700">{{ $snippet->alias }}</strong>. Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-2">
                <button type="button" @click="showDeleteConfirm = false"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">
                    Cancelar
                </button>
                <form action="{{ route('snippets.destroy', $snippet->alias) }}" method="POST" @click.stop>
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 cursor-pointer">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
