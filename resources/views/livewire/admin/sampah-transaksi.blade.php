<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Transaksi Dibatalkan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Daftar riwayat transaksi yang dibatalkan oleh kasir/admin. Otomatis terhapus permanen setelah 30 hari.</p>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between text-sm">
        <div class="relative w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                data-shortcut-search
                placeholder="Cari No. TRX..."
                class="w-full h-11 pl-9 pr-12 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 bg-slate-50 text-slate-800 placeholder:text-slate-400 transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <div class="absolute right-3 top-3 hidden sm:flex items-center gap-0.5">
                <kbd class="px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-400 bg-white border border-slate-200 rounded shadow-2xs">⌘K</kbd>
            </div>
        </div>
    </div>

    <!-- Tabel Sampah Transaksi -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200/90 text-slate-500 uppercase text-xs font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-4">No. TRX &amp; Waktu Dibatalkan</th>
                        <th class="py-3.5 px-4">Kasir & Lokasi</th>
                        <th class="py-3.5 px-4 text-right">Total Nominal</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi Pemulihan</th>
                    </tr>
                </thead>
                <!-- Loading Skeleton State -->
                <x-table-skeleton :cols="5" :rows="3" wire:loading.delay />

                <!-- Data Table Body -->
                <tbody wire:loading.remove.delay class="divide-y divide-slate-100 font-medium">
                    @forelse($trashedSales as $sale)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-emerald-700 font-mono text-xs bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md inline-block">{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-slate-500 mt-1 font-semibold whitespace-nowrap">Dibatalkan: {{ $sale->updated_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-900 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-sm">{{ $sale->user?->name }}</div>
                                <div class="text-xs text-slate-500 font-semibold">{{ $sale->location?->name }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-rose-600 text-sm whitespace-nowrap">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-800 border border-rose-200/80 whitespace-nowrap inline-block">
                                    DIBATALKAN
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if(auth()->user()->can('sales.restore'))
                                    <button wire:click="restoreSale({{ $sale->id }})" wire:confirm="Pulihkan transaksi ini dari Sampah Transaksi? Stok akan dikurangi kembali dan saldo pembayaran akan dicatat ulang." class="h-9 px-3.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl font-extrabold text-sm transition uppercase tracking-wider shadow-2xs cursor-pointer whitespace-nowrap flex items-center justify-center">
                                        Pulihkan Transaksi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state
                                    icon="receipt"
                                    title="Sampah Transaksi Kosong"
                                    description="Tidak ada transaksi yang pernah dibatalkan atau kata kunci pencarian Anda tidak cocok."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200/90">
            {{ $trashedSales->links() }}
        </div>
    </div>
</div>
