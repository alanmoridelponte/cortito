@props(['contentTypes', 'maxChars', 'alias' => null, 'anonymousCount' => 0, 'anonymousLimit' => 10])

<div x-data="snippetModal({{ json_encode($contentTypes) }}, {{ $maxChars }}, {{ $anonymousCount }}, {{ $anonymousLimit }})"
     x-on:open-create-modal.window="openCreate()"
     x-on:open-edit-modal.window="openEdit($event.detail)"
     @keydown.escape.window="close()">

    {{-- Overlay --}}
    <div x-show="isOpen" x-cloak
         class="fixed inset-0 z-50 bg-black/40"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()">
    </div>

    {{-- Slide-over panel --}}
    <div x-show="isOpen" x-cloak
         class="fixed inset-y-0 right-0 z-50 flex w-full md:w-3/4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">

        <div class="flex w-full flex-col bg-white shadow-2xl" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900" x-text="isEditing ? 'Editar ' + editAlias : 'Crear cortito'"></h2>
                <button @click="close()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">
                {{-- Anonymous limit warning --}}
                <template x-if="!isEditing && atLimit">
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        Alcanzaste el limite de <span x-text="anonymousLimit"></span> cortitos gratuitos.
                        <a href="#" class="font-medium underline hover:text-amber-900">Registrate</a> para crear ilimitados.
                    </div>
                </template>

                {{-- Content type --}}
                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Tipo de contenido</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($contentTypes as $value => $label)
                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3.5 py-2 text-sm font-medium transition-all duration-150"
                                :class="form.contentType === '{{ $value }}'
                                    ? @js(match($value) {
                                        'text' => 'border-green-300 bg-green-50 text-green-700 ring-1 ring-green-200',
                                        'url' => 'border-orange-300 bg-orange-50 text-orange-700 ring-1 ring-orange-200',
                                        default => 'border-indigo-300 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
                                    })
                                    : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                            >
                                <input type="radio" name="content_type" value="{{ $value }}" x-model="form.contentType" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Alias section (create only) --}}
                <template x-if="!isEditing">
                    <div class="mb-5">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Direccion del cortito</label>
                        <div class="flex items-stretch gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-400">cortito.ar/</span>
                                <input
                                    type="text"
                                    x-model="form.alias"
                                    @input="onAliasInput()"
                                    maxlength="250"
                                    class="w-full rounded-lg border border-gray-300 py-2.5 pr-3 pl-20 text-sm font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500/20': aliasAvailable === false,
                                        'border-green-400 focus:border-green-500 focus:ring-green-500/20': aliasAvailable === true,
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
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                                <svg class="h-4 w-4" :class="{'animate-spin': rerolling}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4v5h5M20 20v-5h-5"/>
                                    <path d="M20.49 9A9 9 0 005.64 5.64L4 4m16 16l-1.64-1.64A9 9 0 014.51 15"/>
                                </svg>
                                Sugerir
                            </button>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">Minimo 5 caracteres. Solo minusculas, numeros, puntos y guiones.</p>
                        <template x-if="errors.alias">
                            <div class="mt-1 space-y-0.5">
                                <template x-for="(err, i) in errors.alias" :key="i">
                                    <p class="text-xs text-red-500" x-text="err"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- URL input (url type only) --}}
                <template x-if="form.contentType === 'url'">
                    <div class="mb-5">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">URL destino</label>
                        <input
                            type="url"
                            x-model="form.content"
                            placeholder="https://ejemplo.com/pagina"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <p class="mt-1.5 text-xs text-gray-400">Si el visitante llega con datos en el enlace, se mantienen al redirigir.</p>
                        <template x-if="errors.content">
                            <p class="mt-1 text-xs text-red-500" x-text="errors.content[0]"></p>
                        </template>
                    </div>
                </template>

                {{-- Text content (text type only) --}}
                <template x-if="form.contentType === 'text'">
                    <div class="mb-5">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Contenido</label>
                        <textarea
                            x-model="form.content"
                            placeholder="Escribi o pega tu nota aca..."
                            required
                            rows="14"
                            class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 font-mono text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        ></textarea>
                        <div class="mt-1.5 flex items-center justify-between">
                            <template x-if="errors.content">
                                <p class="text-xs text-red-500" x-text="errors.content[0]"></p>
                            </template>
                            <p class="ml-auto text-xs text-gray-400" x-text="form.content.length.toLocaleString('es-AR') + ' / ' + maxChars.toLocaleString('es-AR') + ' caracteres'"></p>
                        </div>
                    </div>
                </template>

                {{-- Premium options (authenticated only) --}}
                @auth
                    <div class="rounded-lg border border-indigo-100 bg-indigo-50/50 p-4">
                        <button type="button" @click="showPremium = !showPremium"
                                class="flex w-full items-center justify-between text-sm font-medium text-indigo-800">
                            <span>Opciones premium</span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-180': showPremium}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showPremium" x-collapse class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Expiracion</label>
                                <select x-model="form.ttl" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="7d">7 dias</option>
                                    <option value="30d">30 dias</option>
                                    <option value="90d">90 dias</option>
                                    <option value="1y">1 anio</option>
                                    <option value="never">Nunca</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Privacidad</label>
                                <select x-model="form.isPublic" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="1">Publico</option>
                                    <option value="0">Privado</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">Contrasena</label>
                                <input
                                    type="password"
                                    x-model="form.password"
                                    placeholder="Minimo 4 caracteres"
                                    minlength="4"
                                    maxlength="255"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Server error --}}
                <template x-if="serverError">
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" x-text="serverError"></div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-gray-400">
                        @auth
                            Premium &middot; Expira: <span x-text="ttlLabel"></span>
                        @else
                            Gratis &middot; <span x-text="anonymousCount + '/' + anonymousLimit"></span> &middot; Expira en 24hs
                        @endauth
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="close()"
                                class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="submit()"
                            :disabled="submitting || (!isEditing && (atLimit || !form.content.trim() || aliasAvailable !== true || aliasChecking || form.alias.length < 5 || !/^[a-z0-9][a-z0-9.\-]*$/.test(form.alias))) || (isEditing && !form.content.trim()) || (form.contentType === 'url' && !isValidUrl(form.content))"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <svg x-show="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="submitting
                                ? (isEditing ? 'Guardando...' : 'Creando...')
                                : (isEditing ? 'Guardar cambios' : 'Crear cortito')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function snippetModal(contentTypes, maxChars, anonymousCount, anonymousLimit) {
    return {
        isOpen: false,
        isEditing: false,
        editAlias: null,
        form: {
            alias: '',
            content: '',
            contentType: 'text',
            language: '',
            title: '',
            ttl: '7d',
            isPublic: '1',
            password: '',
        },
        aliasChecking: false,
        aliasAvailable: null,
        rerolling: false,
        submitting: false,
        serverError: null,
        errors: {},
        showPremium: false,
        debounceTimer: null,
        anonymousCount: anonymousCount,
        anonymousLimit: anonymousLimit,
        maxChars: maxChars,
        contentTypes: contentTypes,

        get atLimit() {
            return this.anonymousCount >= this.anonymousLimit;
        },

        get ttlLabel() {
            return { '7d': '7 dias', '30d': '30 dias', '90d': '90 dias', '1y': '1 anio', 'never': 'Nunca' }[this.form.ttl] || this.form.ttl;
        },

        isValidUrl(str) {
            if (!str || !str.trim()) return false;
            try {
                const url = new URL(str);
                return url.protocol === 'http:' || url.protocol === 'https:';
            } catch {
                return false;
            }
        },

        async openCreate() {
            this.isEditing = false;
            this.editAlias = null;
            this.resetForm();
            try {
                const res = await fetch('{{ route("snippets.reroll") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.form.alias = data.alias;
                this.aliasAvailable = true;
            } catch {
                this.form.alias = '';
                this.aliasAvailable = null;
            }
            this.isOpen = true;
        },

        async openEdit(alias) {
            this.isEditing = true;
            this.editAlias = alias;
            this.resetForm();
            this.form.alias = alias;
            this.aliasAvailable = true;
            this.isOpen = true;

            try {
                const res = await fetch(`{{ url('') }}/${alias}/edit`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) {
                    const data = await res.json();
                    this.form.content = data.content || '';
                    this.form.contentType = data.content_type || 'text';
                    this.form.title = data.title || '';
                    if (data.ttl) this.form.ttl = data.ttl;
                    if (data.is_public !== undefined) this.form.isPublic = data.is_public ? '1' : '0';
                }
            } catch {
                this.serverError = 'No se pudo cargar el cortito.';
            }
        },

        resetForm() {
            this.form = { alias: '', content: '', contentType: 'text', title: '', ttl: '7d', isPublic: '1', password: '' };
            this.aliasChecking = false;
            this.aliasAvailable = null;
            this.submitting = false;
            this.serverError = null;
            this.errors = {};
            this.showPremium = false;
        },

        close() {
            this.isOpen = false;
        },

        onAliasInput() {
            this.aliasAvailable = null;
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                if (this.form.alias.length >= 5) {
                    this.checkAlias();
                }
            }, 400);
        },

        async checkAlias() {
            this.aliasChecking = true;
            try {
                const res = await fetch(`{{ url('') }}/snippets/check-alias/${this.form.alias}`);
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
                this.form.alias = data.alias;
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

            if (!this.isEditing && this.atLimit) {
                this.serverError = 'Alcanzaste el limite de cortitos gratuitos. Registrate para crear ilimitados.';
                this.submitting = false;
                return;
            }

            if (!this.isEditing) {
                if (this.form.alias.length < 5) {
                    this.errors = { alias: ['El alias debe tener al menos 5 caracteres.'] };
                    this.submitting = false;
                    return;
                }
                if (!/^[a-z0-9][a-z0-9.\-]*$/.test(this.form.alias)) {
                    this.errors = { alias: ['El alias solo puede contener minusculas, numeros, puntos y guiones.'] };
                    this.submitting = false;
                    return;
                }
                if (this.aliasAvailable !== true) {
                    this.errors = { alias: ['Este alias ya esta en uso.'] };
                    this.submitting = false;
                    return;
                }
            }

            const body = {
                alias: this.form.alias,
                content: this.form.content,
                content_type: this.form.contentType,
                title: this.form.title || null,
            };

            @auth
                body.ttl = this.form.ttl;
                body.is_public = this.form.isPublic === '1';
                body.password = this.form.password || null;
            @endauth

            const url = this.isEditing
                ? `{{ url('') }}/${this.editAlias}`
                : '{{ route("snippets.store") }}';

            const headers = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };

            if (this.isEditing) {
                body._method = 'PUT';
                headers['X-HTTP-Method-Override'] = 'PUT';
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: headers,
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
