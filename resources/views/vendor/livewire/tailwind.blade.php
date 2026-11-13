@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        
        {{-- Info: Showing X to Y of Z results --}}
        <div class="text-sm text-slate-500">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-semibold text-slate-900">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-semibold text-slate-900">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-semibold text-slate-900">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </div>

        {{-- Tombol Navigasi --}}
        <div>
            <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-lg overflow-hidden border border-slate-200">
                
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-300 bg-white cursor-not-allowed border-r border-slate-200" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-900 transition-colors border-r border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-inset" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border-r border-slate-200">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-blue-900 border-r border-blue-800 cursor-default">{{ $page }}</span>
                                </span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-900 transition-colors border-r border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-inset" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-900 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-inset" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-300 bg-white cursor-not-allowed" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif