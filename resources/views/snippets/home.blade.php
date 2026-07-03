@extends('layouts.app')

@section('title', 'Cortito - Crear anotador')

@section('content')
<div class="grid grid-cols-1 gap-8 md:grid-cols-3">

    {{-- Columna izquierda: Formulario crear --}}
    <div class="md:col-span-2">
        <div x-data="snippetForm()" class="space-y-6">

            {{-- Seccion Alias --}}
            <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                @if(isset($anonymousLimit) && $anonymousCount >= $anonymousLimit)
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Alcanzaste el limite de {{ $anonymousLimit }} anotadores gratuitos.
                        <a href="#" class="font-medium underline hover:text-amber-900">Registrate</a> para crear ilimitados.
                    </div>
                @endif

                <h2 class="mb-3 text-sm font-medium text-gray-700">Direccion del anotador</h2>
                <div class="flex items-stretch gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-400">cortito.ar/</span>
                        <input
                            type="text"
                            x-model="alias"
                            @input="onAliasInput()"
                            maxlength="250"
                            class="w-full rounded-md border border-gray-300 py-2 pr-3 pl-20 text-sm font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-500"
                            :class="{
                                'border-red-300': aliasAvailable === false,
                                'border-green-400': aliasAvailable === true,
                            }"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasChecking">
                            <svg class="h-4 w-4 animate-spin text-gray-400" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasAvailable === true && !aliasChecking">
                            <span class="text-sm text-green-500">&#10003;</span>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasAvailable === false && !aliasChecking">
                            <span class="text-sm text-red-400">&#10007;</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="reroll()"
                        :disabled="rerolling"
                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                    >
                        <svg class="h-4 w-4" :class="{'animate-spin': rerolling}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4v5h5M20 20v-5h-5"/>
                            <path d="M20.49 9A9 9 0 005.64 5.64L4 4m16 16l-1.64-1.64A9 9 0 014.51 15"/>
                        </svg>
                        Alias aleatorio
                    </button>
                </div>
                <p class="mt-2 text-xs text-gray-400">Puedes personalizar la direccion. Minimo 5 caracteres. Solo minusculas, numeros, puntos y guiones.</p>
                <template x-if="errors.alias">
                    <div class="mt-1 space-y-0.5">
                        <template x-for="(err, i) in errors.alias" :key="i">
                            <p class="text-xs text-red-500" x-text="err"></p>
                        </template>
                    </div>
                </template>
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
                <p class="mt-1 text-xs text-gray-400" x-text="content.length.toLocaleString('es-AR') + ' / ' + maxSize + ' caracteres'"></p>
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

            {{-- Boton Crear --}}
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-400">
                    @auth
                        Premium &middot; Expiracion: <span x-text="ttlLabel"></span>
                    @else
                        Gratis &middot; <span x-text="anonymousCount + '/' + anonymousLimit"></span> anotadores &middot; Expira en 24hs
                    @endauth
                </div>
                <button
                    type="button"
                    @click="submit()"
                    :disabled="submitting || atLimit || !content.trim() || aliasAvailable !== true || aliasChecking || alias.length < 5 || !/^[a-z0-9][a-z0-9.\-]*$/.test(alias)"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="submitting ? 'Creando...' : 'Crear anotador'"></span>
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
function snippetForm() {
    return {
        alias: '{{ $alias }}',
        aliasChecking: false,
        aliasAvailable: null,
        rerolling: false,
        content: '',
        contentType: 'text',
        language: '',
        title: '',
        ttl: '7d',
        isPublic: '1',
        password: '',
        submitting: false,
        serverError: null,
        errors: {},
        debounceTimer: null,
        anonymousCount: {{ $anonymousCount ?? 0 }},
        anonymousLimit: {{ $anonymousLimit ?? 10 }},
        maxChars: {{ $maxChars }},

        get atLimit() {
            return this.anonymousCount >= this.anonymousLimit;
        },

        get maxSize() {
            return this.maxChars.toLocaleString('es-AR');
        },

        get ttlLabel() {
            return { '7d': '7 dias', '30d': '30 dias', '90d': '90 dias', '1y': '1 anio', 'never': 'Nunca' }[this.ttl] || this.ttl;
        },

        init() {
            if (this.alias.length >= 5) {
                this.checkAlias();
            }
        },

        onAliasInput() {
            this.aliasAvailable = null;
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                if (this.alias.length >= 5) {
                    this.checkAlias();
                }
            }, 400);
        },

        async checkAlias() {
            this.aliasChecking = true;
            try {
                const res = await fetch(`{{ url('') }}/snippets/check-alias/${this.alias}`);
                const data = await res.json();
                this.aliasAvailable = data.available;
            } catch {
                this.aliasAvailable = null;
            } finally {
                this.aliasChecking = false;
            }
        },

        async reroll() {
            this.rerolling = true;
            try {
                const res = await fetch('{{ route("snippets.reroll") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.alias = data.alias;
                this.aliasAvailable = true;
            } catch {
                // silently fail
            } finally {
                this.rerolling = false;
            }
        },

        async submit() {
            this.submitting = true;
            this.serverError = null;
            this.errors = {};

            if (this.atLimit) {
                this.serverError = 'Alcanzaste el limite de anotadores gratuitos. Registrate para crear ilimitados.';
                this.submitting = false;
                return;
            }

            if (this.alias.length < 5) {
                this.errors = { alias: ['El alias debe tener al menos 5 caracteres.'] };
                this.submitting = false;
                return;
            }
            if (!/^[a-z0-9][a-z0-9.\-]*$/.test(this.alias)) {
                this.errors = { alias: ['El alias solo puede contener minusculas, numeros, puntos y guiones.'] };
                this.submitting = false;
                return;
            }
            if (this.aliasAvailable !== true) {
                this.errors = { alias: ['Este alias ya esta en uso.'] };
                this.submitting = false;
                return;
            }

            const body = {
                alias: this.alias,
                content: this.content,
                content_type: this.contentType,
                language: this.language || null,
                title: this.title || null,
            };

            @auth
                body.ttl = this.ttl;
                body.is_public = this.isPublic === '1';
                body.password = this.password || null;
            @endauth

            try {
                const res = await fetch('{{ route("snippets.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
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
