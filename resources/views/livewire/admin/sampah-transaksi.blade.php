<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Sampah Transaksi Penjualan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Daftar transaksi yang dibatalkan kasir/admin. Transaksi otomatis dihapus permanen setelah 30 hari retention.</p>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between text-xs">
        <div class="w-full sm:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Invoice..."
                class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-xs focus:ring-1 focus:ring-blue-600 focus:border-blue-600 bg-slate-50/60"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Trashed Sales Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold">
                    <tr>
                        <th class="py-3 px-4">No. Invoice & Waktu Dibatalkan</th>
                        <th class="py-3 px-4">Kasir & Lokasi</th>
                        <th class="py-3 px-4 text-right">Total Nominal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Pemulihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($trashedSales as $sale)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 font-mono text-xs">{{ $sale->invoice_number }}</div>
                                <div class="text-[10px] text-slate-500">Dibatalkan: {{ $sale->updated_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <div class="font-semibold">{{ $sale->user?->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $sale->location?->name }}</div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 text-sm">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    DIBATALKAN (TRASHED)
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if(auth()->user()->can('sales.restore'))
                                    <button wire:click="restoreSale({{ $sale->id }})" wire:confirm="Pulihkan transaksi ini dari Sampah Transaksi? Stok dan saldo akan dipotongan ulang." class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition">
                                        Pulihkan Transaksi 🔄
                                    </button>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">[Akses Terbatas]</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Sampah transaksi kosong. Tidak ada nota yang dibatalkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-200">
            {{ $trashedSales->links() }}
        </div>
    </div>
</div>
