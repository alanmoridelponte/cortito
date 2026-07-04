@extends('layouts.app')

@section('title', $snippet->title ?: 'Cortito - '.$snippet->alias)

@section('content')
@if(! $unlocked)
    <div class="mx-auto max-w-md space-y-4">
        <div class="rounded-2xl border border-border-warm bg-warm-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-light">
                <svg class="h-7 w-7 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h2 class="font-display text-xl font-bold text-ink">Anotador protegido</h2>
            <p class="mt-2 text-sm text-graphite">Ingresa la contraseña para ver el contenido.</p>

            <form method="POST" action="{{ route('snippets.show.password', $snippet->alias) }}" class="mt-6 space-y-3">
                @csrf
                <input
                    type="password"
                    name="password"
                    placeholder="Contraseña"
                    required
                    autofocus
                    class="w-full rounded-lg border-2 border-border-warm bg-warm-white px-4 py-3 text-sm text-ink placeholder-graphite-light transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
                @error('password')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn-press w-full rounded-lg bg-violet px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-violet/20 transition-all hover:bg-violet-hover hover:shadow-md hover:shadow-violet/25 cursor-pointer">
                    Desbloquear
                </button>
            </form>
        </div>
    </div>
@else
    <div class="space-y-6" x-data="{ showDeleteConfirm: false }">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-sm text-graphite-light">
            <a href="{{ route('home') }}" class="font-medium text-graphite transition-colors hover:text-violet">Cortitos</a>
            <svg class="h-3.5 w-3.5 text-border-warm" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-mono font-medium text-ink">{{ $snippet->alias }}</span>
        </nav>

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="font-display text-2xl font-bold tracking-tight text-ink">
                    {{ $snippet->title ?: $snippet->alias }}
                </h1>
                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                    {{-- Type badge --}}
                    @php
                        $typeConfig = [
                            'text' => ['bg' => 'bg-mint-light', 'text' => 'text-mint', 'label' => 'Texto'],
                            'url' => ['bg' => 'bg-coral-light', 'text' => 'text-coral', 'label' => 'Acortador'],
                        ];
                        $cfg = $typeConfig[$snippet->content_type] ?? ['bg' => 'bg-cream-dark', 'text' => 'text-graphite', 'label' => ucfirst($snippet->content_type)];
                    @endphp
                    <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                        {{ $cfg['label'] }}
                    </span>
                    @if($snippet->language)
                        <span class="rounded-md bg-cream-dark px-2 py-0.5 text-xs font-medium text-graphite">
                            {{ $snippet->language }}
                        </span>
                    @endif
                    @if($snippet->isProtected())
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-light px-2 py-0.5 text-xs font-medium text-amber">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            protegido
                        </span>
                    @endif
                    @if($snippet->is_edited)
                        <span class="rounded-md bg-cream-dark px-2 py-0.5 text-xs font-medium text-graphite">
                            Editado @if($snippet->edited_at) {{ $snippet->edited_at->diffForHumans() }} @endif
                        </span>
                    @endif
                </div>
                <div x-data="{ copied: false, canShare: typeof navigator !== 'undefined' && !!navigator.share }" class="mt-3 flex flex-wrap items-center gap-3 text-xs text-graphite-light">
                    <span>{{ $snippet->created_at->diffForHumans() }}</span>
                    <span class="text-border-warm">&middot;</span>
                    <span class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $snippet->views_count }} {{ Str::plural('vista', $snippet->views_count) }}
                    </span>
                    @if($snippet->expires_at)
                        <span class="text-border-warm">&middot;</span>
                        <span>Expira {{ $snippet->expires_at->diffForHumans() }}</span>
                    @endif
                    <span class="text-border-warm">|</span>
                    <button
                        x-show="canShare"
                        type="button"
                        @click="
                            navigator.share({
                                title: '{{ addslashes($snippet->title ?: $snippet->alias) }}',
                                url: '{{ route('snippets.show', $snippet->alias) }}'
                            }).catch(() => {});
                        "
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-graphite transition-colors hover:text-mint hover:bg-mint-light cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Compartir
                    </button>
                    <button
                        type="button"
                        @click="
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
                        "
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 transition-colors cursor-pointer"
                        :class="copied ? 'text-mint bg-mint-light' : 'text-graphite hover:text-violet hover:bg-violet-light'">
                        <svg x-show="!copied" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                        <svg x-show="copied" x-cloak class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="copied ? 'Copiado!' : 'Copiar enlace'"></span>
                    </button>
                </div>
            </div>

            {{-- Action buttons --}}
            @if($snippet->canBeEditedBy(request()))
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button"
                            @click="$dispatch('open-edit-modal', '{{ $snippet->alias }}')"
                            class="btn-press inline-flex items-center gap-1.5 rounded-lg border border-border-warm bg-warm-white px-3.5 py-2 text-sm font-medium text-graphite transition-all hover:border-violet hover:text-violet cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </button>
                    <button
                        type="button"
                        @click="showDeleteConfirm = true"
                        class="btn-press inline-flex items-center gap-1.5 rounded-lg border border-danger/30 bg-warm-white px-3.5 py-2 text-sm font-medium text-danger transition-all hover:bg-danger-light hover:border-danger/50 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="overflow-hidden rounded-xl border border-border-warm bg-warm-white shadow-sm">
            @if($snippet->content_type === 'url')
                <div class="p-6 text-center">
                    <p class="mb-2 text-sm text-graphite">Redirigiendo a:</p>
                    <a href="{{ $snippet->content }}" class="font-mono text-sm font-medium text-violet break-all transition-colors hover:text-violet-hover">{{ $snippet->content }}</a>
                </div>
            @else
                <div class="border-b border-border-light bg-cream/30 px-6 py-2">
                    <span class="text-xs font-medium text-graphite-light">Contenido</span>
                </div>
                <pre class="whitespace-pre-wrap p-6 font-mono text-sm leading-relaxed text-ink">{{ $snippet->content }}</pre>
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
                <h3 class="mb-1 mt-3 font-display text-lg font-bold text-ink">Eliminar cortito</h3>
                <p class="mb-5 text-sm text-graphite">Se eliminará permanentemente <strong class="font-mono font-semibold text-ink">{{ $snippet->alias }}</strong>. Esta accion no se puede deshacer.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showDeleteConfirm = false"
                            class="rounded-lg border border-border-warm bg-warm-white px-4 py-2 text-sm font-medium text-graphite transition-colors hover:bg-cream-dark cursor-pointer">
                        Cancelar
                    </button>
                    <form action="{{ route('snippets.destroy', $snippet->alias) }}" method="POST">
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
@endif

<x-snippet-modal
    :contentTypes="['text' => 'Texto', 'url' => 'Acortador']"
    :maxChars="65536"
    :anonymousCount="0"
    :anonymousLimit="10"
/>
@endsection
