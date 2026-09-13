<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs sm:text-sm text-[#718379] font-medium mt-0.5">Daftar seluruh transaksi penjualan toko, rincian pembayaran, dan cetak ulang struk kasir.</p>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 text-xs sm:text-sm">
        <div class="relative w-full">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. TRX / Kasir..."
                class="w-full h-10 sm:h-11 pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] text-[#2C3E35] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div>
            <select wire:model.live="paymentMethodId" class="w-full h-10 sm:h-11 py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold bg-[#F3F6F4] text-[#2C3E35] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer">
                <option value="">Semua Metode Pembayaran</option>
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
        </div>

        <div>
            <input
                type="date"
                wire:model.live="startDate"
                class="w-full h-10 sm:h-11 py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold bg-[#F3F6F4] text-[#2C3E35] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                placeholder="Dari Tanggal"
            />
        </div>

        <div>
            <input
                type="date"
                wire:model.live="endDate"
                class="w-full h-10 sm:h-11 py-2 px-3 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold bg-[#F3F6F4] text-[#2C3E35] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                placeholder="Sampai Tanggal"
            />
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th wire:click="sortBy('invoice_number')" class="py-3.5 px-4 cursor-pointer hover:text-[#3F7A5D] transition select-none">
                            <div class="flex items-center gap-1">
                                <span>No. TRX &amp; Waktu</span>
                                @if($sortField === 'invoice_number' || $sortField === 'created_at')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Kasir & Lokasi</th>
                        <th class="py-3.5 px-4">Metode Pembayaran</th>
                        <th wire:click="sortBy('grand_total')" class="py-3.5 px-4 text-right cursor-pointer hover:text-[#3F7A5D] transition select-none">
                            <div class="flex items-center justify-end gap-1">
                                <span>Total Transaksi</span>
                                @if($sortField === 'grand_total')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status')" class="py-3.5 px-4 text-center cursor-pointer hover:text-[#3F7A5D] transition select-none">
                            <div class="flex items-center justify-center gap-1">
                                <span>Status</span>
                                @if($sortField === 'status')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-[#3F7A5D] font-mono text-xs bg-[#F3F6F4] border border-slate-200/80 px-2.5 py-0.5 rounded-md inline-block">{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-[#718379] mt-1 font-semibold whitespace-nowrap">{{ $sale->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#2C3E35] whitespace-nowrap">
                                <div class="font-bold text-[#2C3E35]">{{ $sale->user?->name ?? 'Kasir' }}</div>
                                <div class="text-xs text-[#718379] font-semibold">{{ $sale->location?->name ?? 'Toko' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sale->payments as $p)
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-[#F3F6F4] text-[#2C3E35] border border-slate-200/80 inline-block whitespace-nowrap">
                                            {{ $p->paymentMethod?->name }}: Rp {{ number_format($p->amount, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#2C3E35] text-sm whitespace-nowrap">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider whitespace-nowrap inline-block {{ $sale->status === 'COMPLETED' ? 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20' : 'bg-amber-50 text-amber-800 border border-amber-200/80' }}">
                                    {{ $sale->status === 'COMPLETED' ? 'LUNAS' : $sale->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <button wire:click="openDetailModal({{ $sale->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#2C3E35] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm">
                                        Detail Transaksi
                                    </button>
                                    <button wire:click="openReceiptModal({{ $sale->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#2C3E35] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        <span>Struk</span>
                                    </button>
                                    @if(auth()->user()->can('sales.trash'))
                                        <button wire:click="moveToTrash({{ $sale->id }})" wire:confirm="Pindahkan transaksi ini ke Sampah Transaksi? Stok dan saldo akan dikembalikan." class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm">
                                            Ke Sampah
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">Belum ada riwayat transaksi penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 border-t border-[#E3EEE8]">
            {{ $sales->links('components.emco-pagination') }}
        </div>
    </div>

    <!-- Detail Snapshot Modal -->
    @if($showDetailModal && $selectedSale)
        <div class="fixed inset-0 bg-[#2C3E35]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl space-y-4 border border-slate-100">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-[#2C3E35]">Detail Transaksi</h3>
                        <div class="text-xs font-mono text-[#3F7A5D] font-bold bg-[#E3EEE8] border border-[#3F7A5D]/20 px-2.5 py-0.5 rounded-md inline-block mt-1">{{ $selectedSale->invoice_number }}</div>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- Info Box -->
                    <div class="bg-[#F3F6F4] p-3.5 rounded-xl border border-slate-200/80 space-y-1.5 font-medium">
                        <div class="flex justify-between text-[#718379]"><span>Waktu Transaksi:</span><span class="font-mono font-bold text-[#2C3E35]">{{ $selectedSale->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i:s') }}</span></div>
                        <div class="flex justify-between text-[#718379]"><span>Kasir:</span><span class="font-bold text-[#2C3E35]">{{ $selectedSale->user?->name ?? 'Kasir' }}</span></div>
                        <div class="flex justify-between text-[#718379]"><span>Lokasi Toko:</span><span class="font-bold text-[#2C3E35]">{{ $selectedSale->location?->name ?? 'Toko Utama' }}</span></div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-[#F3F6F4] text-[#718379] font-extrabold uppercase text-[11px] tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th class="p-3 text-left">Item Produk</th>
                                    <th class="p-3 text-center whitespace-nowrap">Qty</th>
                                    <th class="p-3 text-right whitespace-nowrap">Harga</th>
                                    <th class="p-3 text-right whitespace-nowrap">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($selectedSale->items as $item)
                                    <tr class="hover:bg-[#F3F6F4]/50 transition">
                                        <td class="p-3 font-semibold text-[#2C3E35]">{{ $item->product_name_snapshot }}</td>
                                        <td class="p-3 text-center font-mono font-bold whitespace-nowrap">{{ $item->quantity }}</td>
                                        <td class="p-3 text-right font-mono text-[#2C3E35] whitespace-nowrap">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                        <td class="p-3 text-right font-mono font-extrabold text-[#3F7A5D] whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Box -->
                    <div class="bg-[#F3F6F4] p-3.5 rounded-xl border border-slate-200/80 space-y-2 text-xs font-medium">
                        <div class="flex justify-between text-[#718379]">
                            <span>Subtotal:</span>
                            <span class="font-mono font-bold text-[#2C3E35]">Rp {{ number_format($selectedSale->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedSale->discount_amount > 0)
                            <div class="flex justify-between text-rose-600 font-semibold">
                                <span>Diskon:</span>
                                <span class="font-mono font-bold">-Rp {{ number_format($selectedSale->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-baseline pt-1 border-t border-slate-200/80">
                            <span class="font-extrabold text-[#2C3E35]">Grand Total:</span>
                            <span class="font-mono font-extrabold text-[#3F7A5D] text-base">Rp {{ number_format($selectedSale->total_amount, 0, ',', '.') }}</span>
                        </div>

                        <!-- Payment Methods Breakdown -->
                        <div class="pt-2 border-t border-slate-200/80 space-y-1 text-[11px]">
                            <div class="text-[#718379] font-bold uppercase tracking-wider">Rincian Pembayaran:</div>
                            @foreach($selectedSale->payments as $p)
                                <div class="flex justify-between font-mono">
                                    <span class="text-[#52645B]">{{ $p->paymentMethod?->name ?? 'Pembayaran' }}:</span>
                                    <span class="font-bold text-[#2C3E35]">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between text-emerald-700 font-bold font-mono pt-1">
                                <span>Kembalian:</span>
                                <span>Rp {{ number_format($selectedSale->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                    <button type="button" wire:click="openReceiptModal({{ $selectedSale->id }})" class="py-2.5 px-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider flex items-center gap-2 shadow-sm transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Lihat &amp; Cetak Struk</span>
                    </button>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="py-2.5 px-5 bg-slate-100 hover:bg-slate-200 text-[#2C3E35] font-bold rounded-2xl text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hidden Thermal Receipt Frame -->
    <iframe id="receipt-iframe-sales" class="hidden fixed -top-full -left-full w-0 h-0 border-0"></iframe>

    <!-- In-Page Thermal Receipt Pop-Up Modal -->
    @if($showReceiptModal && $receiptSale)
        @php
            $paperWidth = \App\Models\Setting::get('receipt_paper_width', '58mm');
            $printMode = \App\Models\Setting::get('print_mode', 'BROWSER');
            $storeName = \App\Models\Setting::get('store_name', 'Raja Aksesoris');
            $tagline = \App\Models\Setting::get('receipt_header_tagline', 'Retail Management System');
            $address = \App\Models\Setting::get('receipt_address', '');
            $phone = \App\Models\Setting::get('receipt_phone', '');
            $footerText = \App\Models\Setting::get('receipt_footer_text', 'Terima Kasih Telah Berbelanja!');
            $showCashier = \App\Models\Setting::get('show_cashier_name', '1') === '1';

            $receiptData = [
                'storeName' => $storeName,
                'tagline' => $tagline,
                'address' => $address,
                'phone' => $phone,
                'paperWidth' => $paperWidth,
                'invoiceNumber' => $receiptSale->invoice_number,
                'date' => ($receiptSale->transaction_date ?? $receiptSale->created_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
                'cashier' => $showCashier ? ($receiptSale->cashier?->name ?? $receiptSale->user?->name ?? 'Kasir') : '',
                'items' => $receiptSale->items->map(fn($it) => [
                    'name' => $it->product_name_snapshot,
                    'qty' => $it->quantity,
                    'price' => 'Rp '.number_format($it->selling_price, 0, ',', '.'),
                    'subtotal' => 'Rp '.number_format($it->subtotal, 0, ',', '.'),
                ])->values()->all(),
                'total' => 'Rp '.number_format($receiptSale->total_amount, 0, ',', '.'),
                'payments' => $receiptSale->payments->map(fn($p) => [
                    'method' => 'BAYAR ('.($p->paymentMethod?->name ?? 'Metode').')',
                    'amount' => 'Rp '.number_format($p->amount, 0, ',', '.'),
                ])->values()->all(),
                'change' => ($receiptSale->change_amount ?? 0) > 0 ? 'Rp '.number_format($receiptSale->change_amount, 0, ',', '.') : null,
                'footer' => $footerText,
            ];
        @endphp

        <div
            class="fixed inset-0 bg-[#2C3E35]/70 backdrop-blur-sm flex items-center justify-center z-[60] p-4 overflow-y-auto"
            x-data="{
                receiptJson: @js($receiptData),
                printWebBt() {
                    window.webBluetoothThermalPrinter?.printReceipt(this.receiptJson);
                },
                printThermal(saleId) {
                    if (!saleId) return;
                    let iframe = document.getElementById('receipt-iframe-sales');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'receipt-iframe-sales';
                        iframe.className = 'hidden fixed -top-full -left-full w-0 h-0 border-0';
                        document.body.appendChild(iframe);
                    }
                    iframe.src = '/receipt/thermal/' + saleId;
                    iframe.onload = function() {
                        try {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        } catch (e) {
                            console.error('Thermal print error:', e);
                        }
                    };
                }
            }"
        >
            <div class="bg-white rounded-2xl p-5 max-w-sm w-full shadow-2xl space-y-4 border border-slate-100 relative my-auto animate-fade-in">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-[#E3EEE8] text-[#3F7A5D] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-[#2C3E35]">Preview Struk Kasir</h3>
                            <p class="text-[11px] font-mono text-[#718379]">Kertas Thermal {{ $paperWidth }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showReceiptModal', false)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Receipt Paper Simulation Box -->
                <div class="bg-[#F3F6F4] p-3 rounded-xl border border-slate-200/80 max-h-[380px] overflow-y-auto">
                    @include('receipt._preview', ['sale' => $receiptSale])
                </div>

                <!-- Modal Actions Footer -->
                <div class="pt-2 space-y-2">
                    @if($printMode === 'WEB_BLUETOOTH')
                        <button
                            type="button"
                            @click="printWebBt()"
                            class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span>Cetak Direct (Web Bluetooth)</span>
                        </button>
                    @else
                        <button
                            type="button"
                            @click="printThermal({{ $receiptSale->id }})"
                            class="w-full py-2.5 px-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Cetak Struk Thermal ({{ $paperWidth }})</span>
                        </button>
                    @endif

                    <button
                        type="button"
                        onclick="window.location.href='intent:' + encodeURIComponent(window.location.origin + '/receipt/thermal/{{ $receiptSale->id }}') + '#Intent;scheme=http;package=ru.a256.rawbtprinter;end;'"
                        class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition text-center flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs"
                    >
                        <span>Cetak Direct (RawBT Bluetooth)</span>
                    </button>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <a href="/receipt/thermal/{{ $receiptSale->id }}" target="_blank" class="text-[#3F7A5D] hover:underline font-bold text-[11px] flex items-center gap-1">
                            <span>Buka versi cetak penuh</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <button type="button" wire:click="$set('showReceiptModal', false)" class="py-1.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#2C3E35] font-bold rounded-xl text-xs transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

