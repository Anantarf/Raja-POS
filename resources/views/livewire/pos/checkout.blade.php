<div x-data="{ activeTab: 'catalog' }" class="min-h-screen lg:h-screen flex flex-col overflow-y-auto lg:overflow-hidden bg-[#F3F6F4] font-sans text-[#232E28]">
    <!-- Topbar Navigation Header -->
    <header class="px-3 sm:px-6 pt-3 sm:pt-4 pb-2 sm:pb-3 flex-shrink-0">
        <div class="bg-white rounded-2xl border border-[#E3EEE8] px-3.5 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between gap-2 sm:gap-3 shadow-xs">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <img src="{{ asset('favicon.svg') }}" alt="Raja POS" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl shrink-0 shadow-2xs">
                <span class="bg-[#3F7A5D] text-white font-extrabold px-3 py-1.5 rounded-xl text-sm sm:text-base tracking-wide uppercase shadow-2xs whitespace-nowrap shrink-0">
                    RAJA AKSESORIS
                </span>
                
                <!-- Store Location Status Badge -->
                <div class="hidden md:flex items-center gap-1.5 text-sm font-extrabold text-[#5F7167] bg-[#F3F6F4] px-3 py-1.5 rounded-xl border border-[#E3EEE8] shrink-0">
                    <svg class="w-4 h-4 text-[#3F7A5D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ $location?->name ?? 'Raja Aksesoris Bango' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 sm:gap-4 text-sm font-semibold shrink-0">
                <div class="hidden sm:flex items-center gap-2.5 bg-[#F3F6F4] px-3.5 py-1.5 rounded-xl border border-[#E3EEE8] shadow-2xs">
                    <div class="w-6.5 h-6.5 rounded-full bg-[#3F7A5D] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="font-extrabold text-[#232E28] text-sm">{{ auth()->user()->name }}</span>
                    <span class="text-[#5F7167] font-semibold text-xs uppercase tracking-wide">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
                </div>

                <a href="/admin" class="bg-[#3F7A5D] hover:bg-[#32634B] text-white h-11 px-3 sm:px-4 py-2.5 rounded-xl font-extrabold text-sm sm:text-base transition flex items-center gap-2 btn-glow active-press hover-lift shadow-xs whitespace-nowrap cursor-pointer" title="Buka Dashboard Admin Management">
                    <svg class="w-4.5 h-4.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="hidden sm:inline">Dashboard Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile View Tab Switcher (md:hidden) -->
    <div class="md:hidden px-2.5 sm:px-6 mb-2 shrink-0 sticky top-0 z-30 bg-[#F3F6F4]/90 backdrop-blur-md pt-1 pb-1">
        <div class="bg-white p-1 rounded-2xl border border-[#E3EEE8] flex items-center shadow-xs">
            <button
                type="button"
                @click="activeTab = 'catalog'"
                :class="activeTab === 'catalog' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]'"
                class="flex-1 py-3 rounded-xl text-base font-extrabold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path>
                </svg>
                <span>Katalog Produk</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'cart'"
                :class="activeTab === 'cart' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]'"
                class="flex-1 py-3 rounded-xl text-base font-extrabold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Keranjang</span>
                <span class="bg-amber-400 text-[#232E28] text-xs font-black px-2 py-0.5 rounded-full font-mono">
                    {{ count($cart) }}
                </span>
            </button>
        </div>
    </div>

    <!-- Main Operational Split View (Desktop: 61.8% Katalog : 38.2% Keranjang, Tablet: 58% : 42%, Mobile: Responsive Tab) -->
    <div class="flex-1 flex flex-col md:flex-row overflow-y-auto md:overflow-hidden px-2.5 sm:px-6 pb-20 md:pb-5 gap-3.5 md:gap-5">

        <!-- LEFT COLUMN: Product Catalog (Golden Ratio Proportion) -->
        <div
            :class="{ 'hidden md:flex': activeTab === 'cart', 'flex': activeTab === 'catalog' }"
            class="w-full md:w-[58%] lg:w-[61.8%] flex-col flex-shrink-0"
        >

            <!-- Streamlined Toolbar -->
            <div class="p-3 sm:p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-3 mb-3">
                <!-- Search & Jenis Dropdown Row -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama produk / scan barcode..."
                            aria-label="Cari nama produk atau scan barcode"
                            class="w-full h-11 pl-10 pr-10 py-2 text-base font-semibold border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/30 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379] transition-all"
                            autofocus
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if($search)
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                aria-label="Bersihkan pencarian"
                                class="absolute right-2 top-2 text-slate-500 hover:text-slate-800 text-sm font-extrabold bg-slate-200 hover:bg-slate-300 rounded-lg w-7 h-7 flex items-center justify-center transition active-press cursor-pointer"
                            >
                                &times;
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                        <select
                            wire:model.live="selectedType"
                            aria-label="Filter Jenis Produk"
                            class="h-11 px-3 py-2 border border-slate-200 rounded-xl bg-white text-sm font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/30 focus:border-[#3F7A5D] shrink-0 cursor-pointer shadow-xs"
                        >
                            <option value="ALL">Semua Jenis</option>
                            <option value="PHYSICAL">Fisik</option>
                            <option value="DIGITAL">Digital</option>
                            <option value="LAYANAN">Layanan</option>
                        </select>

                        <span class="h-11 px-3 flex items-center justify-center rounded-xl bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 font-mono font-extrabold text-xs whitespace-nowrap shrink-0" title="Jumlah Katalog Produk">
                            {{ number_format($totalProductsCount, 0, ',', '.') }} Item
                        </span>

                        <!-- View Mode Toggle (Grid Cards vs List Rows) -->
                        <div class="flex items-center bg-[#F3F6F4] p-1 h-11 rounded-xl border border-slate-200 shrink-0">
                            <button
                                type="button"
                                wire:click="setViewMode('grid')"
                                aria-label="Tampilan Kartu (Grid)"
                                aria-pressed="{{ $viewMode === 'grid' ? 'true' : 'false' }}"
                                class="h-9 px-2.5 rounded-lg text-sm font-bold transition-all cursor-pointer flex items-center justify-center {{ $viewMode === 'grid' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]' }}"
                                title="Tampilan Kartu (Grid)"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </button>
                            <button
                                type="button"
                                wire:click="setViewMode('list')"
                                aria-label="Tampilan Daftar (Baris)"
                                aria-pressed="{{ $viewMode === 'list' ? 'true' : 'false' }}"
                                class="h-9 px-2.5 rounded-lg text-sm font-bold transition-all cursor-pointer flex items-center justify-center {{ $viewMode === 'list' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]' }}"
                                title="Tampilan Daftar (Baris)"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs Row (Pure Full Width Pill Scrollbar-Free Container) -->
                <div role="tablist" aria-label="Kategori Produk" class="flex items-center gap-2 overflow-x-auto py-1 px-0.5 w-full shrink-0 no-scrollbar">
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $selectedCategory === null ? 'true' : 'false' }}"
                        wire:click="$set('selectedCategory', null)"
                        class="h-9 px-4 py-1.5 rounded-xl text-sm font-extrabold transition-all shrink-0 border cursor-pointer {{ $selectedCategory === null ? 'bg-[#3F7A5D] text-white border-[#3F7A5D] shadow-xs' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                    >
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button
                            type="button"
                            role="tab"
                            aria-selected="{{ $selectedCategory === $cat->id ? 'true' : 'false' }}"
                            wire:click="$set('selectedCategory', {{ $cat->id }})"
                            class="h-9 px-4 py-1.5 rounded-xl text-sm font-extrabold transition-all shrink-0 border cursor-pointer {{ $selectedCategory === $cat->id ? 'bg-[#3F7A5D] text-white border-[#3F7A5D] shadow-xs' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                        >
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Display (Grid Cards or Aligned List Rows) -->
            <div class="flex-1 overflow-y-auto pr-1 pb-6">
                @if($viewMode === 'grid')
                    <!-- Product Cards Grid (Spacious 3-Column Layout with Keyboard Accessibility) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-3.5">
                        @forelse($products as $product)
                            @php
                                $isIncomplete = $product->price_status === 'INCOMPLETE' && $product->product_type !== 'LAYANAN';
                                $inv = $product->product_type === 'PHYSICAL' ? $product->inventories->first() : null;
                                $stockQty = $inv?->quantity ?? 0;
                                $stockStatus = $inv?->stock_status ?? 'AVAILABLE';

                                $words = array_values(array_filter(explode(' ', trim($product->name))));
                                $initials = (count($words) >= 2)
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($product->name, 0, 2));
                            @endphp

                            <div
                                tabindex="0"
                                role="button"
                                aria-label="Tambah {{ $product->name }} ke keranjang - Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}"
                                wire:click="addToCart({{ $product->id }})"
                                @keydown.enter="$wire.addToCart({{ $product->id }})"
                                @keydown.space.prevent="$wire.addToCart({{ $product->id }})"
                                class="bg-white border border-[#E3EEE8] hover:border-[#3F7A5D] focus:border-[#3F7A5D] focus:ring-2 focus:ring-[#3F7A5D]/30 focus:outline-none rounded-2xl overflow-hidden flex flex-col justify-between h-[215px] sm:h-[238px] cursor-pointer transition-all duration-200 relative group hover-lift active-press shadow-xs hover:shadow-md {{ $isIncomplete ? 'opacity-65 bg-rose-50/20' : '' }}"
                            >
                                <div>
                                    <!-- Top Image/Banner Container -->
                                    <div class="w-full h-24 sm:h-28 relative overflow-hidden bg-gradient-to-br from-[#E3EEE8]/60 via-[#F3F6F4] to-[#E3EEE8]/30 flex items-center justify-center shrink-0">
                                        <!-- Overlay Badges -->
                                        <div class="absolute top-2 left-2 right-2 flex items-center justify-between z-10 opacity-90 group-hover:opacity-100 transition-opacity">
                                            <span class="text-[0.7rem] font-mono text-[#3F7A5D] bg-white/95 backdrop-blur-sm px-1.5 py-0.5 rounded font-bold border border-[#3F7A5D]/20 shadow-2xs">
                                                {{ $product->code }}
                                            </span>
                                            <span class="text-[0.7rem] uppercase font-extrabold px-1.5 py-0.5 rounded backdrop-blur-sm border {{ $product->product_type === 'PHYSICAL' ? 'bg-[#3F7A5D]/10 text-[#3F7A5D] border-[#3F7A5D]/20' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100/90 text-emerald-800 border-emerald-300/60' : 'bg-[#C2AC7C]/15 text-[#8F794B] border-[#C2AC7C]/30') }}">
                                                {{ $product->product_type === 'PHYSICAL' ? 'FISIK' : ($product->product_type === 'DIGITAL' ? 'DIGITAL' : 'LAYANAN') }}
                                            </span>
                                        </div>

                                        @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                            <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-x-0 bottom-0 h-4 bg-gradient-to-t from-white via-white/40 to-transparent"></div>
                                        @else
                                            <div class="flex items-center justify-center h-full pt-2">
                                                <div class="w-11 h-11 rounded-2xl bg-[#E3EEE8] border border-[#3F7A5D]/25 shadow-2xs flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                                    <span class="text-base font-mono font-black text-[#3F7A5D] tracking-wide">
                                                        {{ $initials }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="absolute inset-x-0 bottom-0 h-4 bg-gradient-to-t from-white to-transparent"></div>
                                        @endif
                                    </div>

                                    <!-- Card Title (Top-Aligned Baseline) -->
                                    <div class="px-3 pt-2 pb-0.5 h-10 sm:h-12 flex items-start">
                                        <h4 class="text-sm sm:text-base font-extrabold text-[#232E28] leading-snug group-hover:text-[#3F7A5D] transition-colors line-clamp-2 overflow-hidden text-ellipsis">
                                            {{ $product->name }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Card Footer Price & Stock (Perfect Sejajar Alignment) -->
                                <div class="px-3 py-2 border-t border-slate-100 flex items-center justify-between gap-1 shrink-0 bg-white min-h-[42px] sm:min-h-[44px]">
                                    <div class="min-w-0 flex-1">
                                        @if($product->product_type === 'LAYANAN')
                                            <span class="inline-flex items-center gap-0.5 text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200/80 px-2 py-0.5 rounded whitespace-nowrap">
                                                Input Nominal
                                            </span>
                                        @elseif($isIncomplete)
                                            <span class="text-xs uppercase tracking-tight font-extrabold text-rose-700 bg-rose-50 border border-rose-200/80 px-1.5 py-0.5 rounded">
                                                INCOMPLETE
                                            </span>
                                        @else
                                            <div class="text-base sm:text-lg font-black text-[#232E28] font-mono tracking-tight whitespace-nowrap">
                                                Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        @if($product->product_type === 'PHYSICAL')
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[0.72rem] whitespace-nowrap {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20') }}">
                                                Stok: {{ $stockQty }}
                                            </span>
                                        @elseif($product->product_type === 'DIGITAL')
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[0.72rem] bg-emerald-50 text-emerald-700 border border-emerald-200/60 whitespace-nowrap">
                                                Digital
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[0.72rem] bg-amber-50 text-amber-700 border border-amber-200/60 whitespace-nowrap">
                                                Layanan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-24 text-center text-slate-400 text-sm">
                                <div class="font-bold text-[#232E28] text-lg mb-1">Tidak ada produk ditemukan.</div>
                                <div class="text-base text-[#5F7167]">Gunakan kata kunci pencarian lain atau pilih kategori lain.</div>
                            </div>
                        @endforelse

                        @if($totalProductsCount > count($products))
                            <div class="col-span-full py-4 text-center">
                                <button
                                    type="button"
                                    wire:click="loadMore"
                                    wire:loading.attr="disabled"
                                    class="h-11 px-5 py-2 bg-white hover:bg-[#E3EEE8]/70 border border-[#3F7A5D]/30 text-[#3F7A5D] font-extrabold text-sm rounded-2xl shadow-2xs hover-lift active-press transition cursor-pointer inline-flex items-center gap-2"
                                >
                                    <svg wire:loading.remove class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <svg wire:loading class="w-4 h-4 text-[#3F7A5D] animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Tampilkan Produk Lainnya</span>
                                    <span class="bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 px-2 py-0.5 rounded-lg text-xs font-mono font-black">
                                        +{{ number_format($totalProductsCount - count($products), 0, ',', '.') }}
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Product List View (Aligned Table Rows with Scan-First Font Hierarchy) -->
                    <div class="bg-white rounded-2xl border border-[#E3EEE8] overflow-hidden shadow-xs">
                        <div class="divide-y divide-slate-100">
                            @forelse($products as $product)
                                @php
                                    $isIncomplete = $product->price_status === 'INCOMPLETE' && $product->product_type !== 'LAYANAN';
                                    $inv = $product->product_type === 'PHYSICAL' ? $product->inventories->first() : null;
                                    $stockQty = $inv?->quantity ?? 0;
                                    $stockStatus = $inv?->stock_status ?? 'AVAILABLE';

                                    $words = array_values(array_filter(explode(' ', trim($product->name))));
                                    $initials = (count($words) >= 2)
                                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                        : strtoupper(substr($product->name, 0, 2));
                                @endphp

                                <div
                                    tabindex="0"
                                    role="button"
                                    aria-label="Tambah {{ $product->name }} ke keranjang - Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}"
                                    wire:click="addToCart({{ $product->id }})"
                                    @keydown.enter="$wire.addToCart({{ $product->id }})"
                                    @keydown.space.prevent="$wire.addToCart({{ $product->id }})"
                                    class="p-3.5 hover:bg-[#F3F6F4] focus:bg-[#E3EEE8]/50 focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/30 cursor-pointer transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 group border-b border-slate-100/80 {{ $isIncomplete ? 'opacity-60 bg-rose-50/20' : '' }}"
                                >
                                    <!-- Left Column: Avatar + Product Name & Badges -->
                                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                        <!-- Code & Initials Avatar Badge -->
                                        <div class="w-11 h-11 rounded-2xl bg-[#E3EEE8] border border-[#3F7A5D]/25 flex items-center justify-center font-mono font-black text-sm text-[#3F7A5D] shrink-0 group-hover:bg-[#3F7A5D] group-hover:text-white transition-all shadow-2xs">
                                            {{ $initials }}
                                        </div>

                                        <!-- Product Title First, Badges Second -->
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-base font-extrabold text-[#232E28] group-hover:text-[#3F7A5D] transition-colors leading-snug truncate">
                                                {{ $product->name }}
                                            </h4>
                                            <div class="flex items-center gap-2 flex-wrap mt-0.5">
                                                <span class="text-[0.72rem] font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/70 whitespace-nowrap">
                                                    {{ $product->code }}
                                                </span>
                                                <span class="text-[0.72rem] uppercase font-extrabold px-2 py-0.5 rounded-md border whitespace-nowrap {{ $product->product_type === 'PHYSICAL' ? 'bg-[#3F7A5D]/10 text-[#3F7A5D] border-[#3F7A5D]/20' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100/90 text-emerald-800 border-emerald-300/60' : 'bg-[#C2AC7C]/15 text-[#8F794B] border-[#C2AC7C]/30') }}">
                                                    {{ $product->product_type === 'PHYSICAL' ? 'FISIK' : ($product->product_type === 'DIGITAL' ? 'DIGITAL' : 'LAYANAN') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Price & Stock Badge Alignment -->
                                    <div class="flex items-center justify-between sm:justify-end gap-3.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100/80 text-right pl-14 sm:pl-0">
                                        <div class="sm:min-w-[130px] text-left sm:text-right">
                                            @if($product->product_type === 'LAYANAN')
                                                <span class="inline-flex items-center gap-0.5 text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200/80 px-2 py-0.5 rounded whitespace-nowrap">
                                                    Input Nominal
                                                </span>
                                            @elseif($isIncomplete)
                                                <span class="text-xs uppercase font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded whitespace-nowrap">
                                                    INCOMPLETE
                                                </span>
                                            @else
                                                <div class="text-base sm:text-lg font-black text-[#232E28] font-mono whitespace-nowrap">
                                                    Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="sm:w-24 text-right">
                                            @if($product->product_type === 'PHYSICAL')
                                                <span class="px-2.5 py-1 rounded-full font-extrabold text-xs inline-block whitespace-nowrap {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20') }}">
                                                    Stok: {{ $stockQty }}
                                                </span>
                                            @elseif($product->product_type === 'DIGITAL')
                                                <span class="px-2.5 py-1 rounded-full font-extrabold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-block whitespace-nowrap">
                                                    Digital
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full font-extrabold text-xs bg-amber-50 text-amber-700 border border-amber-200/60 inline-block whitespace-nowrap">
                                                    Layanan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center text-slate-400 text-sm">
                                    <div class="font-bold text-[#232E28] text-lg mb-1">Tidak ada produk ditemukan.</div>
                                    <div class="text-base text-[#5F7167]">Gunakan kata kunci pencarian lain atau pilih kategori lain.</div>
                                </div>
                            @endforelse
                        </div>

                        @if($totalProductsCount > count($products))
                            <div class="p-4 text-center border-t border-slate-100 bg-[#F3F6F4]/50">
                                <button
                                    type="button"
                                    wire:click="loadMore"
                                    wire:loading.attr="disabled"
                                    class="h-11 px-5 py-2 bg-white hover:bg-[#E3EEE8]/70 border border-[#3F7A5D]/30 text-[#3F7A5D] font-extrabold text-sm rounded-2xl shadow-2xs hover-lift active-press transition cursor-pointer inline-flex items-center gap-2"
                                >
                                    <svg wire:loading.remove class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <svg wire:loading class="w-4 h-4 text-[#3F7A5D] animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Tampilkan Produk Lainnya</span>
                                    <span class="bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 px-2 py-0.5 rounded-lg text-xs font-mono font-black">
                                        +{{ number_format($totalProductsCount - count($products), 0, ',', '.') }}
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: Golden Ratio Cart Sidebar (38.2% Desktop / 42% Tablet) -->
        <div
            id="cart-section"
            :class="{ 'hidden md:flex': activeTab === 'catalog', 'flex': activeTab === 'cart' }"
            class="w-full md:w-[42%] lg:w-[38.2%] bg-white rounded-2xl border border-[#E3EEE8] flex-col flex-shrink-0 overflow-hidden h-full shadow-sm"
        >

            <!-- 1. Cart Header -->
            <div class="px-5 py-3.5 bg-white border-b border-slate-200/80 flex items-center justify-between shrink-0">
                <div class="font-extrabold text-base sm:text-lg text-[#232E28] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Keranjang</span>
                    <span class="bg-[#E3EEE8] text-[#3F7A5D] px-2.5 py-0.5 rounded-full text-sm font-extrabold font-mono">
                        {{ count($cart) }}
                    </span>
                </div>
                @if(count($cart) > 0)
                    <button
                        type="button"
                        wire:click="clearCart"
                        class="text-xs font-extrabold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-xl transition cursor-pointer flex items-center gap-1.5 active-press"
                        title="Kosongkan Keranjang"
                    >
                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Kosongkan</span>
                    </button>
                @endif
            </div>

            <!-- 2. MAXIMIZED FLEX-1 CART ITEMS SCROLLABLE LIST -->
            <div class="flex-1 overflow-y-auto px-5 py-3 divide-y divide-slate-100 min-h-0">
                @forelse($cart as $id => $item)
                    <div class="py-3 flex items-center justify-between gap-3 group">
                        <!-- Product Name & Unit Price -->
                        <div class="flex-1 min-w-0">
                            <div class="text-base font-bold text-[#232E28] truncate leading-snug group-hover:text-[#3F7A5D] transition-colors" title="{{ $item['name'] }}">
                                {{ $item['name'] }}
                            </div>
                            <div class="text-[0.78rem] text-[#718379] font-mono font-semibold mt-0.5">
                                @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Fixed-Width Perfectly Aligned Quantity +/- Stepper -->
                        <div class="shrink-0 w-[100px] sm:w-[110px] flex items-center justify-between bg-[#F3F6F4] p-1 rounded-2xl border border-slate-200/80 shadow-2xs">
                            <button
                                type="button"
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-7 sm:w-8 h-7 sm:h-8 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 font-extrabold text-sm rounded-xl flex items-center justify-center text-[#232E28] transition active:scale-95 border border-slate-200 shadow-2xs cursor-pointer"
                                title="Kurangi 1"
                            >-</button>

                            <span class="flex-1 text-center font-black text-sm font-mono text-[#232E28] tracking-tight">
                                {{ $item['quantity'] }}
                            </span>

                            <button
                                type="button"
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-7 sm:w-8 h-7 sm:h-8 bg-white hover:bg-[#E3EEE8] hover:text-[#3F7A5D] hover:border-[#3F7A5D]/30 font-extrabold text-sm rounded-xl flex items-center justify-center text-[#232E28] transition active:scale-95 border border-slate-200 shadow-2xs cursor-pointer"
                                title="Tambah 1"
                            >+</button>
                        </div>

                        <!-- Fixed-Width Subtotal & Delete Action with Trash Icon Button -->
                        <div class="shrink-0 w-[105px] text-right flex flex-col items-end justify-between min-h-[42px] py-0.5">
                            <div class="text-sm font-extrabold text-[#232E28] font-mono tracking-tight">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button
                                type="button"
                                wire:click="removeFromCart({{ $id }})"
                                class="inline-flex items-center gap-1 text-[0.68rem] uppercase font-extrabold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-1.5 py-0.5 rounded-md transition cursor-pointer mt-1 active-press"
                                title="Hapus dari keranjang"
                            >
                                <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>HAPUS</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="h-full min-h-[200px] flex flex-col items-center justify-center text-center text-slate-400 space-y-2 p-6">
                        <div class="w-12 h-12 rounded-2xl bg-[#F3F6F4] border border-slate-200/80 text-slate-400 flex items-center justify-center mb-1">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div class="font-bold text-[#232E28] text-lg">Keranjang Kosong</div>
                        <div class="text-sm text-[#5F7167] max-w-xs">Pilih barang di katalog untuk menambahkan ke transaksi.</div>
                    </div>
                @endforelse
            </div>

            <!-- 3. PROPORTIONAL PAYMENT FOOTER -->
            <div class="p-4 sm:p-5 border-t border-slate-200/80 bg-[#F3F6F4]/60 space-y-3 shrink-0">

                <!-- Grand Total Billing Card -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-baseline justify-between">
                    <div>
                        <div class="text-base font-extrabold uppercase text-[#5F7167] tracking-wider">Total Belanja</div>
                        <div class="text-sm text-slate-500 font-semibold mt-0.5">Subtotal: Rp {{ number_format($this->subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-[#232E28] font-mono tracking-tight">
                        Rp {{ number_format($this->grand_total, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Payment Method Inputs -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm font-extrabold text-[#232E28]">
                        <span>Metode Pembayaran</span>
                        <button type="button" wire:click="addPaymentRow" class="text-[#3F7A5D] hover:underline text-sm font-bold cursor-pointer">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 space-y-2.5 text-base shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-full sm:w-1/2 h-12 px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white text-base font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] shadow-2xs cursor-pointer">
                                    @foreach($paymentMethods as $pm)
                                        @php
                                            $pmLabel = match($pm->type) {
                                                'CASH' => 'Tunai / Cash',
                                                'QRIS' => 'QRIS',
                                                'TRANSFER' => 'Transfer Bank',
                                                'E_WALLET' => 'E-Wallet',
                                                default => $pm->name,
                                            };
                                        @endphp
                                        <option value="{{ $pm->id }}">{{ $pmLabel }}</option>
                                    @endforeach
                                </select>

                                <div class="flex items-center gap-1.5 flex-1 w-full sm:w-auto">
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-3 text-sm font-bold text-[#718379]">Rp</span>
                                        <input
                                            type="text"
                                            maxlength="13"
                                            x-data
                                            x-on:input="
                                                let val = $el.value.replace(/\D/g, '');
                                                if (val && parseInt(val) > 1000000000) val = '1000000000';
                                                $el.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                                                $wire.set('payments.{{ $index }}.amount', val ? parseInt(val) : 0);
                                            "
                                            value="{{ $pay['amount'] ? number_format((float) $pay['amount'], 0, ',', '.') : '' }}"
                                            placeholder="0"
                                            class="w-full h-11 pl-9 pr-3 py-2.5 border border-slate-200 rounded-xl font-mono font-extrabold text-right text-base sm:text-lg text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                        />
                                    </div>

                                    @if(count($payments) > 1)
                                        <button type="button" wire:click="removePaymentRow({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold w-11 h-11 min-h-[44px] bg-rose-50 rounded-xl text-lg flex items-center justify-center cursor-pointer shrink-0 border border-rose-200/60" title="Hapus metode pembayaran">
                                            &times;
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                @php
                                    $targetAccountType = $selectedPm->type === 'TRANSFER' ? 'BANK' : 'E_WALLET';
                                    $filteredAccounts = $balanceAccounts->where('account_type', $targetAccountType);
                                    $placeholderText = $selectedPm->type === 'TRANSFER' ? 'Pilih Rekening Bank Tujuan' : 'Pilih Akun E-Wallet Tujuan';
                                @endphp
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full h-11 px-3.5 py-2 border border-slate-200 rounded-xl bg-indigo-50/50 text-sm font-extrabold text-indigo-900 focus:ring-2 focus:ring-indigo-500/20 shadow-2xs cursor-pointer">
                                        <option value="">{{ $placeholderText }}</option>
                                        @foreach($filteredAccounts as $ba)
                                            <option value="{{ $ba->id }}">{{ $ba->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Cash Change Display (Overflow Protected) -->
                <div class="bg-white px-4 py-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between text-sm overflow-hidden">
                    <span class="font-extrabold text-[#232E28] shrink-0 text-sm sm:text-base">Kembalian</span>
                    <span class="truncate text-right font-mono font-black {{ $this->change_amount > 0 ? 'text-emerald-700 text-2xl sm:text-3xl' : 'text-[#232E28] text-sm font-bold' }}">
                        Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Primary Action Checkout Button (High Contrast Disabled & Active) -->
                <button
                    type="button"
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full h-14 py-3.5 rounded-2xl font-black text-base sm:text-lg uppercase tracking-wider transition-all flex items-center justify-center gap-2.5 {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-[#3F7A5D] hover:bg-[#32634B] text-white shadow-md cursor-pointer active-press hover-lift btn-glow' : 'bg-slate-100 text-slate-400 border border-slate-200/80 cursor-not-allowed' }}"
                >
                    <svg class="w-5 h-5 text-white shrink-0 {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'opacity-100' : 'opacity-40' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>SELESAIKAN TRANSAKSI & CETAK STRUK</span>
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-7 max-w-sm w-full shadow-2xl text-center space-y-5 border border-slate-100">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#232E28]">Transaksi Berhasil!</h3>
                    <p class="text-sm text-[#3F7A5D] font-mono mt-1 font-bold">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-[#F3F6F4] p-5 rounded-2xl border border-slate-200">
                    <div class="text-base text-[#5F7167] font-medium">Kembali</div>
                    <div class="text-3xl sm:text-4xl font-black text-emerald-700 font-mono mt-1">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-1">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full h-14 py-3.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-sm transition uppercase tracking-wider text-center flex items-center justify-center"
                    >
                        CETAK STRUK THERMAL
                    </a>
                    <button
                        type="button"
                        wire:click="closeSuccessModal"
                        class="w-full h-14 py-3.5 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-sm transition cursor-pointer"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- PPOB Open-Nominal Bill Modal -->
    @if($showPpobModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                    <!-- PPOB Open-Nominal Bill Modal -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs uppercase font-extrabold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">Layanan</span>
                            <h3 class="text-lg font-extrabold text-[#232E28] mt-1">{{ $selectedPpobProductName }}</h3>
                        </div>
                        <button type="button" wire:click="$set('showPpobModal', false)" aria-label="Tutup modal" class="text-slate-400 hover:text-slate-600 font-bold text-xl px-2 cursor-pointer">&times;</button>
                    </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-[#232E28] mb-1">{{ $ppobModalLabel }} Pelanggan *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3.5 text-sm font-bold text-[#718379]">Rp</span>
                            <input
                                type="text"
                                x-data
                                x-on:input="
                                    let val = $el.value.replace(/\D/g, '');
                                    if (val && parseInt(val) > 1000000000) val = '1000000000';
                                    $el.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                                    $wire.set('ppobBillAmount', val ? parseInt(val) : 0);
                                "
                                value="{{ $ppobBillAmount ? number_format($ppobBillAmount, 0, ',', '.') : '' }}"
                                placeholder="0"
                                class="w-full h-12 pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl font-mono font-extrabold text-lg text-right text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                autofocus
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-[#232E28] mb-1">Biaya Admin Toko (Jasa) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-sm font-bold text-[#718379]">Rp</span>
                                <input
                                    type="text"
                                    x-data
                                    x-on:input="
                                        let val = $el.value.replace(/\D/g, '');
                                        $el.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                                        $wire.set('ppobStoreAdminFee', val ? parseInt(val) : 0);
                                    "
                                    value="{{ $ppobStoreAdminFee ? number_format($ppobStoreAdminFee, 0, ',', '.') : '' }}"
                                    placeholder="3.000"
                                    class="w-full h-11 pl-9 pr-2.5 py-2 border border-slate-200 rounded-xl font-mono font-bold text-right text-sm text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#232E28] mb-1">Biaya Admin Vendor *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-sm font-bold text-[#718379]">Rp</span>
                                <input
                                    type="text"
                                    x-data
                                    x-on:input="
                                        let val = $el.value.replace(/\D/g, '');
                                        $el.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                                        $wire.set('ppobVendorAdminFee', val ? parseInt(val) : 0);
                                    "
                                    value="{{ $ppobVendorAdminFee ? number_format($ppobVendorAdminFee, 0, ',', '.') : '' }}"
                                    placeholder="1.500"
                                    class="w-full h-11 pl-9 pr-2.5 py-2 border border-slate-200 rounded-xl font-mono font-bold text-right text-base text-[#5F7167] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Live Calculations Card -->
                    @php
                        $calcTotalPay = $ppobBillAmount + $ppobStoreAdminFee;
                        $calcTotalCost = $ppobBillAmount + $ppobVendorAdminFee;
                        $calcMargin = $calcTotalPay - $calcTotalCost;
                    @endphp
                    <div class="bg-[#F3F6F4] p-4 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-base font-bold text-[#232E28]">
                            <span>Total Ditagihkan Ke Pelanggan:</span>
                            <span class="font-mono text-base text-[#3F7A5D] font-black">Rp {{ number_format($calcTotalPay, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-base text-[#5F7167]">
                            <span>Estimasi Modal Toko:</span>
                            <span class="font-mono">Rp {{ number_format($calcTotalCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm font-extrabold text-emerald-700 pt-1 border-t border-slate-200/60">
                            <span>Estimasi Keuntungan Toko (Margin):</span>
                            <span class="font-mono">Rp {{ number_format($calcMargin, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button
                        type="button"
                        wire:click="confirmAddPpobToCart"
                        class="flex-1 h-11 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-sm transition uppercase tracking-wider shadow-sm cursor-pointer"
                    >
                        Masukkan Ke Keranjang
                    </button>
                    <button
                        type="button"
                        wire:click="$set('showPpobModal', false)"
                        class="h-11 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-sm transition cursor-pointer"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MOBILE STICKY FLOATING CART BAR (md:hidden) -->
    <div class="md:hidden fixed bottom-3 left-3 right-3 z-40 bg-[#232E28]/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-xl border border-emerald-500/30 flex items-center justify-between gap-2.5 transition-all duration-300">
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <div class="w-10 h-10 rounded-xl bg-[#3F7A5D] text-white flex items-center justify-center font-mono font-extrabold text-sm shrink-0 shadow-inner">
                {{ count($cart) }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs text-emerald-300 uppercase font-bold tracking-wider truncate">
                    {{ count($cart) > 0 ? 'Total Belanja' : 'Keranjang Belanja' }}
                </div>
                <div class="text-sm sm:text-base font-extrabold font-mono text-white truncate">
                    @if(count($cart) > 0)
                        Rp {{ number_format((float) $this->grand_total, 0, ',', '.') }}
                    @else
                        Belum Ada Produk
                    @endif
                </div>
            </div>
        </div>

        <button
            type="button"
            @click="activeTab = (activeTab === 'cart' ? 'catalog' : 'cart'); document.getElementById('cart-section')?.scrollIntoView({ behavior: 'smooth' })"
            class="bg-[#3F7A5D] hover:bg-[#32634B] text-white h-10 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-extrabold flex items-center gap-1 shadow-md active:scale-95 transition shrink-0 cursor-pointer whitespace-nowrap"
        >
            <span x-text="activeTab === 'cart' ? '\u2190 Katalog' : 'Lihat & Bayar'"></span>
            <svg x-show="activeTab !== 'cart'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>
