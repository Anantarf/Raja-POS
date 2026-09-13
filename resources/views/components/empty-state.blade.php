@props([
    'title' => 'Data Tidak Ditemukan',
    'description' => 'Belum ada data yang tersedia atau coba ubah kata kunci pencarian dan filter Anda.',
    'icon' => 'search', // search, box, file, receipt, wallet
    'actionText' => null,
    'actionClick' => null,
    'actionHref' => null,
])

<div class="py-12 px-4 text-center flex flex-col items-center justify-center min-h-[220px]">
    <!-- Icon Surface Ring -->
    <div class="w-14 h-14 rounded-2xl bg-slate-100/90 border border-slate-200/80 flex items-center justify-center text-slate-400 mb-3.5 shadow-2xs">
        @if($icon === 'box')
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        @elseif($icon === 'file')
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @elseif($icon === 'receipt')
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l2 2 4-4m-6 8a2 2 0 01-2-2V5a2 2 0 012-2h8a2 2 0 012 2v14a2 2 0 01-2 2H9z" />
            </svg>
        @elseif($icon === 'wallet')
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        @else
            <!-- Search Fallback -->
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        @endif
    </div>

    <!-- Text Content -->
    <h3 class="text-sm font-extrabold text-slate-800 tracking-tight mb-1">{{ $title }}</h3>
    <p class="text-xs text-slate-500 font-medium max-w-sm leading-relaxed mb-4">{{ $description }}</p>

    <!-- Optional Action Button or Slot -->
    @if($actionText)
        @if($actionHref)
            <a href="{{ $actionHref }}" class="h-9 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl text-xs transition shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer active-press">
                <span>{{ $actionText }}</span>
            </a>
        @elseif($actionClick)
            <button type="button" wire:click="{{ $actionClick }}" class="h-9 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200/80 font-bold rounded-xl text-xs transition shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer active-press">
                <span>{{ $actionText }}</span>
            </button>
        @endif
    @elseif(isset($slot) && $slot->isNotEmpty())
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
