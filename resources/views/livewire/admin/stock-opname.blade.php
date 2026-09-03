<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">Sesi Stock Opname</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Penyesuaian stok fisik berkala dan audit selisih stok.</p>
        </div>
        <button wire:click="openCreateModal" class="px-5 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition flex items-center gap-1.5 shadow-sm active:scale-95">
            <span>+ Buat Sesi Opname</span>
        </button>
    </div>

    <div class="bg-white border border-[#E3EEE8] rounded-3xl shadow-emco overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-[#F3F6F4] border-b border-[#E3EEE8] text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                <tr>
                    <th class="py-4 px-5">No. Opname & Waktu</th>
                    <th class="py-4 px-5">Produk & Lokasi</th>
                    <th class="py-4 px-5 text-center">Stok Sistem</th>
                    <th class="py-4 px-5 text-center">Stok Fisik</th>
                    <th class="py-4 px-5 text-center">Selisih</th>
                    <th class="py-4 px-5 text-center">Status</th>
                    <th class="py-4 px-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($sessions as $opn)
                    @php($item = $opn->items->first())
                    <tr class="hover:bg-[#F3F6F4]/60 transition">
                        <td class="py-4 px-5 font-mono text-xs">
                            <div class="font-bold text-indigo-600 bg-indigo-50 border border-indigo-200/70 px-2 py-0.5 rounded-md inline-block">{{ $opn->opname_number }}</div>
                            <div class="text-[10px] text-[#718379] font-sans mt-1 font-semibold">{{ $opn->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-4 px-5">
                            <div class="font-extrabold text-[#232E28] text-sm">{{ $item?->product?->name }}</div>
                            <div class="text-[10px] text-[#718379] font-semibold">{{ $opn->location?->name }}</div>
                        </td>
                        <td class="py-4 px-5 text-center font-mono font-bold text-[#232E28]">{{ $item?->system_quantity ?? 0 }}</td>
                        <td class="py-4 px-5 text-center font-mono font-extrabold text-indigo-600 text-sm">{{ $item?->physical_quantity ?? 0 }}</td>
                        <td class="py-4 px-5 text-center font-mono font-extrabold {{ ($item?->difference ?? 0) < 0 ? 'text-rose-600' : (($item?->difference ?? 0) > 0 ? 'text-emerald-600' : 'text-[#718379]') }}">
                            {{ ($item?->difference ?? 0) > 0 ? '+' : '' }}{{ $item?->difference ?? 0 }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $opn->status === 'COMPLETED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $opn->status }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($opn->status === 'DRAFT')
                                <button wire:click="approveSession({{ $opn->id }})" wire:confirm="Setujui penyesuaian stok opname ini?" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold text-xs transition active:scale-95">
                                    Approve
                                </button>
                            @else
                                <span class="text-[10px] text-[#718379] font-semibold">Disetujui: {{ $opn->approver?->name }}</span>
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
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
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
                                <option value="{{ $prod->id }}">{{ $prod->name }} (SKU: {{ $prod->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Hasil Hitung Stok Fisik Real *</label>
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
