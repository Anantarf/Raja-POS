@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm w-full">
        <!-- Results Summary Text -->
        <div class="text-[#718379] font-medium text-xs sm:text-sm text-center sm:text-left">
            Menampilkan <span class="font-bold text-[#232E28] font-mono">{{ $paginator->firstItem() ?? 0 }}</span> &ndash; <span class="font-bold text-[#232E28] font-mono">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#3F7A5D] font-mono">{{ number_format($paginator->total(), 0, ',', '.') }}</span> item
        </div>

        <!-- Pagination Controls -->
        <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-1.5 font-bold font-mono max-w-full overflow-x-auto py-0.5 no-scrollbar">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed text-xs sm:text-sm shrink-0">
                    &lsaquo;
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 shadow-xs text-xs sm:text-sm shrink-0">
                    &lsaquo;
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-7 h-8 sm:w-8 sm:h-9 flex items-center justify-center text-slate-400 font-bold text-xs sm:text-sm shrink-0">&hellip;</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#3F7A5D] text-white flex items-center justify-center shadow-xs font-black text-xs sm:text-sm shrink-0">
                                {{ $page }}
                            </span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 text-xs sm:text-sm shrink-0">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 shadow-xs text-xs sm:text-sm shrink-0">
                    &rsaquo;
                </button>
            @else
                <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed text-xs sm:text-sm shrink-0">
                    &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif
