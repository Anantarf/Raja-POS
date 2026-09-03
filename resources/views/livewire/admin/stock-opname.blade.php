<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Sesi Stock Opname</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Penyesuaian stok fisik berkala dan audit selisih stok.</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition flex items-center gap-2 shadow-sm active:scale-95 cursor-pointer uppercase tracking-wider shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>+ Buat Sesi Opname</span>
        </button>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap">
                <tr>
                    <th class="py-3.5 px-4">No. Opname & Waktu</th>
                    <th class="py-3.5 px-4">Nama Barang & Lokasi</th>
                    <th class="py-3.5 px-4 text-center">Stok Sistem</th>
                    <th class="py-3.5 px-4 text-center">Stok Fisik</th>
                    <th class="py-3.5 px-4 text-center">Selisih</th>
                    <th class="py-3.5 px-4 text-center">Status</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($sessions as $opn)
                    @php($item = $opn->items->first())
                    <tr class="hover:bg-[#F3F6F4]/60 transition">
                        <td class="py-3.5 px-4 font-mono text-xs whitespace-nowrap">
                            <div class="font-bold text-[#3F7A5D] bg-[#F3F6F4] border border-slate-200/80 px-2.5 py-0.5 rounded-md inline-block">{{ $opn->opname_number }}</div>
                            <div class="text-[10px] text-[#718379] font-sans mt-1 font-semibold whitespace-nowrap">{{ $opn->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-[#232E28] text-sm">{{ $item?->product?->name }}</div>
                            <div class="text-xs text-[#718379] font-semibold">{{ $opn->location?->name }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-[#232E28] whitespace-nowrap">{{ $item?->system_quantity ?? 0 }}</td>
                        <td class="py-3.5 px-4 text-center font-mono font-extrabold text-[#3F7A5D] text-sm whitespace-nowrap">{{ $item?->physical_quantity ?? 0 }}</td>
                        <td class="py-3.5 px-4 text-center font-mono font-extrabold whitespace-nowrap {{ ($item?->difference ?? 0) < 0 ? 'text-rose-600' : (($item?->difference ?? 0) > 0 ? 'text-emerald-600' : 'text-[#718379]') }}">
                            {{ ($item?->difference ?? 0) > 0 ? '+' : '' }}{{ $item?->difference ?? 0 }}
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider whitespace-nowrap inline-block {{ $opn->status === 'COMPLETED' ? 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20' : 'bg-amber-50 text-amber-800 border border-amber-200/80' }}">
                                {{ $opn->status === 'COMPLETED' ? 'SELESAI' : 'DRAFT' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($opn->status === 'DRAFT')
                                <button wire:click="approveSession({{ $opn->id }})" wire:confirm="Setujui penyesuaian stok opname ini?" class="px-3.5 py-1.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white rounded-xl font-extrabold text-xs transition uppercase tracking-wider shadow-sm cursor-pointer whitespace-nowrap">
                                    Setujui Opname
                                </button>
                            @else
                                <span class="text-xs text-[#718379] font-semibold whitespace-nowrap">Disetujui: {{ $opn->approver?->name }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Belum ada sesi stock opname.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-[#E3EEE8]">{{ $sessions->links() }}</div>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl space-y-4 border border-slate-100">
                <h3 class="text-base font-extrabold text-[#232E28]">Buat Sesi Stock Opname Baru</h3>
                <form wire:submit.prevent="createSession" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Lokasi Toko</label>
                        <select wire:model="location_id" class="w-full p-3 border border-slate-200 rounded-2xl bg-white text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Produk</label>
                        <select wire:model="product_id" class="w-full p-3 border border-slate-200 rounded-2xl bg-white text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Barcode: {{ $prod->effective_barcode }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Hasil Hitung Stok Fisik *</label>
                        <input type="number" wire:model="physical_qty" class="w-full p-3 border border-slate-200 rounded-2xl text-xs font-mono font-bold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required min="0" placeholder="0" />
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Catatan / Alasan Opname</label>
                        <textarea wire:model="notes" rows="2" class="w-full p-3 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" placeholder="Misal: Barang hilang/rusak saat pajang"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition uppercase tracking-wider">Simpan Opname</button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

