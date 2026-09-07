@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
        <!-- Results Summary Text -->
        <div class="text-[#718379] font-medium text-sm">
            Menampilkan <span class="font-bold text-[#232E28] font-mono">{{ $paginator->firstItem() ?? 0 }}</span> &ndash; <span class="font-bold text-[#232E28] font-mono">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#3F7A5D] font-mono">{{ number_format($paginator->total(), 0, ',', '.') }}</span> produk
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-1.5 font-bold font-mono">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed text-sm">
                    &lsaquo;
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-9 h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 shadow-xs text-sm">
                    &lsaquo;
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-9 flex items-center justify-center text-slate-400 font-bold text-sm">&hellip;</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 rounded-xl bg-[#3F7A5D] text-white flex items-center justify-center shadow-xs font-black text-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="w-9 h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 text-sm">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-9 h-9 rounded-xl border border-slate-200/80 bg-white text-[#232E28] hover:bg-[#F3F6F4] hover:border-[#3F7A5D]/40 flex items-center justify-center transition cursor-pointer active:scale-95 shadow-xs text-sm">
                    &rsaquo;
                </button>
            @else
                <span class="w-9 h-9 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed text-sm">
                    &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif
