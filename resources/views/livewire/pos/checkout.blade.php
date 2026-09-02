<div class="h-screen flex flex-col overflow-hidden bg-slate-100 font-sans text-slate-800">
    <!-- Topbar Header -->
    <header class="bg-navy-900 text-white px-4 py-2.5 flex items-center justify-between shadow-sm z-20 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="bg-amber-500 text-navy-900 font-bold px-2.5 py-0.5 rounded text-xs tracking-wider">RAJA POS</span>
                <span class="font-semibold text-xs text-slate-200 hidden sm:inline">{{ $location?->name ?? 'Raja Aksesoris Bango' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="text-right">
                <div class="font-medium text-slate-100">{{ auth()->user()->name }}</div>
                <div class="text-[11px] text-slate-400 capitalize">{{ auth()->user()->role?->name ?? 'Kasir' }}</div>
            </div>
            <a href="/admin" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded-lg border border-slate-700 font-medium transition">
                Admin Panel &rarr;
            </a>
        </div>
    </header>

    <!-- Main Split Screen -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Left Column: Catalog & Search (65%) -->
        <div class="w-full lg:w-[65%] flex flex-col border-r border-slate-200 bg-slate-50 flex-shrink-0">
            <!-- Search & Filters Bar -->
            <div class="p-3 bg-white border-b border-slate-200 space-y-2">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk / scan barcode..."
                        class="w-full pl-9 pr-8 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600 bg-white"
                        autofocus
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold">
                            &times;
                        </button>
                    @endif
                </div>

                <!-- Category Tabs (Matching Blueprint) -->
                <div class="flex items-center justify-between gap-2 overflow-x-auto text-xs pt-1">
                    <div class="flex items-center gap-1.5">
                        <button
                            wire:click="$set('selectedCategory', null)"
                            class="px-3 py-1 rounded-md font-semibold transition shrink-0 {{ $selectedCategory === null ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            Semua
                        </button>
                        @foreach($categories as $cat)
                            <button
                                wire:click="$set('selectedCategory', {{ $cat->id }})"
                                class="px-3 py-1 rounded-md font-semibold transition shrink-0 {{ $selectedCategory === $cat->id ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Type Filter -->
                    <div class="flex items-center gap-1 shrink-0 text-[11px]">
                        <button
                            wire:click="$set('selectedType', 'ALL')"
                            class="px-2 py-0.5 rounded font-medium border {{ $selectedType === 'ALL' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-300' }}"
                        >
                            Semua Tipe
                        </button>
                        <button
                            wire:click="$set('selectedType', 'PHYSICAL')"
                            class="px-2 py-0.5 rounded font-medium border {{ $selectedType === 'PHYSICAL' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-300' }}"
                        >
                            Fisik
                        </button>
                        <button
                            wire:click="$set('selectedType', 'DIGITAL')"
                            class="px-2 py-0.5 rounded font-medium border {{ $selectedType === 'DIGITAL' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-300' }}"
                        >
                            Digital
                        </button>
                        <button
                            wire:click="$set('selectedType', 'SERVICE')"
                            class="px-2 py-0.5 rounded font-medium border {{ $selectedType === 'SERVICE' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-600 border-slate-300' }}"
                        >
                            Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 overflow-y-auto p-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @forelse($products as $product)
                    @php
                        $isIncomplete = $product->price_status === 'INCOMPLETE';
                        $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->where('location_id', $location?->id)->first() : null;
                        $stockQty = $inv?->quantity ?? 0;
                        $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                    @endphp

                    <div
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white border border-slate-200 rounded-xl p-2.5 flex flex-col justify-between cursor-pointer hover:border-blue-600 transition relative {{ $isIncomplete ? 'opacity-60 bg-red-50/20' : '' }}"
                    >
                        <div>
                            <!-- Code & Type -->
                            <div class="flex items-center justify-between mb-1 text-[10px]">
                                <span class="font-mono text-slate-500 bg-slate-100 px-1 rounded">{{ $product->code }}</span>
                                <span class="font-bold uppercase text-slate-600">{{ $product->product_type }}</span>
                            </div>

                            <!-- Image & Name -->
                            <div class="flex items-center gap-2 mb-1.5">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-9 h-9 object-cover rounded bg-slate-100 shrink-0">
                                <div class="text-xs font-semibold text-slate-800 line-clamp-2 leading-tight">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock -->
                        <div class="mt-1.5 pt-1.5 border-t border-slate-100">
                            @if($isIncomplete)
                                <div class="text-[10px] font-bold text-red-600 bg-red-100 px-1 py-0.5 rounded text-center">
                                    HARGA INCOMPLETE
                                </div>
                            @else
                                <div class="text-xs font-bold text-blue-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($product->product_type === 'PHYSICAL')
                                <div class="flex items-center justify-between mt-1 text-[10px]">
                                    <span class="text-slate-500">Stok: {{ $stockQty }}</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold text-[9px] {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-red-100 text-red-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $stockStatus === 'OUT_OF_STOCK' ? 'HABIS' : ($stockStatus === 'LOW_STOCK' ? 'MENIPIS' : 'TERSEDIA') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                        Tidak ada produk ditemukan.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-2 border-t border-slate-200 bg-white">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Right Column: Cart & Checkout (35%) (Matching Blueprint) -->
        <div class="w-full lg:w-[35%] bg-white flex flex-col flex-shrink-0 shadow-md">

            <!-- Cart Header -->
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="font-bold text-xs text-slate-800 uppercase tracking-wider">
                    KERANJANG ({{ count($cart) }})
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-[11px] text-red-600 hover:underline font-medium">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100">
                @forelse($cart as $id => $item)
                    <div class="py-2 flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded border border-slate-200">
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-5 h-5 bg-white hover:bg-slate-200 font-bold text-xs rounded flex items-center justify-center text-slate-700"
                            >-</button>
                            <span class="w-5 text-center font-bold text-xs">{{ $item['quantity'] }}</span>
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-5 h-5 bg-white hover:bg-slate-200 font-bold text-xs rounded flex items-center justify-center text-slate-700"
                            >+</button>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-900 font-mono">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-red-500 hover:underline">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Keranjang kosong. Klik produk untuk memilih.
                    </div>
                @endforelse
            </div>

            <!-- Cart Summary & Multi-Payment Checkout -->
            <div class="p-3 border-t border-slate-200 bg-slate-50 space-y-2.5">
                <!-- Subtotal, Diskon, Total -->
                <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-semibold font-mono">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Diskon</span>
                        <span class="font-semibold font-mono">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 border-t pt-1.5">
                        <span>Total</span>
                        <span class="text-blue-600 font-mono text-base">Rp {{ number_format($this->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Cash Nominal Quick Shortcut Pills -->
                @if(count($cart) > 0)
                    <div class="flex items-center gap-1 overflow-x-auto text-[10px] font-semibold">
                        <span class="text-slate-500 shrink-0">Bayar:</span>
                        <button wire:click="setExactPayment" class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded shrink-0">Uang Pas</button>
                        <button wire:click="setPaymentAmount(10000)" class="px-2 py-0.5 bg-white border border-slate-200 rounded shrink-0">10rb</button>
                        <button wire:click="setPaymentAmount(20000)" class="px-2 py-0.5 bg-white border border-slate-200 rounded shrink-0">20rb</button>
                        <button wire:click="setPaymentAmount(50000)" class="px-2 py-0.5 bg-white border border-slate-200 rounded shrink-0">50rb</button>
                        <button wire:click="setPaymentAmount(100000)" class="px-2 py-0.5 bg-white border border-slate-200 rounded shrink-0">100rb</button>
                    </div>
                @endif

                <!-- Payment Methods Section -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span>Metode Pembayaran</span>
                        <button wire:click="addPaymentRow" class="text-blue-600 hover:underline text-[11px]">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-2 rounded-lg border border-slate-200 space-y-1 text-xs">
                            <div class="flex items-center gap-1.5">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-1 border border-slate-300 rounded bg-white text-xs">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <input
                                    type="number"
                                    wire:model.live="payments.{{ $index }}.amount"
                                    placeholder="Nominal"
                                    class="w-1/2 p-1 border border-slate-300 rounded font-mono font-bold text-right text-xs"
                                />

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-red-500 hover:text-red-700 font-bold px-1">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-1 border border-amber-200 rounded bg-amber-50 text-xs">
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

                <!-- Amount Paid & Change Calculation -->
                <div class="bg-white p-2.5 rounded-lg border border-slate-200 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Jumlah Bayar</span>
                        <span class="font-bold font-mono text-slate-900">Rp {{ number_format($this->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-900 border-t pt-1">
                        <span>Kembali</span>
                        <span class="{{ $this->change_amount > 0 ? 'text-emerald-600 font-mono text-base font-bold' : 'text-slate-900 font-mono' }}">
                            Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($cashBalance < 0)
                        <div class="text-[10px] bg-amber-50 text-amber-800 p-1.5 rounded border border-amber-200 mt-1">
                            ⚠️ Warning: Saldo Uang Fisik Kasir Minus (Rp {{ number_format(abs($cashBalance), 0, ',', '.') }}). Operasional dapat diganti dari rekening.
                        </div>
                    @endif
                </div>

                <!-- Checkout Action Button (Matching Blueprint 'BAYAR / Selesaikan Transaksi') -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-3 rounded-lg font-bold text-xs text-white uppercase tracking-wider transition {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-slate-300 text-slate-500 cursor-not-allowed' }}"
                >
                    Selesaikan Transaksi & Cetak Struk
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-5 max-w-sm w-full shadow-xl text-center space-y-3">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">
                    ✓
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="text-xs text-slate-500">Kembali</div>
                    <div class="text-xl font-bold text-emerald-600 font-mono">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-2 pt-1">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition"
                    >
                        🖨️ Cetak Struk Thermal
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
