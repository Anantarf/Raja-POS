<div class="h-screen flex flex-col overflow-hidden bg-slate-100 font-sans">
    <!-- Topbar Header -->
    <header class="bg-navy-950 text-white px-5 py-3 flex items-center justify-between shadow-lg z-20 flex-shrink-0 border-b border-slate-800/80">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-r from-amber-500 to-yellow-400 text-navy-950 font-extrabold px-3 py-1 rounded-lg text-xs tracking-wider shadow-glow-gold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-navy-950 animate-pulse"></span>
                RAJA POS
            </div>
            <div class="text-xs text-slate-300 hidden sm:flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span class="font-semibold text-slate-100">{{ $location?->name ?? 'Raja Aksesoris Bango' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-5 text-xs">
            <div class="flex items-center gap-2 bg-slate-900/80 px-3 py-1.5 rounded-xl border border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="text-left">
                    <div class="font-bold text-slate-100 leading-tight">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-slate-400 capitalize">{{ auth()->user()->role?->name ?? 'Kasir' }}</div>
                </div>
            </div>

            <a href="/admin" class="bg-slate-800/90 hover:bg-slate-700 text-slate-200 px-3.5 py-2 rounded-xl border border-slate-700 font-semibold transition-all flex items-center gap-1.5 hover:shadow-md">
                <span>Admin Panel</span>
                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Left Column: Product Catalog (65%) -->
        <div class="w-full lg:w-[65%] flex flex-col border-r border-slate-200/80 bg-slate-50 flex-shrink-0">
            <!-- Search & Filters Bar -->
            <div class="p-3.5 bg-white border-b border-slate-200/80 space-y-2.5 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Ketik Nama Produk / SKU / Barcode scanner..."
                            class="w-full pl-10 pr-9 py-2.5 text-xs font-medium border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600 bg-slate-50/80 transition-all shadow-inner"
                            autofocus
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold bg-slate-200 rounded-full w-4 h-4 flex items-center justify-center">
                                &times;
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Type Filters & Category Select -->
                <div class="flex items-center justify-between gap-2 overflow-x-auto text-xs pt-1">
                    <div class="flex items-center gap-1.5">
                        <button
                            wire:click="$set('selectedType', 'ALL')"
                            class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all shrink-0 {{ $selectedType === 'ALL' ? 'bg-navy-950 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            Semua Tipe
                        </button>
                        <button
                            wire:click="$set('selectedType', 'PHYSICAL')"
                            class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all shrink-0 flex items-center gap-1 {{ $selectedType === 'PHYSICAL' ? 'bg-blue-600 text-white shadow-glow-blue' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            📦 Fisik
                        </button>
                        <button
                            wire:click="$set('selectedType', 'DIGITAL')"
                            class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all shrink-0 flex items-center gap-1 {{ $selectedType === 'DIGITAL' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            ⚡ Digital
                        </button>
                        <button
                            wire:click="$set('selectedType', 'SERVICE')"
                            class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all shrink-0 flex items-center gap-1 {{ $selectedType === 'SERVICE' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            🛠️ Service
                        </button>
                    </div>

                    <div class="shrink-0">
                        <select wire:model.live="selectedCategory" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Grid Catalog -->
            <div class="flex-1 overflow-y-auto p-3.5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5">
                @forelse($products as $product)
                    @php
                        $isIncomplete = $product->price_status === 'INCOMPLETE';
                        $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->where('location_id', $location?->id)->first() : null;
                        $stockQty = $inv?->quantity ?? 0;
                        $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                    @endphp

                    <div
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white border rounded-2xl p-3 flex flex-col justify-between cursor-pointer hover:shadow-xl hover:-translate-y-0.5 hover:border-blue-500/60 transition-all duration-200 relative group overflow-hidden {{ $isIncomplete ? 'opacity-70 border-red-200 bg-red-50/10' : 'border-slate-200/90 shadow-sm' }}"
                    >
                        <!-- Top Badges -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-mono bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-md font-bold border border-slate-200/60">
                                    {{ $product->code }}
                                </span>
                                <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-full {{ $product->product_type === 'PHYSICAL' ? 'bg-blue-50 text-blue-600 border border-blue-200/50' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/50' : 'bg-amber-50 text-amber-600 border border-amber-200/50') }}">
                                    {{ $product->product_type }}
                                </span>
                            </div>

                            <!-- Image & Product Name -->
                            <div class="flex items-start gap-2.5 mb-2">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-xl bg-slate-100 shrink-0 border border-slate-100">
                                <div class="text-xs font-bold text-slate-800 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Price & Live Stock Indicator -->
                        <div class="mt-2 pt-2 border-t border-slate-100">
                            @if($isIncomplete)
                                <div class="text-[10px] font-bold text-red-600 bg-red-100/80 px-2 py-0.5 rounded-md text-center">
                                    HARGA INCOMPLETE
                                </div>
                            @else
                                <div class="text-xs font-extrabold text-blue-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($product->product_type === 'PHYSICAL')
                                <div class="flex items-center justify-between mt-1.5 text-[10px]">
                                    <span class="text-slate-500 font-medium">Stok: {{ $stockQty }}</span>
                                    <span class="flex items-center gap-1 font-bold px-1.5 py-0.5 rounded-full {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-red-100 text-red-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-red-500' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-500 animate-ping' : 'bg-emerald-500') }}"></span>
                                        {{ $stockStatus === 'OUT_OF_STOCK' ? 'HABIS' : ($stockStatus === 'LOW_STOCK' ? 'MENIPIS' : 'TERSEDIA') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <div class="text-xs font-semibold">Produk tidak ditemukan</div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-2.5 border-t border-slate-200 bg-white">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Right Column: Shopping Cart & Multi-Payment Panel (35%) -->
        <div class="w-full lg:w-[35%] bg-white flex flex-col flex-shrink-0 shadow-2xl z-10 border-l border-slate-200">

            <!-- Cart Top Header -->
            <div class="p-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="font-extrabold text-xs text-navy-950 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                    </svg>
                    Keranjang Belanja
                    <span class="bg-blue-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">
                        {{ count($cart) }} Item
                    </span>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-[11px] text-rose-600 hover:text-rose-800 font-bold transition">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100">
                @forelse($cart as $id => $item)
                    <div class="py-2.5 flex items-center justify-between gap-2 hover:bg-slate-50/60 p-1.5 rounded-xl transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-[10px] text-slate-500 font-mono">
                                {{ $item['code'] }} &bull; Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Quantity +/- Controls -->
                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg border border-slate-200">
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-5 h-5 rounded bg-white hover:bg-slate-200 font-bold text-xs flex items-center justify-center text-slate-700 transition shadow-sm active:scale-95"
                            >-</button>
                            <span class="w-6 text-center font-extrabold text-xs font-mono">{{ $item['quantity'] }}</span>
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-5 h-5 rounded bg-white hover:bg-slate-200 font-bold text-xs flex items-center justify-center text-slate-700 transition shadow-sm active:scale-95"
                            >+</button>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-extrabold text-slate-900 font-mono">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-rose-500 hover:text-rose-700 font-bold">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center text-slate-400 text-xs">
                        Keranjang masih kosong. Klik produk di katalog untuk menambahkan.
                    </div>
                @endforelse
            </div>

            <!-- Summary & Payment Breakdown -->
            <div class="p-3.5 border-t border-slate-200 bg-slate-50 space-y-3">
                <!-- Grand Total Box -->
                <div class="bg-gradient-to-br from-navy-950 via-slate-900 to-navy-900 text-white p-3.5 rounded-2xl flex items-center justify-between shadow-lg border border-amber-500/20 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="text-[10px] text-slate-300 font-bold uppercase tracking-wider">Total Belanja</div>
                        <div class="text-xl font-extrabold text-amber-400 font-mono tracking-tight">
                            Rp {{ number_format($this->grand_total, 0, ',', '.') }}
                        </div>
                    </div>
                    @if(count($cart) > 0)
                        <button
                            wire:click="setExactPayment"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition shadow-glow-blue active:scale-95 relative z-10"
                        >
                            Uang Pas
                        </button>
                    @endif
                </div>

                <!-- Cash Nominal Quick Shortcut Pills -->
                @if(count($cart) > 0)
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[10px] font-bold">
                        <span class="text-slate-500 shrink-0">Nominal:</span>
                        <button wire:click="setPaymentAmount(10000)" class="px-2 py-1 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg shrink-0 shadow-sm">10rb</button>
                        <button wire:click="setPaymentAmount(20000)" class="px-2 py-1 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg shrink-0 shadow-sm">20rb</button>
                        <button wire:click="setPaymentAmount(50000)" class="px-2 py-1 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg shrink-0 shadow-sm">50rb</button>
                        <button wire:click="setPaymentAmount(100000)" class="px-2 py-1 bg-white hover:bg-slate-200 border border-slate-200 rounded-lg shrink-0 shadow-sm">100rb</button>
                        <button wire:click="addNominalToPayment(50000)" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg shrink-0 font-extrabold">+50rb</button>
                    </div>
                @endif

                <!-- Payment Split Section -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span>Pembayaran (Multi-Payment)</span>
                        <button wire:click="addPaymentRow" class="text-blue-600 hover:text-blue-800 text-[11px] font-bold">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 space-y-1.5 text-xs shadow-sm">
                            <div class="flex items-center gap-1.5">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-1.5 border border-slate-200 rounded-lg bg-slate-50 text-xs font-semibold">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <input
                                    type="number"
                                    wire:model.live="payments.{{ $index }}.amount"
                                    placeholder="Nominal"
                                    class="w-1/2 p-1.5 border border-slate-200 rounded-lg font-mono font-bold text-right text-xs focus:ring-2 focus:ring-blue-500/20"
                                />

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold px-1 text-sm">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-1.5 border border-amber-200 rounded-lg bg-amber-50/60 text-xs font-semibold text-amber-900">
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

                <!-- Change Calculation & Cash Minus Operational Warning -->
                <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1.5 text-xs shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Total Dibayar:</span>
                        <span class="font-bold font-mono">Rp {{ number_format($this->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-bold border-t pt-1.5">
                        <span class="text-slate-800">Kembalian:</span>
                        <span class="{{ $this->change_amount > 0 ? 'text-emerald-600 font-mono text-base font-extrabold' : 'text-slate-800 font-mono' }}">
                            Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($cashBalance < 0)
                        <div class="text-[10px] bg-amber-50 text-amber-800 p-2 rounded-lg border border-amber-200 mt-1 font-semibold">
                            ⚠️ Warning: Saldo Uang Fisik Kasir Minus (Rp {{ number_format(abs($cashBalance), 0, ',', '.') }}). Operasional dapat diganti dari rekening.
                        </div>
                    @endif
                </div>

                <!-- Process Checkout CTA Button -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-3.5 rounded-xl font-extrabold text-xs tracking-wider uppercase text-white shadow-lg transition-all duration-200 flex items-center justify-center gap-2 {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-emerald-600 hover:bg-emerald-500 cursor-pointer shadow-glow-blue active:scale-95' : 'bg-slate-300 text-slate-500 cursor-not-allowed' }}"
                >
                    <span>PROSES BAYAR & CETAK STRUK</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal with Receipt Trigger -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-navy-950/70 backdrop-blur-md flex items-center justify-center z-50 p-4 animate-fade-in">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4 border border-slate-100">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl font-extrabold shadow-inner">
                    ✓
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-500 font-mono mt-1 font-bold">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="text-xs text-slate-500 font-semibold">Kembalian Kasir</div>
                    <div class="text-2xl font-extrabold text-emerald-600 font-mono">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-extrabold rounded-xl text-xs transition-all shadow-glow-blue flex items-center justify-center gap-2"
                    >
                        <span>🖨️ CETAK STRUK THERMAL</span>
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
