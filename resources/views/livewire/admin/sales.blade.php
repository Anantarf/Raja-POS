<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-[#566A7F] tracking-tight">Riwayat Transaksi Penjualan</h1>
            <p class="text-xs text-[#A1ACB8] font-medium mt-0.5">Daftar seluruh nota penjualan toko, rincian pembayaran, dan struk kasir.</p>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sneat flex items-center justify-between text-xs">
        <div class="w-full sm:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Invoice / Nama Kasir..."
                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] bg-[#F5F5F9] placeholder:text-[#A1ACB8]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sneat overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F5F5F9] border-b border-slate-100 text-[#A1ACB8] uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No. Invoice & Waktu</th>
                        <th class="py-3.5 px-4">Kasir & Lokasi</th>
                        <th class="py-3.5 px-4">Metode Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Total Transaksi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-[#F5F5F9]/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#696CFF] font-mono text-xs bg-[#E7E7FF] px-2 py-0.5 rounded inline-block">{{ $sale->invoice_number }}</div>
                                <div class="text-[10px] text-[#A1ACB8] mt-1 font-semibold">{{ $sale->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#566A7F]">
                                <div class="font-bold text-[#566A7F]">{{ $sale->user?->name ?? 'Kasir' }}</div>
                                <div class="text-[10px] text-[#A1ACB8] font-semibold">{{ $sale->location?->name ?? 'Toko' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sale->payments as $p)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F5F5F9] text-[#566A7F] border border-slate-200">
                                            {{ $p->paymentMethod?->name }}: Rp {{ number_format($p->amount, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#696CFF] text-sm">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                    {{ $sale->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openDetailModal({{ $sale->id }})" class="px-3 py-1.5 bg-[#F5F5F9] hover:bg-slate-200 text-[#566A7F] rounded-lg text-xs font-semibold transition">
                                        Detail Nota
                                    </button>
                                    <a href="/receipt/thermal/{{ $sale->id }}" target="_blank" class="px-3 py-1.5 bg-[#E7E7FF] hover:bg-indigo-100 text-[#696CFF] rounded-lg text-xs font-semibold transition">
                                        Struk 🖨️
                                    </a>
                                    @if(auth()->user()->can('sales.trash'))
                                        <button wire:click="moveToTrash({{ $sale->id }})" wire:confirm="Pindahkan transaksi ini ke Sampah Transaksi? Stok dan saldo akan dikembalikan." class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition">
                                            Ke Sampah 🗑️
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

        <div class="p-3.5 border-t border-slate-100">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Detail Snapshot Modal -->
    @if($showDetailModal && $selectedSale)
        <div class="fixed inset-0 bg-[#232333]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-[#566A7F]">Detail Transaksi Nota</h3>
                        <p class="text-xs font-mono text-[#696CFF] font-bold mt-0.5">{{ $selectedSale->invoice_number }}</p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="bg-[#F5F5F9] p-3.5 rounded-2xl border border-slate-200/80 space-y-1.5">
                        <div class="flex justify-between text-[#697A8D]"><span>Waktu Transaksi:</span><span class="font-bold text-[#566A7F]">{{ $selectedSale->created_at->format('d F Y, H:i:s') }}</span></div>
                        <div class="flex justify-between text-[#697A8D]"><span>Kasir:</span><span class="font-bold text-[#566A7F]">{{ $selectedSale->user?->name }}</span></div>
                        <div class="flex justify-between text-[#697A8D]"><span>Lokasi Toko:</span><span class="font-bold text-[#566A7F]">{{ $selectedSale->location?->name }}</span></div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-slate-100 rounded-2xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-[#F5F5F9] text-[#A1ACB8] font-bold border-b">
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
                                        <td class="p-2.5 font-semibold text-[#566A7F]">{{ $item->product_name_snapshot }}</td>
                                        <td class="p-2.5 text-center font-mono font-bold">{{ $item->quantity }}</td>
                                        <td class="p-2.5 text-right font-mono">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                        <td class="p-2.5 text-right font-mono font-bold text-[#696CFF]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-[#E7E7FF] p-3.5 rounded-2xl text-[#566A7F] space-y-1.5 font-mono text-xs border border-[#696CFF]/20">
                        <div class="flex justify-between items-baseline"><span>Grand Total:</span><span class="font-extrabold text-[#696CFF] text-base">Rp {{ number_format($selectedSale->grand_total, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Total Dibayar:</span><span class="font-bold">Rp {{ number_format($selectedSale->paid_amount, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-emerald-600 font-bold"><span>Kembalian:</span><span>Rp {{ number_format($selectedSale->change_amount, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button wire:click="$set('showDetailModal', false)" class="py-2.5 px-5 bg-slate-100 hover:bg-slate-200 text-[#566A7F] font-bold rounded-xl text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
