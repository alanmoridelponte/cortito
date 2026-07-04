@extends('layouts.app')

@section('title', 'Cortito - Mis cortitos')

@section('header-actions')
    <button
        type="button"
        onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
        class="btn-press inline-flex items-center gap-2 rounded-lg bg-violet px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet/20 transition-all duration-150 hover:bg-violet-hover hover:shadow-md hover:shadow-violet/25 focus:outline-none focus-ring-violet">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Crear cortito</span>
        <span class="sm:hidden">Crear</span>
    </button>
@endsection

@section('content')
<div x-data="snippetDashboard()" x-init="init()">

    {{-- Hero section --}}
    @if($snippets->isEmpty())
        <div class="mb-10 text-center sm:text-left">
            <h1 class="font-display text-3xl font-bold tracking-tight text-ink sm:text-4xl">Tus cortitos</h1>
            <p class="mt-2 text-base text-graphite">Links y notas temporales, listos para compartir.</p>
        </div>
    @else
        <div class="mb-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="font-display text-3xl font-bold tracking-tight text-ink sm:text-4xl">Tus cortitos</h1>
                    <p class="mt-1.5 text-base text-graphite">Links y notas temporales, listos para compartir.</p>
                </div>

                {{-- Metrics --}}
                @php
                    $allSnippets = $snippets->all();
                    $activeCount = count($allSnippets);
                    $totalViews = collect($allSnippets)->sum('views_count');
                    $expiringSoon = collect($allSnippets)->filter(function ($s) {
                        return $s->expires_at && $s->expires_at->isFuture() && $s->expires_at->diffInHours(now()) < 48;
                    })->count();
                @endphp
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <div class="flex items-center gap-2 rounded-lg border border-border-warm bg-warm-white px-3.5 py-2">
                        <span class="font-display font-bold text-ink">{{ $activeCount }}</span>
                        <span class="text-graphite">Activos</span>
                    </div>
                    <div class="flex items-center gap-2 rounded-lg border border-border-warm bg-warm-white px-3.5 py-2">
                        <svg class="h-3.5 w-3.5 text-graphite-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="font-display font-bold text-ink">{{ $totalViews }}</span>
                        <span class="text-graphite">Vistas</span>
                    </div>
                    @if($expiringSoon > 0)
                        <div class="flex items-center gap-2 rounded-lg border border-amber/20 bg-amber-light px-3.5 py-2">
                            <svg class="h-3.5 w-3.5 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-display font-bold text-amber">{{ $expiringSoon }}</span>
                            <span class="whitespace-nowrap text-graphite">Vence pronto</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Search and filters --}}
    @if($snippets->count() > 0)
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            {{-- Search --}}
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-graphite-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    @input="applyFilters()"
                    placeholder="Buscar por titulo o alias..."
                    class="w-full rounded-lg border border-border-warm bg-warm-white py-2.5 pl-10 pr-4 text-sm text-ink placeholder-graphite-light transition-colors focus:border-violet focus:outline-none focus:ring-2 focus:ring-violet-ring">
            </div>

            {{-- Filters --}}
            <div class="flex items-center gap-1.5">
                <template x-for="filter in filters" :key="filter.value">
                    <button
                        type="button"
                        @click="activeFilter = filter.value; applyFilters()"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-150"
                        :class="activeFilter === filter.value
                            ? 'bg-ink text-warm-white shadow-sm'
                            : 'bg-warm-white text-graphite hover:bg-cream-dark border border-border-warm'">
                        <span x-text="filter.label"></span>
                        <template x-if="filter.count > 0">
                            <span class="rounded-md px-1.5 py-0.5 text-[10px] font-bold"
                                  :class="activeFilter === filter.value ? 'bg-white/20 text-white' : 'bg-cream-dark text-graphite'"
                                  x-text="filter.count"></span>
                        </template>
                    </button>
                </template>
            </div>
        </div>
    @endif

    {{-- Anonymous limit banner --}}
    @if(isset($anonymousLimit) && $anonymousCount >= $anonymousLimit)
        <div class="mb-6 rounded-xl border border-amber/20 bg-amber-light p-4 text-sm text-amber">
            <div class="flex items-center gap-2.5">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Alcanzaste el limite de {{ $anonymousLimit }} cortitos gratuitos.
                <a href="#" class="font-semibold underline decoration-amber/40 underline-offset-2 hover:text-amber hover:decoration-amber">Registrate</a> para crear ilimitados.
            </div>
        </div>
    @endif

    {{-- Empty state (no snippets at all) --}}
    @if($snippets->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border-warm bg-warm-white py-24">
            <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-light">
                <svg class="h-8 w-8 text-violet" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h3 class="mb-1.5 font-display text-lg font-bold text-ink">No tenes cortitos todavia</h3>
            <p class="mb-6 text-sm text-graphite">Crea tu primer cortito para empezar a guardar notas y links.</p>
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                class="btn-press inline-flex items-center gap-2 rounded-lg bg-violet px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet/20 transition-all duration-150 hover:bg-violet-hover hover:shadow-md hover:shadow-violet/25">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Crear mi primer cortito
            </button>
        </div>
    @else
        {{-- Snippet grid --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($snippets as $snippet)
                <div data-type="{{ $snippet->content_type }}"
                     data-title="{{ strtolower($snippet->title ?? '') }}"
                     data-alias="{{ strtolower($snippet->alias) }}"
                     x-show="isVisible('{{ $snippet->content_type }}', '{{ strtolower(addslashes($snippet->title ?? '')) }}', '{{ strtolower($snippet->alias) }}')"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    <x-snippet-card :snippet="$snippet" />
                </div>
            @endforeach
        </div>

        {{-- No results message --}}
        <div x-show="hasResults === false" x-cloak class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border-warm bg-warm-white py-16">
            <svg class="mb-3 h-10 w-10 text-border-warm" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm text-graphite">No se encontraron cortitos con ese criterio.</p>
        </div>

        {{-- Pagination --}}
        @if($snippets instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-8" x-show="!search && activeFilter === 'all'">
                {{ $snippets->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Modal --}}
<x-snippet-modal
    :contentTypes="$contentTypes"
    :maxChars="$maxChars"
    :anonymousCount="$anonymousCount ?? 0"
    :anonymousLimit="$anonymousLimit ?? 10"
/>

<script>
function snippetDashboard() {
    return {
        search: '',
        activeFilter: 'all',
        hasResults: true,
        filters: [
            { value: 'all', label: 'Todos', count: 0 },
            { value: 'text', label: 'Texto', count: 0 },
            { value: 'url', label: 'Acortador', count: 0 },
        ],

        init() {
            this.updateFilterCounts();
        },

        updateFilterCounts() {
            const snippets = @json($snippets->all());
            this.filters[0].count = snippets.length;
            this.filters[1].count = snippets.filter(s => s.content_type === 'text').length;
            this.filters[2].count = snippets.filter(s => s.content_type === 'url').length;
        },

        isVisible(type, title, alias) {
            const matchesType = this.activeFilter === 'all' || type === this.activeFilter;
            const searchLower = this.search.toLowerCase().trim();
            const matchesSearch = !searchLower || title.includes(searchLower) || alias.includes(searchLower);
            return matchesType && matchesSearch;
        },

        applyFilters() {
            const cards = document.querySelectorAll('[data-type]');
            let visible = 0;
            cards.forEach(card => {
                const type = card.dataset.type;
                const title = card.dataset.title;
                const alias = card.dataset.alias;
                if (this.isVisible(type, title, alias)) {
                    visible++;
                }
            });
            this.hasResults = visible > 0;
        },
    };
}
</script>
@endsection
