<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Sampah Transaksi Penjualan</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Daftar transaksi yang dibatalkan kasir/admin. Transaksi otomatis diarsipkan setelah 30 hari retensi.</p>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between text-xs">
        <div class="w-full sm:w-72 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Nota..."
                class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Tabel Sampah Transaksi -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-4">No. Nota & Waktu Dibatalkan</th>
                        <th class="py-3.5 px-4">Kasir & Lokasi</th>
                        <th class="py-3.5 px-4 text-right">Total Nominal</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi Pemulihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($trashedSales as $sale)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-[#3F7A5D] font-mono text-xs bg-[#F3F6F4] border border-slate-200/80 px-2.5 py-0.5 rounded-md inline-block">{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-[#718379] mt-1 font-semibold whitespace-nowrap">Dibatalkan: {{ $sale->updated_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-[#232E28] whitespace-nowrap">
                                <div class="font-bold text-[#232E28]">{{ $sale->user?->name }}</div>
                                <div class="text-xs text-[#718379] font-semibold">{{ $sale->location?->name }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-rose-600 text-sm whitespace-nowrap">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider bg-rose-50 text-rose-800 border border-rose-200/80 whitespace-nowrap inline-block">
                                    DIBATALKAN
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if(auth()->user()->can('sales.restore'))
                                    <button wire:click="restoreSale({{ $sale->id }})" wire:confirm="Pulihkan transaksi ini dari Sampah Transaksi? Stok dan saldo akan dipotongan ulang." class="px-3.5 py-1.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white rounded-xl font-extrabold text-xs transition uppercase tracking-wider shadow-sm cursor-pointer whitespace-nowrap">
                                        Pulihkan Transaksi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-medium">Sampah transaksi kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#E3EEE8]">
            {{ $trashedSales->links() }}
        </div>
    </div>
</div>

