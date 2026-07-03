<aside>
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Mis anotadores</h2>
        @if(isset($anonymousLimit))
            <span class="text-xs text-gray-400">{{ $anonymousCount }}/{{ $anonymousLimit }}</span>
        @endif
    </div>

    @if(isset($anonymousLimit) && $anonymousCount >= $anonymousLimit)
        <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
            Límite alcanzado.
            <a href="#" class="font-medium underline hover:text-amber-900">Registrate</a> para crear ilimitados.
        </div>
    @endif

    @if($snippets->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-200 bg-white p-6 text-center">
            <p class="text-xs text-gray-400">No tenés anotadores todavía.</p>
        </div>
    @else
        <div class="space-y-1.5">
            @foreach($snippets as $snippet)
                <div class="group rounded-lg border border-gray-200 bg-white p-3 transition hover:border-indigo-200 hover:shadow-sm"
                     x-data="{ showDeleteConfirm: false }">
                    <a href="{{ route('snippets.show', $snippet->alias) }}" class="block">
                        <h3 class="truncate text-sm font-medium text-gray-900 group-hover:text-indigo-600">
                            {{ $snippet->title ?: $snippet->alias }}
                        </h3>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-gray-400">
                            <span class="font-mono text-[11px]">{{ $snippet->alias }}</span>
                            <span>{{ $snippet->created_at->diffForHumans() }}</span>
                            @if($snippet->is_edited)
                                <span class="rounded bg-yellow-100 px-1 py-0.5 text-[10px] text-yellow-700">editado</span>
                            @endif
                        </div>
                    </a>

                    @if($snippet->canBeEditedBy(request()))
                        <div class="mt-2 flex items-center gap-2 border-t border-gray-100 pt-2">
                            <a href="{{ route('snippets.edit', $snippet->alias) }}"
                               class="inline-flex items-center gap-1 text-[11px] text-gray-500 hover:text-indigo-600">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar
                            </a>
                            <button
                                type="button"
                                @click.stop="showDeleteConfirm = true"
                                class="inline-flex items-center gap-1 text-[11px] text-gray-500 hover:text-red-600"
                            >
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    @endif

                    <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition>
                        <div class="mx-4 max-w-sm rounded-lg bg-white p-6 shadow-xl" @click.away="showDeleteConfirm = false">
                            <h3 class="mb-2 text-lg font-bold text-gray-900">¿Eliminar anotador?</h3>
                            <p class="mb-4 text-sm text-gray-600">Se eliminará permanentemente "{{ $snippet->alias }}".</p>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showDeleteConfirm = false"
                                        class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                                    Cancelar
                                </button>
                                <form action="{{ route('snippets.destroy', $snippet->alias) }}" method="POST" @click.stop>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($snippets instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-3">
                {{ $snippets->links() }}
            </div>
        @endif
    @endif
</aside>
