<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Sesi Stock Opname</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Penyesuaian stok fisik berkala dan audit selisih stok.</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
            + Buat Sesi Opname
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold">
                <tr>
                    <th class="py-3 px-4">No. Opname & Waktu</th>
                    <th class="py-3 px-4">Produk & Lokasi</th>
                    <th class="py-3 px-4 text-center">Stok Sistem</th>
                    <th class="py-3 px-4 text-center">Stok Fisik</th>
                    <th class="py-3 px-4 text-center">Selisih</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($sessions as $opn)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900">
                            <div>{{ $opn->opname_number }}</div>
                            <div class="text-[10px] text-slate-400 font-sans">{{ $opn->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900">{{ $opn->product?->name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $opn->location?->name }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold">{{ $opn->system_quantity }}</td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-blue-600">{{ $opn->physical_quantity }}</td>
                        <td class="py-3 px-4 text-center font-mono font-bold {{ $opn->difference < 0 ? 'text-rose-600' : ($opn->difference > 0 ? 'text-emerald-600' : 'text-slate-600') }}">
                            {{ $opn->difference > 0 ? '+' : '' }}{{ $opn->difference }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $opn->status === 'APPROVED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $opn->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($opn->status === 'PENDING')
                                <button wire:click="approveSession({{ $opn->id }})" wire:confirm="Setujui penyesuaian stok opname ini?" class="p-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs">
                                    Approve ✓
                                </button>
                            @else
                                <span class="text-[10px] text-slate-400">Disetujui: {{ $opn->approver?->name }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada sesi stock opname.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3 border-t">{{ $sessions->links() }}</div>
    </div>

    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border">
                <h3 class="text-base font-extrabold text-slate-900">Buat Sesi Stock Opname Baru</h3>
                <form wire:submit.prevent="createSession" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Lokasi Toko</label>
                        <select wire:model="location_id" class="w-full p-2.5 border rounded-xl bg-white font-semibold">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Pilih Produk Fisik</label>
                        <select wire:model="product_id" class="w-full p-2.5 border rounded-xl bg-white font-semibold">
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Jumlah Hasil Hitung Fisik *</label>
                        <input type="number" wire:model="physical_qty" min="0" class="w-full p-2.5 border rounded-xl font-mono font-bold text-sm" required />
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Catatan</label>
                        <input type="text" wire:model="notes" class="w-full p-2.5 border rounded-xl" />
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-xl">Simpan Sesi Opname</button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-3 px-4 bg-slate-100 font-semibold rounded-xl">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
