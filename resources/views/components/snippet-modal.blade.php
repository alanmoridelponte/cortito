@props(['contentTypes', 'maxChars', 'alias' => null, 'anonymousCount' => 0, 'anonymousLimit' => 10])

<div x-data="snippetModal({{ json_encode($contentTypes) }}, {{ $maxChars }}, {{ $anonymousCount }}, {{ $anonymousLimit }})"
     x-on:open-create-modal.window="openCreate()"
     x-on:open-edit-modal.window="openEdit($event.detail)"
     @keydown.escape.window="close()">

    {{-- Overlay --}}
    <div x-show="isOpen" x-cloak
         class="fixed inset-0 z-50 bg-ink/40 backdrop-blur-[2px]"
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
         class="fixed inset-y-0 right-0 z-50 flex w-full md:w-3/4 lg:w-1/2"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">

        <div class="flex w-full flex-col bg-warm-white shadow-2xl" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-border-warm px-6 py-5">
                <div>
                    <h2 class="font-display text-lg font-bold text-ink" x-text="isEditing ? 'Editar ' + editAlias : 'Crear cortito'"></h2>
                    <p class="mt-0.5 text-xs text-graphite-light" x-text="isEditing ? 'Modifica el contenido de tu cortito' : 'Elegi el tipo y escribi tu contenido'"></p>
                </div>
                <button @click="close()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-graphite-light transition-colors hover:bg-cream-dark hover:text-ink">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 flex flex-col overflow-y-auto px-6 py-6 slide-over-scrollbar">
                {{-- Anonymous limit warning --}}
                <template x-if="!isEditing && atLimit">
                    <div class="mb-5 rounded-lg border border-amber/20 bg-amber-light p-3.5 text-sm text-amber">
                        Alcanzaste el limite de <span x-text="anonymousLimit"></span> cortitos gratuitos.
                        <a href="#" class="font-semibold underline decoration-amber/40 underline-offset-2 hover:text-amber hover:decoration-amber">Registrate</a> para crear ilimitados.
                    </div>
                </template>

                {{-- Content type selector --}}
                <div class="mb-6">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-graphite-light">Tipo de contenido</label>
                    <div class="flex gap-2">
                        @foreach($contentTypes as $value => $label)
                            <label class="flex-1 cursor-pointer rounded-lg border-2 px-4 py-3 text-center transition-all duration-150"
                                :class="form.contentType === '{{ $value }}'
                                    ? @js(match($value) {
                                        'url' => 'border-coral bg-coral-light text-coral',
                                        'text' => 'border-mint bg-mint-light text-mint',
                                        default => 'border-violet bg-violet-light text-violet',
                                    })
                                    : 'border-border-warm text-graphite hover:border-graphite-light hover:bg-cream'"
                            >
                                <input type="radio" name="content_type" value="{{ $value }}" x-model="form.contentType" class="sr-only">
                                <span class="font-display text-sm font-semibold">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Alias section (create only) --}}
                <template x-if="!isEditing">
                    <div class="mb-6">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-graphite-light">Direccion del cortito</label>
                        <div class="alias-input-wrapper flex flex-col sm:flex-row sm:items-stretch gap-2">
                            <div class="relative flex-1">
                                <span class="alias-domain pt-2.5">cortito.ar/</span>
                                <input
                                    type="text"
                                    x-model="form.alias"
                                    @input="onAliasInput()"
                                    maxlength="250"
                                    placeholder="tu-alias"
                                    class="w-full rounded-lg border-2 border-border-warm bg-warm-white py-2 pr-3 pl-[6.5rem] font-mono text-sm font-medium text-ink placeholder-graphite-light transition-all focus:outline-none"
                                    :class="{
                                        'border-red-400 focus:ring-2 focus:ring-red-500/20': aliasAvailable === false,
                                        'border-mint focus:ring-2 focus:ring-mint/20': aliasAvailable === true,
                                        'focus:border-violet focus:ring-2 focus:ring-violet-ring': aliasAvailable === null,
                                    }"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasChecking">
                                    <svg class="h-4 w-4 animate-spin text-graphite-light" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasAvailable === true && !aliasChecking">
                                    <span class="text-sm font-bold text-mint">&#10003;</span>
                                </div>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3" x-show="aliasAvailable === false && !aliasChecking">
                                    <span class="text-sm font-bold text-danger">&#10007;</span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="reroll()"
                                :disabled="rerolling"
                                class="btn-press inline-flex items-center justify-center gap-1.5 rounded-lg border-2 border-border-warm bg-warm-white px-4 py-2 text-sm font-medium text-graphite transition-all hover:border-violet hover:text-violet disabled:opacity-50 w-full sm:w-auto">
                                <svg class="h-4 w-4" :class="{'animate-spin': rerolling}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4v5h5M20 20v-5h-5"/>
                                    <path d="M20.49 9A9 9 0 005.64 5.64L4 4m16 16l-1.64-1.64A9 9 0 014.51 15"/>
                                </svg>
                                Sugerir
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-graphite-light">Minimo 5 caracteres. Solo minusculas, numeros, puntos y guiones.</p>
                        <template x-if="errors.alias">
                            <div class="mt-1.5 space-y-0.5">
                                <template x-for="(err, i) in errors.alias" :key="i">
                                    <p class="text-xs text-danger" x-text="err"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- URL input (url type only) --}}
                <template x-if="form.contentType === 'url'">
                    <div class="mb-6">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-graphite-light">Enlace destino</label>
                        <input
                            type="url"
                            x-model="form.content"
                            placeholder="https://ejemplo.com/pagina"
                            required
                            class="w-full rounded-lg border-2 border-border-warm bg-warm-white px-4 py-3 text-sm text-ink placeholder-graphite-light transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
                        <p class="mt-2 text-xs text-graphite-light">Si el visitante llega con datos en el enlace, se mantienen al redirigir.</p>
                        <template x-if="errors.content">
                            <p class="mt-1.5 text-xs text-danger" x-text="errors.content[0]"></p>
                        </template>
                    </div>
                </template>

                {{-- Text content (text type only) --}}
                <template x-if="form.contentType === 'text'">
                    <div class="flex flex-col flex-1 mb-6">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-graphite-light">Contenido</label>
                        <textarea
                            x-model="form.content"
                            placeholder="Escribi o pega tu nota aca..."
                            required
                            rows="14"
                            class="w-full flex-1 resize-none rounded-lg border-2 border-border-warm bg-cream/50 px-4 py-3 font-mono text-sm text-ink placeholder-graphite-light leading-relaxed transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring focus:bg-warm-white"
                        ></textarea>
                        <div class="mt-2 flex items-center justify-between">
                            <template x-if="errors.content">
                                <p class="text-xs text-danger" x-text="errors.content[0]"></p>
                            </template>
                            <p class="ml-auto font-mono text-xs text-graphite-light" x-text="form.content.length.toLocaleString('es-AR') + ' / ' + maxChars.toLocaleString('es-AR') + ' caracteres'"></p>
                        </div>
                            <div class="mt-4">
                                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-graphite-light">Contraseña (opcional)</label>
                                <input
                                    type="password"
                                    x-model="form.password"
                                    :placeholder="isEditing && hasPassword ? 'Dejar vacio para mantener la actual' : 'Minimo 4 caracteres'"
                                    minlength="4"
                                    maxlength="255"
                                    class="w-full rounded-lg border-2 border-border-warm bg-warm-white px-4 py-2.5 text-sm text-ink placeholder-graphite-light transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
                                <p class="mt-1.5 text-xs text-graphite-light">Protege el contenido para que solo quien tenga la contraseña pueda verlo.</p>
                                <template x-if="errors.password">
                                    <p class="mt-1 text-xs text-danger" x-text="errors.password[0]"></p>
                                </template>
                            </div>
                    </div>
                </template>

                {{-- Premium options (authenticated only) --}}
                @auth
                    <div class="rounded-xl border border-violet/20 bg-violet-light/50 p-5">
                        <button type="button" @click="showPremium = !showPremium"
                                class="flex w-full items-center justify-between text-sm font-semibold text-violet">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                </svg>
                                Opciones premium
                            </span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-180': showPremium}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showPremium" x-collapse class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-graphite">Expiracion</label>
                                <select x-model="form.ttl" class="w-full rounded-lg border-2 border-border-warm bg-warm-white px-3 py-2.5 text-sm text-ink transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
                                    <option value="7d">7 dias</option>
                                    <option value="30d">30 dias</option>
                                    <option value="90d">90 dias</option>
                                    <option value="1y">1 anio</option>
                                    <option value="never">Nunca</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-graphite">Privacidad</label>
                                <select x-model="form.isPublic" class="w-full rounded-lg border-2 border-border-warm bg-warm-white px-3 py-2.5 text-sm text-ink transition-all focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
                                    <option value="1">Publico</option>
                                    <option value="0">Privado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Server error --}}
                <template x-if="serverError">
                    <div class="mt-5 rounded-lg border border-danger/30 bg-danger-light p-3.5 text-sm font-medium text-danger" x-text="serverError"></div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="border-t border-border-warm bg-cream/40 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-graphite-light">
                        @auth
                            <span class="inline-flex items-center gap-1.5">
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-violet"></span>
                                Premium &middot; Expira: <span class="font-medium text-ink" x-text="ttlLabel"></span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5">
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-graphite-light"></span>
                                Gratis &middot; <span x-text="anonymousCount + '/' + anonymousLimit"></span> &middot; Expira en 24hs
                            </span>
                        @endauth
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="close()"
                                class="rounded-lg border border-border-warm bg-warm-white px-4 py-2.5 text-sm font-medium text-graphite transition-colors hover:bg-cream-dark">
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="submit()"
                            :disabled="submitting || (!isEditing && (atLimit || !form.content.trim() || aliasAvailable !== true || aliasChecking || form.alias.length < 5 || !/^[a-z0-9][a-z0-9.\-]*$/.test(form.alias))) || (isEditing && !form.content.trim()) || (form.contentType === 'url' && !isValidUrl(form.content))"
                            class="btn-press inline-flex items-center gap-2 rounded-lg bg-violet px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet/20 transition-all duration-150 hover:bg-violet-hover hover:shadow-md hover:shadow-violet/25 focus:outline-none focus-ring-violet disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none">
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
            contentType: 'url',
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
        hasPassword: false,
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
                    this.hasPassword = data.has_password || false;
                }
            } catch {
                this.serverError = 'No se pudo cargar el cortito.';
            }
        },

        resetForm() {
            this.form = { alias: '', content: '', contentType: 'url', title: '', ttl: '7d', isPublic: '1', password: '' };
            this.aliasChecking = false;
            this.aliasAvailable = null;
            this.submitting = false;
            this.serverError = null;
            this.errors = {};
            this.showPremium = false;
            this.hasPassword = false;
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
            @endauth

            if (this.form.password) {
                body.password = this.form.password;
            }

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
