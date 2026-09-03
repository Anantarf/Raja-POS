<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Pergerakan Stok</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Histori SALE, TRASH_RESTORE, ADJUSTMENT, DAMAGE, dan STOCK_OPNAME terpadu.</p>
        </div>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari produk, SKU, tipe, atau catatan..." class="w-full md:w-72 px-3.5 py-2 border border-slate-200 rounded-xl bg-[#F3F6F4] font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20" />
        <select wire:model.live="movementType" class="w-full md:w-auto px-3 py-2 border border-slate-200 rounded-xl bg-white font-bold text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20">
            <option value="ALL">Semua Tipe Pergerakan</option>
            @foreach($movementTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Produk & Lokasi</th>
                        <th class="py-3.5 px-4">Tipe Pergerakan</th>
                        <th class="py-3.5 px-4 text-center">Sebelum</th>
                        <th class="py-3.5 px-4 text-center">Perubahan</th>
                        <th class="py-3.5 px-4 text-center">Sesudah</th>
                        <th class="py-3.5 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 text-[#718379] font-semibold">{{ $movement->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#232E28] text-sm">{{ $movement->product?->name ?? '-' }}</div>
                                <div class="text-xs text-[#718379] font-mono mt-0.5">{{ $movement->product?->code ?? '-' }} &bull; {{ $movement->location?->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-bold text-[11px]">{{ $movement->movement_type }}</span></td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold">{{ $movement->quantity_before }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold {{ $movement->quantity_change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold">{{ $movement->quantity_after }}</td>
                            <td class="py-3.5 px-4 text-[#52645B]">{{ $movement->notes ?? '-' }}</td>
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
