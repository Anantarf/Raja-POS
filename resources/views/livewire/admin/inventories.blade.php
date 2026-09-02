<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-[#566A7F] tracking-tight">Manajemen Stok Fisik Produk</h1>
            <p class="text-xs text-[#A1ACB8] font-medium mt-0.5">Pantau jumlah ketersediaan stok fisik barang per lokasi cabang toko.</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sneat flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <div class="w-full md:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama produk, SKU, barcode..."
                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] bg-[#F5F5F9] placeholder:text-[#A1ACB8]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="w-full md:w-auto flex items-center gap-2">
            <label class="font-semibold text-[#566A7F]">Lokasi Toko:</label>
            <select wire:model.live="selectedLocationId" class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-semibold bg-white text-[#566A7F]">
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Inventories Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sneat overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F5F5F9] border-b border-slate-100 text-[#A1ACB8] uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Produk</th>
                        <th class="py-3.5 px-4">Lokasi Cabang</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Stok</th>
                        <th class="py-3.5 px-4 text-center">Status Stok</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($inventories as $inv)
                        <tr class="hover:bg-[#F5F5F9]/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#566A7F] leading-tight">{{ $inv->product?->name }}</div>
                                <div class="text-[10px] text-[#A1ACB8] font-mono mt-0.5">
                                    <span class="bg-[#E7E7FF] text-[#696CFF] px-1.5 py-0.5 rounded font-bold">SKU: {{ $inv->product?->code }}</span> &bull; Kategori: {{ $inv->product?->category?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-[#566A7F]">
                                {{ $inv->location?->name }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sm text-[#566A7F]">
                                {{ number_format($inv->quantity, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inv->stock_status === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700' : ($inv->stock_status === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                    {{ $inv->stock_status === 'OUT_OF_STOCK' ? 'HABIS' : ($inv->stock_status === 'LOW_STOCK' ? 'MENIPIS' : 'TERSEDIA') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button wire:click="openAdjustmentModal({{ $inv->id }})" class="px-3 py-1.5 bg-[#E7E7FF] hover:bg-indigo-100 text-[#696CFF] rounded-lg text-xs font-semibold transition">
                                    Adjust Stok ✏️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-medium">Belum ada data stok fisik di lokasi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 border-t border-slate-100">
            {{ $inventories->links() }}
        </div>
    </div>

    <!-- Manual Stock Adjustment Modal -->
    @if($showAdjustmentModal)
        <div class="fixed inset-0 bg-[#232333]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-extrabold text-[#566A7F]">Penyesuaian Manual Stok</h3>
                    <button wire:click="$set('showAdjustmentModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processAdjustment" class="space-y-3.5 text-xs font-medium">
                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Tipe Penyesuaian</label>
                        <select wire:model="adjustType" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold">
                            <option value="SET">Atur Jumlah Baru (SET)</option>
                            <option value="ADD">Tambah Stok (+)</option>
                            <option value="SUBTRACT">Kurangi Stok (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Jumlah Nilai *</label>
                        <input type="number" wire:model="adjustQuantity" min="0" class="w-full p-2.5 border border-slate-300 rounded-xl font-mono font-bold text-sm text-[#696CFF]" required />
                    </div>

                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Alasan Penyesuaian *</label>
                        <input type="text" wire:model="adjustReason" class="w-full p-2.5 border border-slate-300 rounded-xl" required />
                    </div>

                    <div class="pt-3 flex gap-2">
                        <button type="submit" class="flex-1 py-3.5 bg-[#696CFF] hover:bg-[#5F61E6] text-white font-bold rounded-xl transition shadow-sneat-primary text-xs uppercase tracking-wider">
                            Update Stok Fisik
                        </button>
                        <button type="button" wire:click="$set('showAdjustmentModal', false)" class="py-3.5 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
