@extends('layouts.app')

@section('title', $snippet->title ?: 'Cortito - '.$snippet->alias)

@section('content')
@if(! $unlocked)
    <div class="mx-auto max-w-md space-y-4">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-6 text-center">
            <svg class="mx-auto h-10 w-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            <h2 class="mt-3 text-lg font-semibold text-gray-800">Este anotador está protegido</h2>
            <p class="mt-1 text-sm text-gray-500">Ingresá la contraseña para ver el contenido.</p>

            <form method="POST" action="{{ route('snippets.show.password', $snippet->alias) }}" class="mt-4 space-y-3">
                @csrf
                <input
                    type="password"
                    name="password"
                    placeholder="Contraseña"
                    required
                    autofocus
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
                @error('password')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Desbloquear
                </button>
            </form>
        </div>
    </div>
@else
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">
                    {{ $snippet->title ?: $snippet->alias }}
                </h1>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                    <span class="font-mono">{{ $snippet->alias }}</span>
                    <span>&middot;</span>
                    <span>{{ $snippet->created_at->diffForHumans() }}</span>
                    @if($snippet->is_edited && $snippet->edited_at)
                        <span>&middot;</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="rounded bg-yellow-100 px-1.5 py-0.5 text-yellow-700">editado</span>
                            {{ $snippet->edited_at->diffForHumans() }}
                        </span>
                    @endif
                    <span>&middot;</span>
                    <span>{{ $snippet->views_count }} {{ Str::plural('vista', $snippet->views_count) }}</span>
                    <span>&middot;</span>
                    <span class="rounded bg-gray-100 px-1.5 py-0.5">{{ $snippet->content_type }}</span>
                    @if($snippet->language)
                        <span class="rounded bg-gray-100 px-1.5 py-0.5">{{ $snippet->language }}</span>
                    @endif
                    @if($snippet->isProtected())
                        <span class="rounded bg-amber-100 px-1.5 py-0.5 text-amber-700">protegido</span>
                    @endif
                    @if($snippet->expires_at)
                        <span>&middot;</span>
                        <span>expira {{ $snippet->expires_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('home') }}" class="text-xs text-indigo-600 hover:text-indigo-800 whitespace-nowrap">
                + Crear otro
            </a>
        </div>

        {{-- Acciones (editar/eliminar) --}}
        @if($snippet->canBeEditedBy(request()))
            <div class="flex items-center gap-2" x-data="{ showDeleteConfirm: false }">
                <a href="{{ route('snippets.edit', $snippet->alias) }}"
                   class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Editar
                </a>
                <button
                    type="button"
                    @click="showDeleteConfirm = true"
                    class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-white px-3 py-1.5 text-xs text-red-600 hover:bg-red-50"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                    Eliminar
                </button>

                {{-- Modal confirmar eliminación --}}
                <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition>
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-4" @click.away="showDeleteConfirm = false">
                        <h3 class="text-lg font-bold mb-2 text-gray-900">¿Eliminar anotador?</h3>
                        <p class="text-sm text-gray-600 mb-4">Esta acción no se puede deshacer. Se eliminará permanentemente.</p>
                        <div class="flex gap-2 justify-end">
                            <button type="button" @click="showDeleteConfirm = false"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
                                Cancelar
                            </button>
                            <form action="{{ route('snippets.destroy', $snippet->alias) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Contenido --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            @if($snippet->content_type === 'code')
                <pre class="overflow-x-auto p-4 text-sm leading-relaxed bg-gray-900 text-gray-100"><code>{{ $snippet->content }}</code></pre>
            @elseif($snippet->content_type === 'markdown')
                <div class="prose prose-sm max-w-none p-4">
                    {!! Str::markdown($snippet->content) !!}
                </div>
            @elseif($snippet->content_type === 'html')
                <div class="p-4">{!! $snippet->content !!}</div>
            @else
                <pre class="whitespace-pre-wrap p-4 text-sm leading-relaxed">{{ $snippet->content }}</pre>
            @endif
        </div>

        {{-- Compartir --}}
        <div class="flex items-center gap-2">
            <input
                type="text"
                value="{{ route('snippets.show', $snippet->alias) }}"
                readonly
                class="flex-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-500"
                onclick="this.select()"
            >
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
                x-data="{ copied: false }"
                class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-50"
            >
                <span x-text="copied ? 'Copiado!' : 'Copiar enlace'"></span>
            </button>
        </div>
    </div>
@endif
@endsection
