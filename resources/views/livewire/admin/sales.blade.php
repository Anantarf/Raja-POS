<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Riwayat Transaksi Penjualan</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Daftar seluruh nota penjualan toko, rincian pembayaran, dan struk kasir.</p>
        </div>
    </div>

    <!-- KPI Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#E3EEE8] flex items-center justify-center text-[#3F7A5D] shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-[#718379] uppercase tracking-wider">Total Omzet Penjualan</div>
                <div class="text-base font-extrabold font-mono text-[#232E28] mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-[#718379] uppercase tracking-wider">Jumlah Transaksi (Lunas)</div>
                <div class="text-base font-extrabold font-mono text-[#232E28] mt-0.5">{{ number_format($totalTransactions, 0, ',', '.') }} Nota</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-[#718379] uppercase tracking-wider">Rata-rata Per Transaksi</div>
                <div class="text-base font-extrabold font-mono text-[#232E28] mt-0.5">Rp {{ number_format($averageBasket, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="relative sm:col-span-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Nota / Kasir..."
                class="w-full pl-9 pr-3 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] text-[#232E28] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div>
            <input
                type="date"
                wire:model.live="startDate"
                class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
                placeholder="Dari Tanggal"
            />
        </div>

        <div>
            <input
                type="date"
                wire:model.live="endDate"
                class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]"
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
                        <th class="py-3.5 px-4">No. Nota & Waktu</th>
                        <th class="py-3.5 px-4">Kasir & Lokasi</th>
                        <th class="py-3.5 px-4">Metode Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Total Transaksi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
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
                                        Detail Nota
                                    </button>
                                    <a href="/receipt/thermal/{{ $sale->id }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#232E28] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm">
                                        Struk
                                    </a>
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
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Detail Snapshot Modal -->
    @if($showDetailModal && $selectedSale)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-[#232E28]">Detail Transaksi Nota</h3>
                        <p class="text-xs font-mono text-[#3F7A5D] font-bold mt-0.5">{{ $selectedSale->invoice_number }}</p>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="bg-[#F3F6F4] p-3.5 rounded-2xl border border-slate-200/80 space-y-1.5">
                        <div class="flex justify-between text-[#52645B]"><span>Waktu Transaksi:</span><span class="font-bold text-[#232E28]">{{ $selectedSale->created_at->format('d F Y, H:i:s') }}</span></div>
                        <div class="flex justify-between text-[#52645B]"><span>Kasir:</span><span class="font-bold text-[#232E28]">{{ $selectedSale->user?->name }}</span></div>
                        <div class="flex justify-between text-[#52645B]"><span>Lokasi Toko:</span><span class="font-bold text-[#232E28]">{{ $selectedSale->location?->name }}</span></div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-[#E3EEE8] rounded-2xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-[#F3F6F4] text-[#86968E] font-bold border-b">
                                <tr>
                                    <th class="p-2.5 text-left">Item Produk</th>
                                    <th class="p-2.5 text-center">Qty</th>
                                    <th class="p-2.5 text-right">Harga</th>
                                    <th class="p-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($selectedSale->items as $item)
                                    <tr>
                                        <td class="p-2.5 font-semibold text-[#232E28]">{{ $item->product_name_snapshot }}</td>
                                        <td class="p-2.5 text-center font-mono font-bold">{{ $item->quantity }}</td>
                                        <td class="p-2.5 text-right font-mono">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                        <td class="p-2.5 text-right font-mono font-bold text-[#3F7A5D]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-[#E3EEE8] p-3.5 rounded-2xl text-[#232E28] space-y-1.5 font-mono text-xs border border-[#3F7A5D]/20">
                        <div class="flex justify-between items-baseline"><span>Grand Total:</span><span class="font-extrabold text-[#3F7A5D] text-base">Rp {{ number_format($selectedSale->grand_total, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Total Dibayar:</span><span class="font-bold">Rp {{ number_format($selectedSale->paid_amount, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-emerald-700 font-bold"><span>Kembalian:</span><span>Rp {{ number_format($selectedSale->change_amount, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                    <a href="/receipt/thermal/{{ $selectedSale->id }}" target="_blank" class="py-2 px-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak Struk Thermal</span>
                    </a>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

