<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Pergerakan Stok</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Riwayat lengkap keluar-masuk stok barang (Penjualan, Opname, Penyesuaian, dan Pemulihan Transaksi).</p>
        </div>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <div class="w-full md:w-80 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama barang, barcode, tipe..."
                class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] text-[#232E28] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <select wire:model.live="movementType" class="w-full md:w-auto px-3 py-2 border border-slate-200 rounded-xl bg-white font-bold text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
            <option value="ALL">Semua Tipe Pergerakan</option>
            @foreach($movementTypes as $type)
                <option value="{{ $type }}">
                    {{ $type === 'SALE' ? 'Penjualan Kasir' : ($type === 'STOCK_OPNAME' ? 'Stock Opname' : ($type === 'ADJUSTMENT' ? 'Penyesuaian Manual' : ($type === 'TRASH_RESTORE' ? 'Pemulihan Transaksi' : ($type === 'DAMAGE' ? 'Barang Rusak / Hilang' : $type)))) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Nama Barang & Lokasi</th>
                        <th class="py-3.5 px-4">Tipe Pergerakan</th>
                        <th class="py-3.5 px-4 text-center">Sebelum</th>
                        <th class="py-3.5 px-4 text-center">Perubahan</th>
                        <th class="py-3.5 px-4 text-center">Sesudah</th>
                        <th class="py-3.5 px-4">No. Referensi / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($movements as $movement)
                        @php
                            $typeLabel = match($movement->movement_type) {
                                'SALE' => 'Penjualan Kasir',
                                'STOCK_OPNAME' => 'Stock Opname',
                                'ADJUSTMENT' => 'Penyesuaian Manual',
                                'TRASH_RESTORE' => 'Pemulihan Transaksi',
                                'DAMAGE' => 'Barang Rusak / Hilang',
                                default => $movement->movement_type,
                            };
                        @endphp
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 text-[#718379] font-semibold whitespace-nowrap">{{ $movement->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#232E28] text-sm">{{ $movement->product?->name ?? '-' }}</div>
                                <div class="text-xs text-[#718379] font-mono mt-0.5">Barcode: {{ $movement->product?->effective_barcode ?? '-' }} &bull; {{ $movement->location?->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 font-bold font-sans text-[11px] whitespace-nowrap inline-block">{{ $typeLabel }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold whitespace-nowrap">{{ $movement->quantity_before }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold whitespace-nowrap {{ $movement->quantity_change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold whitespace-nowrap">{{ $movement->quantity_after }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($movement->notes)
                                    <span class="px-2.5 py-1 rounded-md bg-[#F3F6F4] text-[#232E28] font-mono text-[11px] font-bold border border-slate-200/80 inline-block whitespace-nowrap">
                                        {{ $movement->notes }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-semibold">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Belum ada pergerakan stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-[#E3EEE8]">{{ $movements->links() }}</div>
    </div>
</div>
