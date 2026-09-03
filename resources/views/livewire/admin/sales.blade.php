<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Daftar seluruh transaksi penjualan toko, rincian pembayaran, dan cetak ulang struk kasir.</p>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
        <div class="relative sm:col-span-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. TRX / Kasir..."
                class="w-full pl-9 pr-3 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] text-[#232E28] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div>
            <select wire:model.live="paymentMethodId" class="w-full py-2.5 px-3 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                <option value="">-- Semua Metode Pembayaran --</option>
                @foreach($paymentMethods as $pm)
                    <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->type }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <input
                type="date"
                wire:model.live="startDate"
                class="w-full py-2.5 px-3 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                placeholder="Dari Tanggal"
            />
        </div>

        <div>
            <input
                type="date"
                wire:model.live="endDate"
                class="w-full py-2.5 px-3 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                placeholder="Sampai Tanggal"
            />
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap">
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
                                <div class="text-xs text-[#718379] mt-1 font-semibold whitespace-nowrap">{{ $sale->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#232E28] whitespace-nowrap">
                                <div class="font-bold text-[#232E28]">{{ $sale->user?->name ?? 'Kasir' }}</div>
                                <div class="text-xs text-[#718379] font-semibold">{{ $sale->location?->name ?? 'Toko' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sale->payments as $p)
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-[#F3F6F4] text-[#232E28] border border-slate-200/80 inline-block whitespace-nowrap">
                                            {{ $p->paymentMethod?->name }}: Rp {{ number_format($p->amount, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#232E28] text-sm whitespace-nowrap">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider whitespace-nowrap inline-block {{ $sale->status === 'COMPLETED' ? 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20' : 'bg-amber-50 text-amber-800 border border-amber-200/80' }}">
                                    {{ $sale->status === 'COMPLETED' ? 'LUNAS' : $sale->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <button wire:click="openDetailModal({{ $sale->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#232E28] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm">
                                        Detail Transaksi
                                    </button>
                                    <button wire:click="openReceiptModal({{ $sale->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#232E28] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm flex items-center gap-1">
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
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl space-y-4 border border-slate-100">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-[#232E28]">Detail Transaksi</h3>
                        <div class="text-xs font-mono text-[#3F7A5D] font-bold bg-[#E3EEE8] border border-[#3F7A5D]/20 px-2.5 py-0.5 rounded-md inline-block mt-1">{{ $selectedSale->invoice_number }}</div>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- Info Box -->
                    <div class="bg-[#F3F6F4] p-3.5 rounded-xl border border-slate-200/80 space-y-1.5 font-medium">
                        <div class="flex justify-between text-[#718379]"><span>Waktu Transaksi:</span><span class="font-mono font-bold text-[#232E28]">{{ $selectedSale->created_at->format('d F Y, H:i:s') }}</span></div>
                        <div class="flex justify-between text-[#718379]"><span>Kasir:</span><span class="font-bold text-[#232E28]">{{ $selectedSale->user?->name ?? 'Kasir' }}</span></div>
                        <div class="flex justify-between text-[#718379]"><span>Lokasi Toko:</span><span class="font-bold text-[#232E28]">{{ $selectedSale->location?->name ?? 'Toko Utama' }}</span></div>
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
                                        <td class="p-3 font-semibold text-[#232E28]">{{ $item->product_name_snapshot }}</td>
                                        <td class="p-3 text-center font-mono font-bold whitespace-nowrap">{{ $item->quantity }}</td>
                                        <td class="p-3 text-right font-mono text-[#232E28] whitespace-nowrap">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
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
                            <span class="font-mono font-bold text-[#232E28]">Rp {{ number_format($selectedSale->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedSale->discount_amount > 0)
                            <div class="flex justify-between text-rose-600 font-semibold">
                                <span>Diskon:</span>
                                <span class="font-mono font-bold">-Rp {{ number_format($selectedSale->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-baseline pt-1 border-t border-slate-200/80">
                            <span class="font-extrabold text-[#232E28]">Grand Total:</span>
                            <span class="font-mono font-extrabold text-[#3F7A5D] text-base">Rp {{ number_format($selectedSale->total_amount, 0, ',', '.') }}</span>
                        </div>

                        <!-- Payment Methods Breakdown -->
                        <div class="pt-2 border-t border-slate-200/80 space-y-1 text-[11px]">
                            <div class="text-[#718379] font-bold uppercase tracking-wider">Rincian Pembayaran:</div>
                            @foreach($selectedSale->payments as $p)
                                <div class="flex justify-between font-mono">
                                    <span class="text-[#52645B]">{{ $p->paymentMethod?->name ?? 'Pembayaran' }}:</span>
                                    <span class="font-bold text-[#232E28]">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
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
                    <button type="button" wire:click="$set('showDetailModal', false)" class="py-2.5 px-5 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- In-Page Thermal Receipt Pop-Up Modal -->
    @if($showReceiptModal && $receiptSale)
        <div
            class="fixed inset-0 bg-[#232E28]/70 backdrop-blur-sm flex items-center justify-center z-[60] p-4 overflow-y-auto"
            x-data="{
                printReceipt() {
                    const printContents = document.getElementById('printable-receipt-content').innerHTML;
                    const printWindow = window.open('', '_blank', 'width=400,height=600');
                    printWindow.document.write(`
                        <html>
                            <head>
                                <title>Struk #${ '{{ $receiptSale->invoice_number }}' }</title>
                                <style>
                                    @page { margin: 0; }
                                    body {
                                        font-family: 'Courier New', Courier, monospace;
                                        font-size: 12px;
                                        color: #000;
                                        background: #fff;
                                        margin: 0;
                                        padding: 10px;
                                        width: 58mm;
                                    }
                                    .text-center { text-align: center; }
                                    .text-right { text-align: right; }
                                    .text-left { text-align: left; }
                                    .bold { font-weight: bold; }
                                    .divider { border-top: 1px dashed #000; margin: 8px 0; }
                                    .table-items { width: 100%; border-collapse: collapse; }
                                    .table-items td { padding: 2px 0; vertical-align: top; }
                                    .totals-table { width: 100%; margin-top: 5px; }
                                    .totals-table td { padding: 2px 0; }
                                    .footer { margin-top: 15px; font-size: 11px; }
                                </style>
                            </head>
                            <body onload='window.print(); setTimeout(function(){ window.close(); }, 500);'>
                                ${printContents}
                            </body>
                        </html>
                    `);
                    printWindow.document.close();
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
                            <h3 class="text-sm font-extrabold text-[#232E28]">Preview Struk Kasir</h3>
                            <p class="text-[11px] font-mono text-[#718379]">Kertas Thermal 58mm</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showReceiptModal', false)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Receipt Paper Simulation Box -->
                <div class="bg-[#F3F6F4] p-3 rounded-xl border border-slate-200/80 max-h-[380px] overflow-y-auto">
                    <div id="printable-receipt-content" class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 font-mono text-xs text-black leading-relaxed space-y-2 select-text">
                        <div class="text-center font-bold text-sm uppercase tracking-wide">RAJA AKSESORIS</div>
                        <div class="text-center text-[10px] text-slate-600">Retail Management System</div>

                        <div class="border-t border-dashed border-black my-2"></div>

                        <div class="text-[11px] space-y-0.5">
                            <div><strong>No:</strong> {{ $receiptSale->invoice_number }}</div>
                            <div><strong>Tgl:</strong> {{ $receiptSale->created_at->format('d/m/Y H:i') }}</div>
                            <div><strong>Kasir:</strong> {{ $receiptSale->user?->name ?? 'Kasir' }}</div>
                        </div>

                        <div class="border-t border-dashed border-black my-2"></div>

                        <!-- Table Items -->
                        <table class="w-full text-xs text-left">
                            @foreach($receiptSale->items as $item)
                                <tr>
                                    <td colspan="2" class="font-bold pt-1">{{ $item->product_name_snapshot }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left text-[11px] text-slate-700">{{ $item->quantity }} x Rp{{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                    <td class="text-right font-bold whitespace-nowrap">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="border-t border-dashed border-black my-2"></div>

                        <!-- Totals -->
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between font-bold text-sm">
                                <span>TOTAL</span>
                                <span>Rp{{ number_format($receiptSale->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @foreach($receiptSale->payments as $payment)
                                <div class="flex justify-between text-[11px]">
                                    <span>BAYAR ({{ $payment->paymentMethod?->name ?? 'Metode' }})</span>
                                    <span>Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between text-[11px]">
                                <span>KEMBALI</span>
                                <span>Rp{{ number_format($receiptSale->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-black my-2"></div>

                        <div class="text-center text-[10px] text-slate-600 pt-1 space-y-0.5">
                            <div class="font-bold">Terima Kasih Telah Berbelanja!</div>
                            <div>Kepuasan Anda Adalah Kebanggaan Kami.</div>
                            <div>Sampai Jumpa Kembali di Raja Aksesoris!</div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions Footer -->
                <div class="pt-2 space-y-2">
                    <button
                        type="button"
                        @click="printReceipt()"
                        class="w-full py-2.5 px-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak Struk Sekarang</span>
                    </button>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <a href="/receipt/thermal/{{ $receiptSale->id }}" target="_blank" class="text-[#3F7A5D] hover:underline font-bold text-[11px] flex items-center gap-1">
                            <span>Buka versi cetak penuh</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <button type="button" wire:click="$set('showReceiptModal', false)" class="py-1.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-xl text-xs transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

