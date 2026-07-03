@extends('layouts.app')

@section('title', 'Cortito - Editar '.$snippet->alias)

@section('content')
<div class="grid grid-cols-1 gap-8 md:grid-cols-3">

    {{-- Columna izquierda: Formulario editar --}}
    <div class="md:col-span-2">
        <div x-data="editForm()" class="space-y-6">

            <div class="flex items-center gap-2">
                <a href="{{ route('snippets.show', $snippet->alias) }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Volver</a>
                <span class="text-gray-300">|</span>
                <h1 class="text-lg font-semibold text-gray-900">Editando anotador</h1>
                @if($snippet->is_edited)
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                        Editado @if($snippet->edited_at)({{ $snippet->edited_at->diffForHumans() }})@endif
                    </span>
                @endif
            </div>

            {{-- Seccion Alias --}}
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-medium text-gray-700">Direccion del anotador</h2>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-400">cortito.ar/</span>
                    <input
                        type="text"
                        value="{{ $snippet->alias }}"
                        readonly
                        class="w-full rounded-md border border-gray-200 bg-gray-50 py-2 pr-3 pl-20 text-sm font-mono text-gray-500"
                    >
                </div>
            </section>

            {{-- Seccion Contenido --}}
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-medium text-gray-700">Contenido</h2>

                <div class="mb-3 flex flex-wrap gap-2">
                    @foreach($contentTypes as $value => $label)
                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-3 py-1.5 text-sm transition-colors"
                            :class="contentType === '{{ $value }}' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        >
                            <input type="radio" name="content_type" value="{{ $value }}" x-model="contentType" class="sr-only">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <template x-if="contentType === 'code'">
                    <div class="mb-3">
                        <input
                            type="text"
                            x-model="language"
                            placeholder="Lenguaje (ej: javascript, python, php)"
                            maxlength="50"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                </template>

                <input
                    type="text"
                    x-model="title"
                    placeholder="Titulo (opcional)"
                    maxlength="255"
                    class="mb-3 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >

                <textarea
                    x-model="content"
                    placeholder="Escribi o pega tu snippet aca..."
                    required
                    rows="12"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="contentType === 'code' ? 'bg-gray-900 text-gray-100' : ''"
                ></textarea>
                <template x-if="errors.content">
                    <p class="mt-1 text-xs text-red-500" x-text="errors.content[0]"></p>
                </template>
            </section>

            {{-- Seccion Premium (solo logueados) --}}
            @auth
            <section class="rounded-lg border border-indigo-100 bg-indigo-50/50 p-5">
                <h2 class="mb-3 text-sm font-medium text-indigo-800">Opciones premium</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Expiracion</label>
                        <select x-model="ttl" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="7d">7 dias</option>
                            <option value="30d">30 dias</option>
                            <option value="90d">90 dias</option>
                            <option value="1y">1 anio</option>
                            <option value="never">Nunca</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Privacidad</label>
                        <select x-model="isPublic" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="1">Publico</option>
                            <option value="0">Privado</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Contrasena (opcional)</label>
                        <input
                            type="password"
                            x-model="password"
                            placeholder="Minimo 4 caracteres"
                            minlength="4"
                            maxlength="255"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                </div>
            </section>
            @endauth

            {{-- Boton Guardar --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('snippets.show', $snippet->alias) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                <button
                    type="button"
                    @click="submit()"
                    :disabled="submitting || !content.trim()"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="submitting ? 'Guardando...' : 'Guardar cambios'"></span>
                </button>
            </div>

            {{-- Errores generales del servidor --}}
            <template x-if="serverError">
                <div class="rounded-md bg-red-50 p-3 text-sm text-red-700" x-text="serverError"></div>
            </template>
        </div>
    </div>

    {{-- Columna derecha: Listado --}}
    <div class="md:col-span-1">
        @include('snippets._list')
    </div>

</div>

<script>
function editForm() {
    return {
        content: @js($snippet->content),
        contentType: '{{ $snippet->content_type }}',
        language: @js($snippet->language ?? ''),
        title: @js($snippet->title ?? ''),
        @auth
        ttl: '{{ $snippet->expires_at ? ($snippet->expires_at->diffInDays(now()) <= 7 ? "7d" : ($snippet->expires_at->diffInDays(now()) <= 30 ? "30d" : ($snippet->expires_at->diffInDays(now()) <= 90 ? "90d" : "1y"))) : "never" }}',
        isPublic: '{{ $snippet->is_public ? "1" : "0" }}',
        password: '',
        @endauth
        submitting: false,
        serverError: null,
        errors: {},

        async submit() {
            this.submitting = true;
            this.serverError = null;
            this.errors = {};

            const body = {
                content: this.content,
                content_type: this.contentType,
                language: this.language || null,
                title: this.title || null,
                _method: 'PUT',
            };

            @auth
                body.ttl = this.ttl;
                body.is_public = this.isPublic === '1';
                body.password = this.password || null;
            @endauth

            try {
                const res = await fetch('{{ route("snippets.update", $snippet->alias) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify(body),
                });

                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }

                if (res.status === 422) {
                    const data = await res.json();
                    this.errors = data.errors || {};
                    return;
                }

                if (!res.ok) {
                    this.serverError = 'Ocurrio un error inesperado. Intenta de nuevo.';
                }
            } catch {
                this.serverError = 'No se pudo conectar al servidor. Intenta de nuevo.';
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
@endsection
