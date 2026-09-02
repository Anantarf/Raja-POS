<div class="h-screen flex flex-col overflow-hidden bg-[#F3F6F4] font-sans text-[#232E28]">
    <!-- Compact Topbar Navigation Header -->
    <header class="px-5 pt-3 pb-2 flex-shrink-0">
        <div class="bg-white rounded-2xl px-5 py-2.5 flex items-center justify-between border border-[#E3EEE8]">
            <div class="flex items-center gap-3">
                <span class="bg-[#3F7A5D] text-white font-extrabold px-3 py-1 rounded-xl text-xs tracking-wider uppercase">RAJA POS</span>
                <span class="text-xs font-extrabold text-[#232E28] hidden sm:inline border-l border-[#E3EEE8] pl-3">
                    {{ $location?->name ?? 'Raja Aksesoris Bango' }}
                </span>
            </div>

            <div class="flex items-center gap-3 text-xs font-semibold">
                <div class="flex items-center gap-2 bg-[#F3F6F4] px-3 py-1.5 rounded-xl border border-[#E3EEE8]">
                    <div class="w-6 h-6 rounded-full bg-[#3F7A5D] text-white font-bold flex items-center justify-center text-[10px]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="font-extrabold text-[#232E28] text-xs">{{ auth()->user()->name }}</span>
                    <span class="text-[#718379] font-normal text-[11px]">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
                </div>

                <a href="/admin" class="bg-[#3F7A5D] hover:bg-[#32634B] text-white px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Ke Panel Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Operational Split View -->
    <div class="flex-1 flex overflow-hidden px-5 pb-4 gap-4">

        <!-- LEFT COLUMN: Product Catalog (65%) -->
        <div class="w-full lg:w-[65%] flex flex-col flex-shrink-0">

            <!-- Compact Search Bar & Filters Header -->
            <div class="p-3.5 bg-white rounded-2xl border border-[#E3EEE8] space-y-2.5 mb-3">
                <!-- Search Input -->
                <div class="relative">
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

                <!-- Category Tabs & Product Type Pills -->
                <div class="flex items-center justify-between gap-2 text-xs pt-0.5">
                    <!-- Category Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto py-1 px-0.5 shrink min-w-0">
                        <button
                            wire:click="$set('selectedCategory', null)"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 border {{ $selectedCategory === null ? 'bg-[#3F7A5D] text-white border-[#3F7A5D]' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                        >
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button
                                wire:click="$set('selectedCategory', {{ $cat->id }})"
                                class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 border {{ $selectedCategory === $cat->id ? 'bg-[#3F7A5D] text-white border-[#3F7A5D]' : 'bg-[#F3F6F4] text-[#232E28] border-slate-200 hover:bg-slate-200' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Type Filter Pills -->
                    <div class="flex items-center gap-1 shrink-0 text-xs font-bold border-l border-[#E3EEE8] pl-3">
                        <button
                            wire:click="$set('selectedType', 'ALL')"
                            class="px-3 py-1 rounded-lg transition border {{ $selectedType === 'ALL' ? 'bg-[#232E28] text-white border-[#232E28]' : 'bg-white text-[#232E28] border-slate-200 hover:bg-slate-50' }}"
                        >
                            Semua
                        </button>
                        <button
                            wire:click="$set('selectedType', 'PHYSICAL')"
                            class="px-3 py-1 rounded-lg transition border {{ $selectedType === 'PHYSICAL' ? 'bg-[#3F7A5D] text-white border-[#3F7A5D]' : 'bg-white text-[#232E28] border-slate-200 hover:bg-slate-50' }}"
                        >
                            Fisik
                        </button>
                        <button
                            wire:click="$set('selectedType', 'DIGITAL')"
                            class="px-3 py-1 rounded-lg transition border {{ $selectedType === 'DIGITAL' ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-white text-[#232E28] border-slate-200 hover:bg-slate-50' }}"
                        >
                            Digital
                        </button>
                        <button
                            wire:click="$set('selectedType', 'SERVICE')"
                            class="px-3 py-1 rounded-lg transition border {{ $selectedType === 'SERVICE' ? 'bg-[#C2AC7C] text-white border-[#C2AC7C]' : 'bg-white text-[#232E28] border-slate-200 hover:bg-slate-50' }}"
                        >
                            Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 overflow-y-auto pr-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @forelse($products as $product)
                    @php
                        $isIncomplete = $product->price_status === 'INCOMPLETE';
                        $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->where('location_id', $location?->id)->first() : null;
                        $stockQty = $inv?->quantity ?? 0;
                        $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                    @endphp

                    <div
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white border border-[#E3EEE8] hover:border-[#3F7A5D]/60 hover:bg-[#F3F6F4]/50 rounded-2xl p-3 flex flex-col justify-between cursor-pointer transition-all duration-200 relative group {{ $isIncomplete ? 'opacity-65 bg-rose-50/20' : '' }}"
                    >
                        <div>
                            <!-- Top Metadata Row -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-mono text-[#3F7A5D] bg-[#E3EEE8] px-2 py-0.5 rounded font-bold">
                                    SKU: {{ $product->code }}
                                </span>
                                <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-full {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-[#C2AC7C]/20 text-[#8F794B] border border-[#C2AC7C]/40') }}">
                                    {{ $product->product_type }}
                                </span>
                            </div>

                            <!-- Product Thumbnail & Title -->
                            <div class="flex items-start gap-2.5 mb-2">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-xl bg-slate-100 border border-slate-200/70 shrink-0">
                                <div class="text-xs font-bold text-[#232E28] line-clamp-2 leading-snug tracking-tight group-hover:text-[#3F7A5D] transition-colors">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock Indicator -->
                        <div class="mt-2 pt-2 border-t border-slate-100">
                            @if($isIncomplete)
                                <div class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded text-center">
                                    HARGA INCOMPLETE
                                </div>
                            @else
                                <div class="text-sm font-extrabold text-[#3F7A5D] font-mono tracking-tight">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($product->product_type === 'PHYSICAL')
                                <div class="flex items-center justify-between mt-1 text-[11px]">
                                    <span class="text-[#718379] font-medium">Stok: {{ $stockQty }}</span>
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[9px] {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                        {{ $stockStatus === 'OUT_OF_STOCK' ? 'HABIS' : ($stockStatus === 'LOW_STOCK' ? 'MENIPIS' : 'TERSEDIA') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-400 text-xs">
                        <div class="font-medium text-[#232E28] text-sm">Tidak ada produk ditemukan.</div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-3 border-t border-[#E3EEE8] bg-white rounded-2xl mt-2">
                {{ $products->links() }}
            </div>
        </div>

        <!-- RIGHT COLUMN: MAXIMIZED VERTICAL CART SPACE (35%) -->
        <div class="w-full lg:w-[35%] bg-white rounded-2xl border border-[#E3EEE8] flex flex-col flex-shrink-0 overflow-hidden h-full">

            <!-- 1. Compact Cart Header -->
            <div class="px-4 py-3 bg-white border-b border-[#E3EEE8] flex items-center justify-between shrink-0">
                <div class="font-extrabold text-xs text-[#232E28] uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                    </svg>
                    <span>Keranjang Belanja</span>
                    <span class="bg-[#E3EEE8] text-[#3F7A5D] px-2 py-0.5 rounded-full text-[11px] font-bold font-mono">
                        {{ count($cart) }}
                    </span>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-[11px] text-rose-600 hover:underline font-bold transition">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- 2. MAXIMIZED FLEX-1 CART ITEMS LIST (Takes 70%+ Vertical Height) -->
            <div class="flex-1 overflow-y-auto px-4 py-2 divide-y divide-slate-100 min-h-0">
                @forelse($cart as $id => $item)
                    <div class="py-2.5 flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-[#232E28] truncate leading-snug">{{ $item['name'] }}</div>
                            <div class="text-[11px] text-[#718379] font-mono font-medium mt-0.5">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Quantity +/- Controls -->
                        <div class="flex items-center gap-1 bg-[#F3F6F4] p-1 rounded-lg border border-slate-200">
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-5 h-5 bg-white hover:bg-slate-200 font-bold text-xs rounded flex items-center justify-center text-slate-700 transition active:scale-95 border border-slate-200"
                            >-</button>
                            <span class="w-5 text-center font-bold text-xs font-mono text-[#232E28]">{{ $item['quantity'] }}</span>
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-5 h-5 bg-white hover:bg-slate-200 font-bold text-xs rounded flex items-center justify-center text-slate-700 transition active:scale-95 border border-slate-200"
                            >+</button>
                        </div>

                        <!-- Subtotal & Delete -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-[#3F7A5D] font-mono">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-rose-500 hover:underline font-bold">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="h-full min-h-[140px] flex flex-col items-center justify-center text-center text-slate-400 space-y-1.5 p-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <div class="font-bold text-[#232E28] text-xs">Keranjang Belanja Kosong</div>
                        <div class="text-[11px] text-[#718379]">Pilih barang di katalog untuk menambahkan.</div>
                    </div>
                @endforelse
            </div>

            <!-- 3. COMPACT HIGH-DENSITY PAYMENT FOOTER (Fixed at Bottom) -->
            <div class="p-3.5 border-t border-[#E3EEE8] bg-[#F3F6F4]/50 space-y-2.5 shrink-0">

                <!-- Compact Grand Total Line -->
                <div class="bg-white p-3 rounded-xl border border-[#E3EEE8] flex items-baseline justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-[#718379] tracking-wider">Total Belanja</div>
                        <div class="text-[11px] text-slate-400 font-medium">Subtotal: Rp {{ number_format($this->subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-xl font-extrabold text-[#3F7A5D] font-mono">
                        Rp {{ number_format($this->grand_total, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Cash Quick Nominal Shortcut Pills -->
                @if(count($cart) > 0)
                    <div class="flex items-center gap-1 overflow-x-auto text-[11px] font-bold py-0.5">
                        <span class="text-[#718379] shrink-0 font-medium text-[10px]">Uang:</span>
                        <button wire:click="setExactPayment" class="px-2.5 py-1 bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/30 rounded-lg shrink-0 hover:bg-[#3F7A5D]/10 font-bold">Uang Pas</button>
                        <button wire:click="setPaymentAmount(10000)" class="px-2 py-1 bg-white border border-slate-200 rounded-lg shrink-0 hover:bg-slate-100 text-[#232E28]">10rb</button>
                        <button wire:click="setPaymentAmount(20000)" class="px-2 py-1 bg-white border border-slate-200 rounded-lg shrink-0 hover:bg-slate-100 text-[#232E28]">20rb</button>
                        <button wire:click="setPaymentAmount(50000)" class="px-2 py-1 bg-white border border-slate-200 rounded-lg shrink-0 hover:bg-slate-100 text-[#232E28]">50rb</button>
                        <button wire:click="setPaymentAmount(100000)" class="px-2 py-1 bg-white border border-slate-200 rounded-lg shrink-0 hover:bg-slate-100 text-[#232E28]">100rb</button>
                    </div>
                @endif

                <!-- Compact Payment Method Split Inputs -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-extrabold text-[#232E28]">
                        <span>Metode Pembayaran</span>
                        <button wire:click="addPaymentRow" class="text-[#3F7A5D] hover:underline text-[11px] font-bold">
                            + Tambah
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-2 rounded-xl border border-[#E3EEE8] space-y-1 text-xs">
                            <div class="flex items-center gap-1.5">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-1.5 border border-slate-200 rounded-lg bg-white text-[11px] font-semibold focus:outline-none focus:border-[#3F7A5D]">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <div class="relative flex-1">
                                    <span class="absolute left-2.5 top-1.5 text-[11px] font-bold text-[#718379]">Rp</span>
                                    <input
                                        type="number"
                                        wire:model.live="payments.{{ $index }}.amount"
                                        placeholder="0"
                                        class="w-full pl-7 pr-2 py-1.5 border border-slate-200 rounded-lg font-mono font-extrabold text-right text-xs text-[#3F7A5D] focus:outline-none focus:border-[#3F7A5D]"
                                    />
                                </div>

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-rose-500 hover:text-rose-700 font-bold px-1 text-sm">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-1.5 border border-[#C2AC7C]/40 rounded-lg bg-[#C2AC7C]/10 text-[11px] font-semibold text-[#8F794B]">
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

                <!-- Compact Change Display -->
                <div class="bg-white px-3 py-2 rounded-xl border border-[#E3EEE8] flex items-center justify-between text-xs">
                    <span class="font-bold text-[#232E28]">Kembalian</span>
                    <span class="{{ $this->change_amount > 0 ? 'text-emerald-700 font-mono text-base font-extrabold' : 'text-[#232E28] font-mono text-xs font-bold' }}">
                        Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Primary Action Checkout Button -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-3 rounded-xl font-extrabold text-xs text-white uppercase tracking-wider transition-all {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-[#3F7A5D] hover:bg-[#32634B] cursor-pointer' : 'bg-[#E3EEE8] text-[#718379] cursor-not-allowed border border-slate-200' }}"
                >
                    SELESAIKAN TRANSAKSI & CETAK STRUK
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4 border border-slate-100">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
                    ✓
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#232E28]">Transaksi Berhasil!</h3>
                    <p class="text-xs text-[#3F7A5D] font-mono mt-1 font-bold">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-[#F3F6F4] p-4 rounded-2xl border border-slate-200">
                    <div class="text-xs text-[#718379] font-medium">Kembali</div>
                    <div class="text-2xl font-extrabold text-emerald-700 font-mono mt-1">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-2.5 pt-1">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs transition uppercase tracking-wider text-center"
                    >
                        CETAK STRUK THERMAL
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-semibold rounded-xl text-xs transition"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
