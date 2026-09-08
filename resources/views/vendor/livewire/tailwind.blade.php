<div>
    @if ($paginator->hasPages())
        @php($pageName = method_exists($paginator, 'getPageName') ? $paginator->getPageName() : 'page')

        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <!-- Left Info: Result Count -->
            <div class="text-[#718379] font-medium text-sm">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-mono font-bold text-[#2C3E35]">{{ number_format($paginator->firstItem(), 0, ',', '.') }}</span>
                    sampai
                    <span class="font-mono font-bold text-[#2C3E35]">{{ number_format($paginator->lastItem(), 0, ',', '.') }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-mono font-bold text-[#2C3E35]">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
                data
            </div>

            <!-- Right Buttons: Page Links -->
            <div class="flex items-center gap-1.5 text-sm">
                <!-- Previous Button -->
                @if ($paginator->onFirstPage())
                    <span class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-bold border border-slate-200/60 cursor-not-allowed opacity-60 text-sm">
                        &laquo; Prev
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled" class="px-3.5 py-2 rounded-xl bg-white text-[#2C3E35] hover:bg-[#E3EEE8] hover:text-[#3F7A5D] font-bold border border-slate-200/80 transition cursor-pointer shadow-sm text-sm">
                        &laquo; Prev
                    </button>
                @endif

                <!-- Numbered Links -->
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 py-1 text-slate-400 font-bold font-mono text-sm">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3.5 py-2 rounded-xl bg-[#3F7A5D] text-white font-mono font-extrabold shadow-sm border border-[#3F7A5D] text-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" wire:loading.attr="disabled" class="px-3.5 py-2 rounded-xl bg-white text-[#2C3E35] hover:bg-[#E3EEE8] hover:text-[#3F7A5D] font-mono font-bold border border-slate-200/80 transition cursor-pointer shadow-sm text-sm">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <!-- Next Button -->
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled" class="px-3.5 py-2 rounded-xl bg-white text-[#2C3E35] hover:bg-[#E3EEE8] hover:text-[#3F7A5D] font-bold border border-slate-200/80 transition cursor-pointer shadow-sm text-sm">
                        Next &raquo;
                    </button>
                @else
                    <span class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-bold border border-slate-200/60 cursor-not-allowed opacity-60 text-sm">
                        Next &raquo;
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
