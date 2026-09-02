<div class="h-screen flex flex-col overflow-hidden bg-slate-100 font-sans text-slate-800">
    <!-- Topbar Navigation Header -->
    <header class="bg-navy-900 text-white px-5 py-3 flex items-center justify-between shadow-sm z-20 flex-shrink-0 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="bg-amber-500 text-navy-950 font-bold px-3 py-1 rounded-full text-xs tracking-wider uppercase shadow-sm">RAJA POS</span>
                <span class="text-sm font-semibold text-slate-200 hidden sm:inline border-l border-slate-700 pl-3">
                    {{ $location?->name ?? 'Raja Aksesoris Bango' }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-2 bg-slate-800 px-3.5 py-1.5 rounded-xl border border-slate-700 text-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="font-semibold text-slate-100">{{ auth()->user()->name }}</span>
                <span class="text-slate-400 font-normal">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
            </div>
            <a href="/admin" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                <span>Admin Panel</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </header>

    <!-- Main Operational Split View -->
    <div class="flex-1 flex overflow-hidden">

        <!-- LEFT COLUMN: Product Catalog (65%) -->
        <div class="w-full lg:w-[65%] flex flex-col border-r border-slate-200 bg-slate-50 flex-shrink-0">

            <!-- Search Bar & Filters Header -->
            <div class="p-4 bg-white border-b border-slate-200 space-y-3 shadow-sm">
                <!-- Search Input (Enlarged for readability) -->
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk / scan barcode..."
                        class="w-full pl-11 pr-9 py-3 text-sm font-medium border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/70 placeholder:text-slate-400 transition-all"
                        autofocus
                    />
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 text-sm font-bold bg-slate-200 rounded-full w-5 h-5 flex items-center justify-center">
                            &times;
                        </button>
                    @endif
                </div>

                <!-- Category Tabs & Product Type Pills -->
                <div class="flex items-center justify-between gap-2 overflow-x-auto text-xs pt-0.5">
                    <!-- Category Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5">
                        <button
                            wire:click="$set('selectedCategory', null)"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition-all shrink-0 border {{ $selectedCategory === null ? 'bg-navy-900 text-white border-navy-900 shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}"
                        >
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button
                                wire:click="$set('selectedCategory', {{ $cat->id }})"
                                class="px-4 py-2 rounded-full text-xs font-semibold transition-all shrink-0 border {{ $selectedCategory === $cat->id ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Type Filter Pills -->
                    <div class="flex items-center gap-1 shrink-0 text-xs font-semibold border-l border-slate-200 pl-3">
                        <button
                            wire:click="$set('selectedType', 'ALL')"
                            class="px-3 py-1.5 rounded-lg transition border {{ $selectedType === 'ALL' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            Semua
                        </button>
                        <button
                            wire:click="$set('selectedType', 'PHYSICAL')"
                            class="px-3 py-1.5 rounded-lg transition border {{ $selectedType === 'PHYSICAL' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            Fisik
                        </button>
                        <button
                            wire:click="$set('selectedType', 'DIGITAL')"
                            class="px-3 py-1.5 rounded-lg transition border {{ $selectedType === 'DIGITAL' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            Digital
                        </button>
                        <button
                            wire:click="$set('selectedType', 'SERVICE')"
                            class="px-3 py-1.5 rounded-lg transition border {{ $selectedType === 'SERVICE' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid (Scaled up for high readability) -->
            <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($products as $product)
                    @php
                        $isIncomplete = $product->price_status === 'INCOMPLETE';
                        $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->where('location_id', $location?->id)->first() : null;
                        $stockQty = $inv?->quantity ?? 0;
                        $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                    @endphp

                    <div
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white border border-slate-200/90 rounded-2xl p-3.5 flex flex-col justify-between cursor-pointer hover:border-blue-600 hover:shadow-md transition-all duration-200 relative group {{ $isIncomplete ? 'opacity-65 bg-red-50/20' : '' }}"
                    >
                        <div>
                            <!-- Top Metadata Row -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-mono text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md font-medium border border-slate-200/60">
                                    {{ $product->code }}
                                </span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $product->product_type === 'PHYSICAL' ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60') }}">
                                    {{ $product->product_type }}
                                </span>
                            </div>

                            <!-- Product Thumbnail & Title -->
                            <div class="flex items-start gap-2.5 mb-2">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-11 h-11 object-cover rounded-xl bg-slate-100 border border-slate-200/70 shrink-0">
                                <div class="text-sm font-semibold text-slate-900 line-clamp-2 leading-snug tracking-tight group-hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock Indicator -->
                        <div class="mt-2.5 pt-2.5 border-t border-slate-100">
                            @if($isIncomplete)
                                <div class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-1 rounded-md text-center">
                                    HARGA INCOMPLETE
                                </div>
                            @else
                                <div class="text-sm font-bold text-blue-600 tracking-tight">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($product->product_type === 'PHYSICAL')
                                <div class="flex items-center justify-between mt-1.5 text-xs">
                                    <span class="text-slate-500 font-medium">Stok: {{ $stockQty }}</span>
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                        {{ $stockStatus === 'OUT_OF_STOCK' ? 'HABIS' : ($stockStatus === 'LOW_STOCK' ? 'MENIPIS' : 'TERSEDIA') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center text-slate-400 text-sm">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <div class="font-medium text-slate-500">Tidak ada produk ditemukan.</div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-3 border-t border-slate-200 bg-white">
                {{ $products->links() }}
            </div>
        </div>

        <!-- RIGHT COLUMN: Cart & Multi-Payment Checkout (35%) (Enlarged & Prominent) -->
        <div class="w-full lg:w-[35%] bg-white flex flex-col flex-shrink-0 shadow-md border-l border-slate-200">

            <!-- Cart Header -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="font-bold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                    </svg>
                    KERANJANG BELANJA ({{ count($cart) }})
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-rose-600 hover:underline font-semibold">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- Cart Items List (Larger row items) -->
            <div class="flex-1 overflow-y-auto p-4 divide-y divide-slate-100">
                @forelse($cart as $id => $item)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-slate-900 truncate leading-snug">{{ $item['name'] }}</div>
                            <div class="text-xs text-slate-500 font-mono font-medium mt-0.5">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Quantity +/- Controls (Enlarged) -->
                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-6 h-6 bg-white hover:bg-slate-200 font-bold text-sm rounded-lg flex items-center justify-center text-slate-700 transition shadow-sm active:scale-95"
                            >-</button>
                            <span class="w-6 text-center font-bold text-sm font-mono text-slate-900">{{ $item['quantity'] }}</span>
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-6 h-6 bg-white hover:bg-slate-200 font-bold text-sm rounded-lg flex items-center justify-center text-slate-700 transition shadow-sm active:scale-95"
                            >+</button>
                        </div>

                        <!-- Subtotal & Delete -->
                        <div class="text-right shrink-0">
                            <div class="text-sm font-bold text-slate-900 font-mono">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-xs text-rose-500 hover:underline font-medium">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center text-slate-400 text-xs">
                        <div class="font-medium text-slate-400 text-sm">Keranjang masih kosong.</div>
                        <div class="text-xs text-slate-400 mt-1">Klik produk di katalog untuk menambahkan.</div>
                    </div>
                @endforelse
            </div>

            <!-- Cart Summary & Multi-Payment Panel (Prominent Typography) -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3.5">
                <!-- Summary Card (Enlarged Subtotal & Total figures) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2 text-xs shadow-sm">
                    <div class="flex justify-between text-slate-600 text-xs">
                        <span class="font-medium">Subtotal</span>
                        <span class="font-semibold font-mono text-slate-900 text-sm">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 text-xs">
                        <span class="font-medium">Diskon</span>
                        <span class="font-semibold font-mono text-slate-900 text-sm">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-baseline border-t border-slate-100 pt-2.5 mt-1">
                        <span class="text-sm font-bold text-slate-900">Total Belanja</span>
                        <span class="text-blue-600 font-mono text-xl font-bold">Rp {{ number_format($this->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Cash Nominal Quick Shortcut Pills (Enlarged buttons) -->
                @if(count($cart) > 0)
                    <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold">
                        <span class="text-slate-500 shrink-0 font-medium">Bayar:</span>
                        <button wire:click="setExactPayment" class="px-3.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full shrink-0 hover:bg-blue-100 font-bold shadow-sm">Uang Pas</button>
                        <button wire:click="setPaymentAmount(10000)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-full shrink-0 hover:bg-slate-100 shadow-sm">10rb</button>
                        <button wire:click="setPaymentAmount(20000)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-full shrink-0 hover:bg-slate-100 shadow-sm">20rb</button>
                        <button wire:click="setPaymentAmount(50000)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-full shrink-0 hover:bg-slate-100 shadow-sm">50rb</button>
                        <button wire:click="setPaymentAmount(100000)" class="px-3 py-1.5 bg-white border border-slate-200 rounded-full shrink-0 hover:bg-slate-100 shadow-sm">100rb</button>
                    </div>
                @endif

                <!-- Payment Methods Split (Enlarged inputs) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-900">
                        <span>Metode Pembayaran</span>
                        <button wire:click="addPaymentRow" class="text-blue-600 hover:underline text-xs font-semibold">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-2 text-xs shadow-sm">
                            <div class="flex items-center gap-2">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-2.5 border border-slate-300 rounded-xl bg-white text-xs font-semibold focus:outline-none focus:border-blue-600">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <input
                                    type="number"
                                    wire:model.live="payments.{{ $index }}.amount"
                                    placeholder="Nominal"
                                    class="w-1/2 p-2.5 border border-slate-300 rounded-xl font-mono font-bold text-right text-sm focus:outline-none focus:border-blue-600"
                                />

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold px-1 text-base">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-2 border border-amber-200 rounded-xl bg-amber-50 text-xs font-semibold text-amber-900">
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

                <!-- Total Paid & Calculated Change Box (Large prominent font for change) -->
                <div class="bg-white p-3.5 rounded-2xl border border-slate-200 space-y-2 text-xs shadow-sm">
                    <div class="flex justify-between text-slate-600 text-xs">
                        <span class="font-medium">Jumlah Bayar</span>
                        <span class="font-bold font-mono text-slate-900 text-sm">Rp {{ number_format($this->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-baseline border-t border-slate-100 pt-2 mt-1">
                        <span class="text-sm font-bold text-slate-900">Kembali</span>
                        <span class="{{ $this->change_amount > 0 ? 'text-emerald-600 font-mono text-xl font-extrabold' : 'text-slate-900 font-mono text-base font-bold' }}">
                            Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($cashBalance < 0)
                        <div class="text-xs bg-amber-50 text-amber-800 p-2 rounded-xl border border-amber-200 mt-2 font-medium">
                            ⚠️ Warning: Saldo Uang Fisik Kasir Minus (Rp {{ number_format(abs($cashBalance), 0, ',', '.') }}). Operasional dapat diganti dari rekening.
                        </div>
                    @endif
                </div>

                <!-- Primary Action Checkout Button (Enlarged height & font) -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-4 rounded-xl font-bold text-sm text-white uppercase tracking-wider transition-all shadow-md {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer shadow-blue-500/20' : 'bg-slate-300 text-slate-500 cursor-not-allowed' }}"
                >
                    SELESAIKAN TRANSAKSI & CETAK STRUK
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4 border border-slate-100">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
                    ✓
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-500 font-mono mt-1 font-semibold">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="text-xs text-slate-500 font-medium">Kembali</div>
                    <div class="text-2xl font-bold text-emerald-600 font-mono">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-2.5 pt-1">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-sm"
                    >
                        🖨️ CETAK STRUK THERMAL
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
