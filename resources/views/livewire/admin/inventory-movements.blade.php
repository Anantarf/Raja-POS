<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Stok Masuk / Keluar</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Riwayat lengkap pergerakan keluar-masuk barang (Penjualan, Opname, Penyesuaian, dan Pemulihan Transaksi).</p>
        </div>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
        <div class="w-full md:w-80 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama barang, barcode, tipe..."
                class="w-full h-11 pl-9 pr-3.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 bg-slate-50 text-slate-800 placeholder:text-slate-400"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <select wire:model.live="movementType" class="w-full md:w-auto h-11 px-3.5 border border-slate-200 rounded-xl bg-white font-bold text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
            <option value="ALL">Semua Tipe Pergerakan</option>
            @foreach($movementTypes as $type)
                <option value="{{ $type }}">
                    {{ $type === 'SALE' ? 'Penjualan Kasir' : ($type === 'STOCK_OPNAME' ? 'Stock Opname' : ($type === 'ADJUSTMENT' ? 'Penyesuaian Manual' : ($type === 'TRASH_RESTORE' ? 'Pemulihan Transaksi' : ($type === 'DAMAGE' ? 'Barang Rusak / Hilang' : $type)))) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200/90 text-slate-500 uppercase text-xs font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th wire:click="sortBy('created_at')" class="py-3.5 px-4 cursor-pointer hover:text-emerald-700 transition select-none">
                            <div class="flex items-center gap-1">
                                <span>Waktu</span>
                                @if($sortField === 'created_at')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Nama Barang & Lokasi</th>
                        <th wire:click="sortBy('movement_type')" class="py-3.5 px-4 cursor-pointer hover:text-emerald-700 transition select-none">
                            <div class="flex items-center gap-1">
                                <span>Tipe Pergerakan</span>
                                @if($sortField === 'movement_type')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4 text-center">Sebelum</th>
                        <th wire:click="sortBy('quantity_change')" class="py-3.5 px-4 text-center cursor-pointer hover:text-emerald-700 transition select-none">
                            <div class="flex items-center justify-center gap-1">
                                <span>Perubahan</span>
                                @if($sortField === 'quantity_change')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
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
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-500 font-semibold text-xs whitespace-nowrap">{{ $movement->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $movement->product?->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">Barcode: {{ $movement->product?->effective_barcode ?? '-' }} &bull; {{ $movement->location?->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold font-sans text-xs whitespace-nowrap inline-block">{{ $typeLabel }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold whitespace-nowrap text-sm text-slate-800">{{ $movement->quantity_before }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold whitespace-nowrap text-sm {{ $movement->quantity_change < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold whitespace-nowrap text-sm text-slate-800">{{ $movement->quantity_after }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($movement->notes)
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200 inline-block whitespace-nowrap">
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
        <div class="p-3.5 border-t border-slate-200/90">{{ $movements->links('components.emco-pagination') }}</div>
    </div>
</div>
