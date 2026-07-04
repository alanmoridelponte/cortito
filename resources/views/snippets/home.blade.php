@extends('layouts.app')

@section('title', 'Cortito - Mis cortitos')

@section('header-actions')
    <button
        type="button"
        onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Crear cortito
    </button>
@endsection

@section('content')
<div x-data="snippetDashboard()" x-init="init()">

    {{-- Search and filters --}}
    @if($snippets->count() > 0)
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            {{-- Search --}}
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 14z"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    @input="applyFilters()"
                    placeholder="Buscar por titulo o alias..."
                    class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            </div>

            {{-- Filters --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Filtrar:</span>
                <template x-for="filter in filters" :key="filter.value">
                    <button
                        type="button"
                        @click="activeFilter = filter.value; applyFilters()"
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition-colors"
                        :class="activeFilter === filter.value
                            ? 'bg-indigo-100 text-indigo-700'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        <span x-text="filter.label"></span>
                        <template x-if="filter.count > 0">
                            <span class="ml-1 rounded-full bg-white px-1.5 py-0.5 text-[10px] font-semibold"
                                  :class="activeFilter === filter.value ? 'text-indigo-600' : 'text-gray-500'"
                                  x-text="filter.count"></span>
                        </template>
                    </button>
                </template>
            </div>
        </div>
    @endif

    {{-- Anonymous limit banner --}}
    @if(isset($anonymousLimit) && $anonymousCount >= $anonymousLimit)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Alcanzaste el limite de {{ $anonymousLimit }} cortitos gratuitos.
                <a href="#" class="font-medium underline hover:text-amber-900">Registrate</a> para crear ilimitados.
            </div>
        </div>
    @endif

    {{-- Empty state (no snippets at all) --}}
    @if($snippets->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-20">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100">
                <svg class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="mb-1 text-lg font-semibold text-gray-900">No tenes cortitos todavia</h3>
            <p class="mb-6 text-sm text-gray-500">Crea tu primer cortito para empezar a guardar notas.</p>
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        <div x-show="hasResults === false" x-cloak class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-16">
            <svg class="mb-3 h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm text-gray-500">No se encontraron cortitos con ese criterio.</p>
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
