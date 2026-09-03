<div class="min-h-screen lg:h-screen flex flex-col overflow-y-auto lg:overflow-hidden bg-[#F3F6F4] font-sans text-[#232E28]">
    <!-- Topbar Navigation Header -->
    <header class="px-3 sm:px-6 pt-3 sm:pt-4 pb-2 sm:pb-3 flex-shrink-0">
        <div class="bg-white rounded-2xl border border-[#E3EEE8] px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('favicon.svg') }}" alt="Raja POS" class="w-8 h-8 rounded-xl shrink-0">
                <span class="bg-[#3F7A5D] text-white font-extrabold px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm tracking-wider uppercase shadow-sm">RAJA AKSESORIS</span>
                <span class="text-xs sm:text-sm font-extrabold text-[#232E28] hidden md:inline border-l border-[#E3EEE8] pl-4">
                    {{ $location?->name ?? 'Raja Aksesoris Bango' }}
                </span>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 text-xs sm:text-sm font-semibold">
                <div class="hidden sm:flex items-center gap-2.5 bg-[#F3F6F4] px-3.5 py-1.5 rounded-xl border border-[#E3EEE8]">
                    <div class="w-6 h-6 rounded-full bg-[#3F7A5D] text-white font-bold flex items-center justify-center text-[10px]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="font-extrabold text-[#232E28] text-xs sm:text-sm">{{ auth()->user()->name }}</span>
                    <span class="text-[#718379] font-normal text-[11px]">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
                </div>

                <a href="/admin" class="bg-[#3F7A5D] hover:bg-[#32634B] text-white px-3 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 active:scale-95 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Panel Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Operational Split View (Desktop: 58% Katalog : 42% Keranjang, Tablet/HP: Responsive Stack) -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden px-3 sm:px-6 pb-5 gap-4 lg:gap-5">

        <!-- LEFT COLUMN: Product Catalog (61.8% Golden Ratio Proportion) -->
        <div class="w-full lg:w-[61.8%] flex flex-col flex-shrink-0">

            <!-- Streamlined Toolbar -->
            <div class="p-3.5 sm:p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-3 mb-3">
                <!-- Search & Jenis Dropdown Row -->
                <div class="flex items-center gap-2.5">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama produk / scan barcode scanner..."
                            class="w-full pl-10 pr-9 py-2.5 text-xs font-semibold border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379] transition-all"
                            autofocus
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold bg-slate-200 rounded-full w-5 h-5 flex items-center justify-center">
                                &times;
                            </button>
                        @endif
                    </div>

                    <span class="px-3 py-2 rounded-xl bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 font-mono font-extrabold text-xs whitespace-nowrap hidden sm:inline-block" title="Jumlah Katalog Produk Siap Di-Scroll">
                        {{ number_format(count($products), 0, ',', '.') }} Produk
                    </span>

                    <select
                        wire:model.live="selectedType"
                        class="px-3 py-2.5 border border-slate-200 rounded-xl bg-white text-xs font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] shrink-0 cursor-pointer shadow-xs"
                    >
                        <option value="ALL">Semua Jenis</option>
                        <option value="PHYSICAL">Fisik</option>
                        <option value="DIGITAL">Digital</option>
                        <option value="LAYANAN">Layanan</option>
                    </select>

                    <!-- View Mode Toggle (Grid Cards vs List Rows) -->
                    <div class="flex items-center bg-[#F3F6F4] p-1 rounded-xl border border-slate-200 shrink-0">
                        <button
                            wire:click="setViewMode('grid')"
                            class="p-2 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center justify-center {{ $viewMode === 'grid' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]' }}"
                            title="Tampilan Kartu (Grid)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button
                            wire:click="setViewMode('list')"
                            class="p-2 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center justify-center {{ $viewMode === 'list' ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:text-[#232E28]' }}"
                            title="Tampilan Daftar (Baris)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Category Tabs (Full Width Pill Toolbar) -->
                <div class="flex items-center gap-2 overflow-x-auto py-1 px-0.5 w-full shrink-0 text-xs font-bold no-scrollbar">
                    <button
                        wire:click="$set('selectedCategory', null)"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 border cursor-pointer {{ $selectedCategory === null ? 'bg-[#3F7A5D] text-white border-[#3F7A5D] shadow-xs' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                    >
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button
                            wire:click="$set('selectedCategory', {{ $cat->id }})"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 border cursor-pointer {{ $selectedCategory === $cat->id ? 'bg-[#3F7A5D] text-white border-[#3F7A5D] shadow-xs' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                        >
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Display (Grid Cards or Aligned List Rows) -->
            <div class="flex-1 overflow-y-auto pr-1">
                @if($viewMode === 'grid')
                    <!-- Product Cards Grid (100% Equal Height Alignment) -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
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
                                wire:click="addToCart({{ $product->id }})"
                                class="bg-white border border-[#E3EEE8] hover:border-[#3F7A5D] rounded-3xl overflow-hidden flex flex-col justify-between h-[235px] cursor-pointer transition-all duration-200 relative group hover:shadow-sm {{ $isIncomplete ? 'opacity-65 bg-rose-50/20' : '' }}"
                            >
                                <div>
                                    <!-- Top Image/Banner Container -->
                                    <div class="w-full h-28 relative overflow-hidden bg-gradient-to-br from-[#E3EEE8]/70 via-[#F3F6F4] to-[#E3EEE8]/40 flex items-center justify-center shrink-0">
                                        <!-- Overlay Badges -->
                                        <div class="absolute top-2 left-2 right-2 flex items-center justify-between z-10 opacity-90 group-hover:opacity-100 transition-opacity">
                                            <span class="text-[9px] font-mono text-[#3F7A5D] bg-white/80 backdrop-blur-sm px-1.5 py-0.5 rounded font-bold border border-[#3F7A5D]/15">
                                                {{ $product->code }}
                                            </span>
                                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded backdrop-blur-sm border {{ $product->product_type === 'PHYSICAL' ? 'bg-[#3F7A5D]/10 text-[#3F7A5D] border-[#3F7A5D]/20' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100/90 text-emerald-800 border-emerald-300/60' : 'bg-[#C2AC7C]/15 text-[#8F794B] border-[#C2AC7C]/30') }}">
                                                {{ $product->product_type === 'PHYSICAL' ? 'FISIK' : ($product->product_type === 'DIGITAL' ? 'DIGITAL' : 'LAYANAN') }}
                                            </span>
                                        </div>

                                        @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                            <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-x-0 bottom-0 h-6 bg-gradient-to-t from-white via-white/40 to-transparent"></div>
                                        @else
                                            <div class="flex items-center justify-center h-full pt-2">
                                                <span class="text-2xl font-mono font-extrabold text-[#3F7A5D]/80 tracking-wider">
                                                    {{ $initials }}
                                                </span>
                                            </div>
                                            <div class="absolute inset-x-0 bottom-0 h-6 bg-gradient-to-t from-white to-transparent"></div>
                                        @endif
                                    </div>

                                    <!-- Card Title (Top-Aligned Baseline) -->
                                    <div class="px-3 pt-2 pb-1 h-10 flex items-start">
                                        <h4 class="text-xs font-bold text-[#232E28] leading-tight group-hover:text-[#3F7A5D] transition-colors line-clamp-2 overflow-hidden text-ellipsis">
                                            {{ $product->name }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Card Footer Price & Stock (Perfect Sejajar Alignment) -->
                                <div class="px-3 pb-2.5 pt-2 border-t border-slate-100 flex items-center justify-between gap-1.5 shrink-0 bg-white min-h-[42px]">
                                    <div class="min-w-0 flex-1">
                                        @if($product->product_type === 'LAYANAN')
                                            <span class="inline-flex items-center gap-0.5 text-[9.5px] font-bold text-teal-700 bg-teal-50 border border-teal-200/80 px-1.5 py-0.5 rounded whitespace-nowrap">
                                                Input Nominal
                                            </span>
                                        @elseif($isIncomplete)
                                            <span class="text-[9px] uppercase tracking-tight font-extrabold text-rose-700 bg-rose-50 border border-rose-200/80 px-1.5 py-0.5 rounded">
                                                HARGA INCOMPLETE
                                            </span>
                                        @else
                                            <div class="text-sm font-extrabold text-[#232E28] font-mono tracking-tight whitespace-nowrap">
                                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="shrink-0">
                                        @if($product->product_type === 'PHYSICAL')
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] whitespace-nowrap {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700' : 'bg-[#E3EEE8] text-[#3F7A5D]') }}">
                                                Stok: {{ $stockQty }}
                                            </span>
                                        @elseif($product->product_type === 'DIGITAL')
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[9.5px] bg-emerald-50 text-emerald-700 border border-emerald-200/60 whitespace-nowrap">
                                                Digital
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full font-bold text-[9.5px] bg-amber-50 text-amber-700 border border-amber-200/60 whitespace-nowrap">
                                                Layanan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-24 text-center text-slate-400 text-sm">
                                <div class="font-bold text-[#232E28] text-base mb-1">Tidak ada produk ditemukan.</div>
                                <div class="text-xs text-[#718379]">Gunakan kata kunci pencarian lain atau pilih kategori lain.</div>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Product List View (Aligned Table Rows) -->
                    <div class="bg-white rounded-2xl border border-[#E3EEE8] overflow-hidden">
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
                                    wire:click="addToCart({{ $product->id }})"
                                    class="p-3 hover:bg-[#F3F6F4] cursor-pointer transition-colors flex items-center justify-between gap-3 group {{ $isIncomplete ? 'opacity-60 bg-rose-50/20' : '' }}"
                                >
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <!-- Code & Initials Badge -->
                                        <div class="w-10 h-10 rounded-xl bg-[#E3EEE8]/70 border border-[#3F7A5D]/20 flex items-center justify-center font-mono font-extrabold text-sm text-[#3F7A5D] shrink-0 group-hover:bg-[#3F7A5D] group-hover:text-white transition-colors">
                                            {{ $initials }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">
                                                    {{ $product->code }}
                                                </span>
                                                <span class="text-[9.5px] uppercase font-bold px-1.5 py-0.2 rounded border {{ $product->product_type === 'PHYSICAL' ? 'bg-[#3F7A5D]/10 text-[#3F7A5D] border-[#3F7A5D]/20' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100/90 text-emerald-800 border-emerald-300/60' : 'bg-[#C2AC7C]/15 text-[#8F794B] border-[#C2AC7C]/30') }}">
                                                    {{ $product->product_type }}
                                                </span>
                                            </div>
                                            <h4 class="text-xs font-bold text-[#232E28] group-hover:text-[#3F7A5D] transition-colors truncate">
                                                {{ $product->name }}
                                            </h4>
                                        </div>
                                    </div>

                                    <!-- Price & Stock Alignment Column -->
                                    <div class="flex items-center gap-3 shrink-0 text-right">
                                        <div class="min-w-[100px] text-right">
                                            @if($product->product_type === 'LAYANAN')
                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-teal-700 bg-teal-50 border border-teal-200/80 px-2 py-0.5 rounded whitespace-nowrap">
                                                    Input Nominal
                                                </span>
                                            @elseif($isIncomplete)
                                                <span class="text-[9px] uppercase font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded">
                                                    INCOMPLETE
                                                </span>
                                            @else
                                                <div class="text-xs font-extrabold text-[#232E28] font-mono">
                                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="w-20 text-right">
                                            @if($product->product_type === 'PHYSICAL')
                                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] inline-block {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700' : 'bg-[#E3EEE8] text-[#3F7A5D]') }}">
                                                    Stok: {{ $stockQty }}
                                                </span>
                                            @elseif($product->product_type === 'DIGITAL')
                                                <span class="px-2 py-0.5 rounded-full font-bold text-[9.5px] bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-block">
                                                    Digital
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full font-bold text-[9.5px] bg-amber-50 text-amber-700 border border-amber-200/60 inline-block">
                                                    Layanan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center text-slate-400 text-sm">
                                    <div class="font-bold text-[#232E28] text-base mb-1">Tidak ada produk ditemukan.</div>
                                    <div class="text-xs text-[#718379]">Gunakan kata kunci pencarian lain atau pilih kategori lain.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: Golden Ratio Cart Sidebar (38.2% Proportion) -->
        <div class="w-full lg:w-[38.2%] bg-white rounded-3xl border border-[#E3EEE8] flex flex-col flex-shrink-0 overflow-hidden h-full shadow-sm">

            <!-- 1. Cart Header -->
            <div class="px-5 py-3.5 bg-white border-b border-slate-200/80 flex items-center justify-between shrink-0">
                <div class="font-extrabold text-xs text-[#232E28] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Keranjang</span>
                    <span class="bg-[#E3EEE8] text-[#3F7A5D] px-2.5 py-0.5 rounded-full text-xs font-extrabold font-mono">
                        {{ count($cart) }}
                    </span>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-rose-600 hover:underline font-bold transition cursor-pointer">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- 2. MAXIMIZED FLEX-1 CART ITEMS SCROLLABLE LIST -->
            <div class="flex-1 overflow-y-auto px-5 py-3 divide-y divide-slate-100 min-h-0">
                @forelse($cart as $id => $item)
                    <div class="py-3 flex items-center justify-between gap-3 group">
                        <!-- Product Name & Unit Price -->
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-[#232E28] truncate leading-snug group-hover:text-[#3F7A5D] transition-colors" title="{{ $item['name'] }}">
                                {{ $item['name'] }}
                            </div>
                            <div class="text-[11px] text-[#718379] font-mono font-semibold mt-0.5">
                                @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Fixed-Width Perfectly Aligned Quantity +/- Stepper -->
                        <div class="shrink-0 w-[96px] flex items-center justify-between bg-[#F3F6F4] p-1 rounded-2xl border border-slate-200/80 shadow-2xs">
                            <button
                                type="button"
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-7 h-7 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 font-extrabold text-xs rounded-xl flex items-center justify-center text-[#232E28] transition active:scale-95 border border-slate-200 shadow-2xs cursor-pointer"
                                title="Kurangi 1"
                            >-</button>

                            <span class="flex-1 text-center font-black text-xs font-mono text-[#232E28] tracking-tight">
                                {{ $item['quantity'] }}
                            </span>

                            <button
                                type="button"
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-7 h-7 bg-white hover:bg-[#E3EEE8] hover:text-[#3F7A5D] hover:border-[#3F7A5D]/30 font-extrabold text-xs rounded-xl flex items-center justify-center text-[#232E28] transition active:scale-95 border border-slate-200 shadow-2xs cursor-pointer"
                                title="Tambah 1"
                            >+</button>
                        </div>

                        <!-- Fixed-Width Subtotal & Delete Action -->
                        <div class="shrink-0 w-[95px] text-right">
                            <div class="text-xs font-extrabold text-[#232E28] font-mono tracking-tight">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-[10px] uppercase font-bold text-rose-500 hover:text-rose-700 transition cursor-pointer mt-0.5">
                                Hapus
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
                        <div class="font-bold text-[#232E28] text-sm">Keranjang Kosong</div>
                        <div class="text-xs text-[#718379] max-w-xs">Pilih barang di katalog untuk menambahkan ke transaksi.</div>
                    </div>
                @endforelse
            </div>

            <!-- 3. PROPORTIONAL PAYMENT FOOTER -->
            <div class="p-4 sm:p-5 border-t border-slate-200/80 bg-[#F3F6F4]/60 space-y-3 shrink-0">

                <!-- Grand Total Billing Card -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-baseline justify-between">
                    <div>
                        <div class="text-xs font-extrabold uppercase text-[#718379] tracking-wider">Total Belanja</div>
                        <div class="text-xs text-slate-400 font-semibold mt-0.5">Subtotal: Rp {{ number_format($this->subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-2xl font-extrabold text-[#232E28] font-mono tracking-tight">
                        Rp {{ number_format($this->grand_total, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Payment Method Inputs -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-extrabold text-[#232E28]">
                        <span>Metode Pembayaran</span>
                        <button wire:click="addPaymentRow" class="text-[#3F7A5D] hover:underline text-xs font-bold cursor-pointer">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 space-y-2 text-xs shadow-xs">
                            <div class="flex items-center gap-2">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-2 border border-slate-200 rounded-xl bg-white text-xs font-bold text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-2 text-xs font-bold text-[#718379]">Rp</span>
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
                                        value="{{ $pay['amount'] ? number_format($pay['amount'], 0, ',', '.') : '' }}"
                                        placeholder="0"
                                        class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl font-mono font-extrabold text-right text-xs text-[#232E28] focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                    />
                                </div>

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold px-1 text-base cursor-pointer">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            <!-- Quick Cash Nominal Shortcuts for Cash Payment -->
                            @if($selectedPm && $selectedPm->type === 'CASH' && $this->grand_total > 0)
                                <div class="flex items-center gap-1.5 pt-0.5">
                                    <button
                                        type="button"
                                        wire:click="$set('payments.{{ $index }}.amount', {{ min(1000000000, $this->grand_total) }})"
                                        class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-[#3F7A5D] border border-emerald-200/80 rounded-lg text-[11px] font-bold transition shrink-0 cursor-pointer"
                                    >
                                        Uang Pas
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('payments.{{ $index }}.amount', 50000)"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200/80 rounded-lg text-[11px] font-bold transition shrink-0 cursor-pointer"
                                    >
                                        50k
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('payments.{{ $index }}.amount', 100000)"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200/80 rounded-lg text-[11px] font-bold transition shrink-0 cursor-pointer"
                                    >
                                        100k
                                    </button>
                                </div>
                            @endif

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-2 border border-slate-200 rounded-xl bg-indigo-50/50 text-xs font-semibold text-indigo-900 focus:ring-2 focus:ring-indigo-500/20">
                                        <option value="">-- Pilih Akun Bank/E-Wallet Tujuan --</option>
                                        @foreach($balanceAccounts as $ba)
                                            <option value="{{ $ba->id }}">{{ $ba->name }} ({{ $ba->account_type }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Cash Change Display (Overflow Protected) -->
                <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between text-xs overflow-hidden">
                    <span class="font-extrabold text-[#232E28] shrink-0">Kembalian</span>
                    <span class="truncate text-right font-mono font-extrabold {{ $this->change_amount > 0 ? 'text-emerald-700 text-lg sm:text-xl' : 'text-[#232E28] text-xs font-bold' }}">
                        Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Primary Action Checkout Button (High Contrast Disabled & Active) -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-3.5 rounded-2xl font-extrabold text-xs uppercase tracking-wider transition-all {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-[#3F7A5D] hover:bg-[#32634B] text-white shadow-sm cursor-pointer active:scale-[0.99]' : 'bg-slate-100 text-slate-400 border border-slate-200/80 cursor-not-allowed' }}"
                >
                    SELESAIKAN TRANSAKSI & CETAK STRUK
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-sm w-full shadow-2xl text-center space-y-5 border border-slate-100">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto text-3xl font-bold">
                    ÃƒÆ’Ã‚Â¢Ãƒâ€¦Ã¢â‚¬Å“ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#232E28]">Transaksi Berhasil!</h3>
                    <p class="text-xs text-[#3F7A5D] font-mono mt-1 font-bold">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-[#F3F6F4] p-5 rounded-2xl border border-slate-200">
                    <div class="text-xs text-[#718379] font-medium">Kembali</div>
                    <div class="text-3xl font-extrabold text-emerald-700 font-mono mt-1">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-1">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-3.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-xs transition uppercase tracking-wider text-center"
                    >
                        CETAK STRUK THERMAL
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-semibold rounded-2xl text-xs transition"
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
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                    <!-- PPOB Open-Nominal Bill Modal -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">Layanan</span>
                            <h3 class="text-base font-extrabold text-[#232E28] mt-1">{{ $selectedPpobProductName }}</h3>
                        </div>
                        <button wire:click="$set('showPpobModal', false)" class="text-slate-400 hover:text-slate-600 font-bold text-xl px-2 cursor-pointer">&times;</button>
                    </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-[#232E28] mb-1">{{ $ppobModalLabel }} Pelanggan *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-xs font-bold text-[#718379]">Rp</span>
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
                                class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl font-mono font-extrabold text-base text-right text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                autofocus
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#232E28] mb-1">Biaya Admin Toko (Jasa) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-[#718379]">Rp</span>
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
                                    class="w-full pl-8 pr-2.5 py-2 border border-slate-200 rounded-xl font-mono font-bold text-right text-xs text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#232E28] mb-1">Biaya Admin Vendor *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-[#718379]">Rp</span>
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
                                    class="w-full pl-8 pr-2.5 py-2 border border-slate-200 rounded-xl font-mono font-bold text-right text-xs text-[#718379] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
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
                        <div class="flex items-center justify-between text-xs font-bold text-[#232E28]">
                            <span>Total Ditagihkan Ke Pelanggan:</span>
                            <span class="font-mono text-sm text-[#3F7A5D] font-extrabold">Rp {{ number_format($calcTotalPay, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[#718379]">
                            <span>Estimasi Modal Toko:</span>
                            <span class="font-mono">Rp {{ number_format($calcTotalCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-extrabold text-emerald-700 pt-1 border-t border-slate-200/60">
                            <span>Estimasi Keuntungan Toko (Margin):</span>
                            <span class="font-mono">Rp {{ number_format($calcMargin, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button
                        wire:click="confirmAddPpobToCart"
                        class="flex-1 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-xs transition uppercase tracking-wider shadow-sm cursor-pointer"
                    >
                        Masukkan Ke Keranjang
                    </button>
                    <button
                        wire:click="$set('showPpobModal', false)"
                        class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition cursor-pointer"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
