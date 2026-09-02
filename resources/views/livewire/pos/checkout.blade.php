<div class="h-screen flex flex-col overflow-hidden bg-slate-100">
    <!-- Topbar Header -->
    <header class="bg-navy-900 text-white px-4 py-3 flex items-center justify-between shadow-md z-10 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="bg-brand-gold text-navy-900 font-bold px-3 py-1 rounded text-sm tracking-wider">RAJA POS</div>
            <div class="text-xs text-slate-300 hidden sm:block">
                <span class="font-semibold text-white">{{ $location?->name ?? 'Raja Aksesoris Bango' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="text-right">
                <div class="font-medium text-slate-200">{{ auth()->user()->name }}</div>
                <div class="text-slate-400 capitalize">{{ auth()->user()->role?->name ?? 'Kasir' }}</div>
            </div>
            <a href="/admin" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 rounded border border-slate-700 font-medium transition">
                Admin Panel &rarr;
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Left Column: Product Catalog (65%) -->
        <div class="w-full lg:w-[65%] flex flex-col border-r border-slate-200 bg-slate-50 flex-shrink-0">
            <!-- Search & Filters -->
            <div class="p-3 bg-white border-b border-slate-200 space-y-2">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari kode produk / SKU / Barcode / Nama produk..."
                            class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-brand-blue bg-slate-50"
                            autofocus
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Type Filters & Category Select -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button
                        wire:click="$set('selectedType', 'ALL')"
                        class="px-3 py-1 rounded-md font-medium transition shrink-0 {{ $selectedType === 'ALL' ? 'bg-navy-900 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}"
                    >
                        Semua
                    </button>
                    <button
                        wire:click="$set('selectedType', 'PHYSICAL')"
                        class="px-3 py-1 rounded-md font-medium transition shrink-0 {{ $selectedType === 'PHYSICAL' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}"
                    >
                        Fisik
                    </button>
                    <button
                        wire:click="$set('selectedType', 'DIGITAL')"
                        class="px-3 py-1 rounded-md font-medium transition shrink-0 {{ $selectedType === 'DIGITAL' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}"
                    >
                        Digital
                    </button>
                    <button
                        wire:click="$set('selectedType', 'SERVICE')"
                        class="px-3 py-1 rounded-md font-medium transition shrink-0 {{ $selectedType === 'SERVICE' ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}"
                    >
                        Service
                    </button>

                    <div class="ml-auto shrink-0">
                        <select wire:model.live="selectedCategory" class="px-2 py-1 border border-slate-300 rounded-md text-xs bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
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
                        class="bg-white border rounded-xl p-2.5 flex flex-col justify-between cursor-pointer hover:shadow-md hover:border-brand-blue transition relative group {{ $isIncomplete ? 'opacity-75 border-red-300 bg-red-50/20' : 'border-slate-200' }}"
                    >
                        <div>
                            <!-- Header Info -->
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-mono bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">
                                    {{ $product->code }}
                                </span>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded {{ $product->product_type === 'PHYSICAL' ? 'bg-blue-50 text-blue-700' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $product->product_type }}
                                </span>
                            </div>

                            <!-- Image & Name -->
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-lg bg-slate-100 shrink-0">
                                <div class="text-xs font-semibold text-slate-800 line-clamp-2 leading-tight">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock Status -->
                        <div class="mt-2 pt-2 border-t border-slate-100">
                            @if($isIncomplete)
                                <div class="text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded text-center mb-1">
                                    HARGA INCOMPLETE
                                </div>
                            @else
                                <div class="text-xs font-bold text-brand-blue">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                            @endif

                            @if($product->product_type === 'PHYSICAL')
                                <div class="flex items-center justify-between mt-1 text-[10px]">
                                    <span class="text-slate-500">Stok: {{ $stockQty }}</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-red-100 text-red-700' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $stockStatus === 'OUT_OF_STOCK' ? 'HABIS' : ($stockStatus === 'LOW_STOCK' ? 'MENIPIS' : 'ADA') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        No products found.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="p-2 border-t border-slate-200 bg-white">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Right Column: Cart & Multi-Payment Panel (35%) -->
        <div class="w-full lg:w-[35%] bg-white flex flex-col flex-shrink-0 shadow-lg z-10">

            <!-- Cart Header -->
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="font-bold text-sm text-navy-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                    </svg>
                    Keranjang Belanja
                    <span class="bg-blue-100 text-brand-blue text-xs font-semibold px-2 py-0.5 rounded-full">
                        {{ count($cart) }} Item
                    </span>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-red-600 hover:text-red-800 font-medium">
                        Kosongkan
                    </button>
                @endif
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-3 divide-y divide-slate-100">
                @forelse($cart as $id => $item)
                    <div class="py-2.5 flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-[11px] text-slate-500">
                                {{ $item['code'] }} &bull; Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-1.5">
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 font-bold text-xs flex items-center justify-center text-slate-700"
                            >-</button>
                            <span class="w-6 text-center font-bold text-xs">{{ $item['quantity'] }}</span>
                            <button
                                wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 font-bold text-xs flex items-center justify-center text-slate-700"
                            >+</button>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-900">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-red-500 hover:text-red-700">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        Keranjang masih kosong. Klik produk di katalog untuk menambahkan.
                    </div>
                @endforelse
            </div>

            <!-- Summary & Payment Section -->
            <div class="p-3 border-t border-slate-200 bg-slate-50 space-y-3">
                <!-- Grand Total Display -->
                <div class="bg-navy-900 text-white p-3 rounded-xl flex items-center justify-between shadow">
                    <div>
                        <div class="text-[10px] text-slate-300 uppercase tracking-wider">Total Belanja</div>
                        <div class="text-xl font-bold text-brand-gold">
                            Rp {{ number_format($this->grand_total, 0, ',', '.') }}
                        </div>
                    </div>
                    @if(count($cart) > 0)
                        <button
                            wire:click="setExactPayment"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition"
                        >
                            Uang Pas
                        </button>
                    @endif
                </div>

                <!-- Payment Breakdown (Multi-Payment Split) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span>Pembayaran (Payment Split)</span>
                        <button wire:click="addPaymentRow" class="text-brand-blue hover:underline text-[11px]">
                            + Tambah Metode
                        </button>
                    </div>

                    @foreach($payments as $index => $pay)
                        @php
                            $selectedPm = $paymentMethods->firstWhere('id', $pay['payment_method_id']);
                        @endphp
                        <div class="bg-white p-2 rounded-lg border border-slate-200 space-y-1.5 text-xs">
                            <div class="flex items-center gap-1.5">
                                <select wire:model.live="payments.{{ $index }}.payment_method_id" class="w-1/2 p-1.5 border border-slate-300 rounded bg-white text-xs">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                                    @endforeach
                                </select>

                                <input
                                    type="number"
                                    wire:model.live="payments.{{ $index }}.amount"
                                    placeholder="Nominal"
                                    class="w-1/2 p-1.5 border border-slate-300 rounded font-bold text-right text-xs"
                                />

                                @if(count($payments) > 1)
                                    <button wire:click="removePaymentRow({{ $index }})" class="text-red-500 hover:text-red-700 font-bold px-1">
                                        &times;
                                    </button>
                                @endif
                            </div>

                            <!-- Account Destination for Transfer / E-Wallet -->
                            @if($selectedPm && in_array($selectedPm->type, ['TRANSFER', 'E_WALLET']))
                                <div>
                                    <select wire:model="payments.{{ $index }}.balance_account_id" class="w-full p-1 border border-slate-300 rounded bg-amber-50 text-xs">
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

                <!-- Change Calculation & Cash Minus Warning -->
                <div class="bg-white p-2.5 rounded-lg border border-slate-200 space-y-1 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Total Dibayar:</span>
                        <span class="font-bold">Rp {{ number_format($this->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-bold border-t pt-1">
                        <span class="text-slate-800">Kembalian:</span>
                        <span class="{{ $this->change_amount > 0 ? 'text-emerald-600' : 'text-slate-800' }}">
                            Rp {{ number_format($this->change_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    @if($cashBalance < 0)
                        <div class="text-[10px] bg-amber-100 text-amber-800 p-1.5 rounded border border-amber-200 mt-1">
                            ⚠️ Saldo Cash Minus (Rp {{ number_format(abs($cashBalance), 0, ',', '.') }}). Operasional dapat diganti dari bank/e-wallet.
                        </div>
                    @endif
                </div>

                <!-- Process Checkout Button -->
                <button
                    wire:click="processCheckout"
                    @if(count($cart) === 0 || $this->total_paid < $this->grand_total) disabled @endif
                    class="w-full py-3 rounded-xl font-bold text-sm text-white shadow-md transition {{ count($cart) > 0 && $this->total_paid >= $this->grand_total ? 'bg-emerald-600 hover:bg-emerald-500 cursor-pointer' : 'bg-slate-400 cursor-not-allowed' }}"
                >
                    PROSES BAYAR & CETAK STRUK &rarr;
                </button>
            </div>

        </div>

    </div>

    <!-- Success Modal with Print Action -->
    @if($showSuccessModal)
        <div class="fixed inset-0 bg-navy-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">
                    ✓
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-500 font-mono mt-1">{{ $completedInvoiceNumber }}</p>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="text-xs text-slate-500">Kembalian Kasir</div>
                    <div class="text-2xl font-bold text-emerald-600">
                        Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a
                        href="/receipt/thermal/{{ $completedSaleId }}"
                        target="_blank"
                        class="w-full py-2.5 bg-brand-blue hover:bg-blue-600 text-white font-bold rounded-lg text-xs transition"
                    >
                        🖨️ CETAK STRUK THERMAL
                    </a>
                    <button
                        wire:click="closeSuccessModal"
                        class="w-full py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-xs transition"
                    >
                        Selesai / Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
