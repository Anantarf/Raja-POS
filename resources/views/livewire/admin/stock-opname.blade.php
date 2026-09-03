<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Sesi Stock Opname</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Penyesuaian stok fisik berkala (Cepat 1 Barang / Opname Masal Seluruh Toko).</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="openBulkModal" class="px-4 py-2.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#232E28] hover:text-[#3F7A5D] border border-slate-200/80 font-extrabold rounded-2xl text-xs transition flex items-center gap-2 shadow-sm active:scale-95 cursor-pointer uppercase tracking-wider shrink-0">
                <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span>Opname Masal (Banyak Barang)</span>
            </button>
            <button wire:click="openCreateModal" class="px-4 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition flex items-center gap-2 shadow-sm active:scale-95 cursor-pointer uppercase tracking-wider shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Opname Cepat (1 Barang)</span>
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="w-full sm:w-80 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Opname, nama barang, barcode..."
                class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] text-[#232E28] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap">
                    <tr>
                        <th wire:click="sortBy('opname_number')" class="py-3.5 px-4 cursor-pointer hover:text-[#3F7A5D] transition select-none">
                            <div class="flex items-center gap-1">
                                <span>No. Opname &amp; Waktu</span>
                                @if($sortField === 'opname_number' || $sortField === 'created_at')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Rincian Barang & Lokasi</th>
                        <th class="py-3.5 px-4 text-center">Stok Sistem</th>
                        <th class="py-3.5 px-4 text-center">Stok Fisik</th>
                        <th class="py-3.5 px-4 text-center">Total Selisih</th>
                        <th wire:click="sortBy('status')" class="py-3.5 px-4 text-center cursor-pointer hover:text-[#3F7A5D] transition select-none">
                            <div class="flex items-center justify-center gap-1">
                                <span>Status</span>
                                @if($sortField === 'status')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($sessions as $opn)
                        @php
                            $firstItem = $opn->items->first();
                            $itemsCount = $opn->items_count ?? $opn->items->count();
                            $totalSystem = $opn->items->sum('system_quantity');
                            $totalPhysical = $opn->items->sum('physical_quantity');
                            $totalDiff = $opn->items->sum('difference');
                        @endphp
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 font-mono text-xs whitespace-nowrap">
                                <div class="font-bold text-[#3F7A5D] bg-[#F3F6F4] border border-slate-200/80 px-2.5 py-0.5 rounded-md inline-block">{{ $opn->opname_number }}</div>
                                <div class="text-[10px] text-[#718379] font-sans mt-1 font-semibold whitespace-nowrap">{{ $opn->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($itemsCount > 1)
                                    <div class="font-bold text-[#232E28] text-sm flex items-center gap-1.5">
                                        <span>Audit Opname Masal</span>
                                        <span class="bg-[#E3EEE8] text-[#3F7A5D] text-[10px] font-extrabold px-2 py-0.5 rounded-md">{{ $itemsCount }} Barang</span>
                                    </div>
                                    <div class="text-xs text-[#718379] font-semibold">{{ $opn->location?->name }} &bull; {{ $firstItem?->product?->name }} & dll.</div>
                                @else
                                    <div class="font-bold text-[#232E28] text-sm">{{ $firstItem?->product?->name ?? 'Barang' }}</div>
                                    <div class="text-xs text-[#718379] font-semibold">{{ $opn->location?->name }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-[#232E28] whitespace-nowrap">{{ $totalSystem }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold text-[#3F7A5D] text-sm whitespace-nowrap">{{ $totalPhysical }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-extrabold whitespace-nowrap {{ $totalDiff < 0 ? 'text-rose-600' : ($totalDiff > 0 ? 'text-emerald-600' : 'text-[#718379]') }}">
                                {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider whitespace-nowrap inline-block {{ $opn->status === 'COMPLETED' ? 'bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20' : 'bg-amber-50 text-amber-800 border border-amber-200/80' }}">
                                    {{ $opn->status === 'COMPLETED' ? 'SELESAI' : 'DRAFT' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                    <button wire:click="openDetailModal({{ $opn->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-[#E3EEE8] text-[#232E28] hover:text-[#3F7A5D] border border-slate-200/80 rounded-xl text-xs font-extrabold transition cursor-pointer shadow-sm">
                                        Detail
                                    </button>
                                    @if($opn->status === 'DRAFT')
                                        <button wire:click="approveSession({{ $opn->id }})" wire:confirm="Setujui penyesuaian stok opname ini? Stok toko akan diperbarui." class="px-3.5 py-1.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white rounded-xl font-extrabold text-xs transition uppercase tracking-wider shadow-sm cursor-pointer whitespace-nowrap">
                                            Setujui Opname
                                        </button>
                                    @else
                                        <span class="text-xs text-[#718379] font-semibold whitespace-nowrap">Disetujui: {{ $opn->approver?->name }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Belum ada sesi stock opname.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-[#E3EEE8]">{{ $sessions->links() }}</div>
    </div>

    <!-- Quick Single Item Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#232E28]">Opname Cepat (1 Barang)</h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createSession" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Lokasi Toko</label>
                        <select wire:model="location_id" class="w-full p-3 border border-slate-200 rounded-xl bg-[#F3F6F4] text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] transition" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Produk *</label>
                        <select wire:model="product_id" class="w-full p-3 border border-slate-200 rounded-xl bg-[#F3F6F4] text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] transition" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Barcode: {{ $prod->effective_barcode }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Hasil Hitung Stok Fisik *</label>
                        <input type="number" wire:model="physical_qty" class="w-full p-3 border border-slate-200 rounded-xl bg-[#F3F6F4] text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] transition" required min="0" placeholder="0" />
                    </div>

                    <div>
                        <label class="block font-bold text-[#232E28] mb-1.5">Catatan / Alasan Opname</label>
                        <textarea wire:model="notes" rows="2" class="w-full p-3 border border-slate-200 rounded-xl bg-[#F3F6F4] text-xs focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] transition" placeholder="Misal: Barang hilang/rusak saat pajang"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition cursor-pointer">Batal</button>
                        <button type="submit" class="py-2.5 px-5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider transition active:scale-95 shadow-sm cursor-pointer">Simpan Opname</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Bulk Opname Sheet Modal -->
    @if($showBulkModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-6">
            <div class="bg-white rounded-2xl p-5 sm:p-6 max-w-5xl w-full shadow-2xl space-y-4 border border-slate-100 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                    <div>
                        <h3 class="text-lg font-extrabold text-[#232E28]">Lembar Hitung Stock Opname Masal</h3>
                        <p class="text-xs text-[#718379] font-medium mt-0.5">Audit seluruh barang toko sekaligus. Masukkan hasil hitung fisik pada kolom yang tersedia.</p>
                    </div>
                    <button type="button" wire:click="$set('showBulkModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs shrink-0 bg-[#F3F6F4] p-3 rounded-xl border border-slate-200/80">
                    <div>
                        <label class="block font-bold text-[#232E28] mb-1">Lokasi Toko</label>
                        <select wire:model.live="bulk_location_id" class="w-full p-2.5 border border-slate-200 rounded-xl bg-white font-semibold">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#232E28] mb-1">Filter Kategori</label>
                        <select wire:model.live="bulk_category_id" class="w-full p-2.5 border border-slate-200 rounded-xl bg-white font-semibold">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#232E28] mb-1">Cari Barang / Barcode</label>
                        <input type="text" wire:model.live.debounce.300ms="bulk_search" placeholder="Cari nama barang..." class="w-full p-2.5 border border-slate-200 rounded-xl bg-white font-semibold" />
                    </div>
                </div>

                <!-- Bulk Table Sheet -->
                <div class="overflow-y-auto flex-1 border border-slate-200/80 rounded-xl">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F3F6F4] sticky top-0 border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider whitespace-nowrap z-10">
                            <tr>
                                <th class="py-3 px-4">Nama Barang & Barcode</th>
                                <th class="py-3 px-4 text-center">Stok Komputer</th>
                                <th class="py-3 px-4 text-center w-36">Hasil Hitung Fisik</th>
                                <th class="py-3 px-4 text-center">Selisih</th>
                                <th class="py-3 px-4">Catatan Per Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($bulkItems as $prodId => $item)
                                @php
                                    $physQty = (int) ($item['physical_qty'] ?? 0);
                                    $sysQty = (int) ($item['system_qty'] ?? 0);
                                    $diff = $physQty - $sysQty;
                                @endphp
                                <tr class="hover:bg-[#F3F6F4]/60 transition {{ $diff != 0 ? 'bg-amber-50/50' : '' }}">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-[#232E28] text-xs">{{ $item['product_name'] }}</div>
                                        <div class="text-[11px] text-[#718379] font-mono mt-0.5">Barcode: {{ $item['effective_barcode'] }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold text-[#232E28]">{{ $sysQty }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <input
                                            type="number"
                                            wire:model.live.debounce.300ms="bulkItems.{{ $prodId }}.physical_qty"
                                            min="0"
                                            class="w-24 text-center p-2 border border-slate-300 rounded-xl font-mono font-extrabold text-sm focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-white"
                                        />
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-extrabold {{ $diff < 0 ? 'text-rose-600' : ($diff > 0 ? 'text-emerald-600' : 'text-slate-400') }}">
                                        <span class="px-2.5 py-0.5 rounded-md text-xs {{ $diff < 0 ? 'bg-rose-50 border border-rose-200' : ($diff > 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-100') }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input
                                            type="text"
                                            wire:model="bulkItems.{{ $prodId }}.notes"
                                            placeholder="Catatan (opsional)..."
                                            class="w-full p-2 border border-slate-200 rounded-xl text-xs bg-white"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 font-medium">Tidak ada produk fisik ditemukan untuk lokasi ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary & Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 shrink-0 text-xs">
                    <div class="text-[#718379] font-medium">
                        Total Barang Diaudit: <span class="font-mono font-bold text-[#232E28]">{{ count($bulkItems) }}</span> Item
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showBulkModal', false)" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition cursor-pointer">Batal</button>
                        <button type="button" wire:click="createBulkSession" class="py-2.5 px-5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider transition active:scale-95 shadow-sm cursor-pointer">
                            Simpan & Ajukan Opname Masal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Session Detail Modal -->
    @if($showDetailModal && $selectedOpnameDetail)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-2xl w-full shadow-xl space-y-4 border border-slate-100 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                    <div>
                        <h3 class="text-base font-extrabold text-[#232E28]">Detail Sesi Stock Opname</h3>
                        <div class="text-xs text-[#718379] font-mono mt-0.5">{{ $selectedOpnameDetail->opname_number }} &bull; {{ $selectedOpnameDetail->location?->name }}</div>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 border border-slate-200/80 rounded-xl text-xs">
                    <table class="w-full text-left">
                        <thead class="bg-[#F3F6F4] sticky top-0 border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Nama Barang & Barcode</th>
                                <th class="py-3 px-4 text-center">Sistem</th>
                                <th class="py-3 px-4 text-center">Fisik</th>
                                <th class="py-3 px-4 text-center">Selisih</th>
                                <th class="py-3 px-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($selectedOpnameDetail->items as $item)
                                <tr class="hover:bg-[#F3F6F4]/60">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-[#232E28]">{{ $item->product?->name }}</div>
                                        <div class="text-[11px] text-[#718379] font-mono">Barcode: {{ $item->product?->effective_barcode }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold">{{ $item->system_quantity }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-extrabold text-[#3F7A5D]">{{ $item->physical_quantity }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-extrabold {{ $item->difference < 0 ? 'text-rose-600' : ($item->difference > 0 ? 'text-emerald-600' : 'text-slate-400') }}">
                                        {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                    </td>
                                    <td class="py-3 px-4 text-[#718379]">{{ $item->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs shrink-0">
                    <div class="text-[#718379]">
                        Status: <span class="font-bold uppercase {{ $selectedOpnameDetail->status === 'COMPLETED' ? 'text-[#3F7A5D]' : 'text-amber-800' }}">{{ $selectedOpnameDetail->status === 'COMPLETED' ? 'SELESAI' : 'DRAFT' }}</span>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
