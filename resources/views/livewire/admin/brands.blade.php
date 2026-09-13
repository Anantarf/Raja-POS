<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Merk / Brand Produk</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Kelola merek manufaktur aksesoris toko.</p>
        </div>
        <button wire:click="openModal" class="h-11 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl text-sm transition flex items-center gap-2 shadow-2xs active:scale-95 cursor-pointer shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Brand</span>
        </button>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between text-sm">
        <div class="relative w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama brand..."
                class="w-full h-11 pl-9 pr-3.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 bg-slate-50 text-slate-800 placeholder:text-slate-400"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 border-b border-slate-200/90 text-slate-500 uppercase text-xs font-extrabold tracking-wider">
                <tr>
                    <th wire:click="sortBy('name')" class="py-3.5 px-4 cursor-pointer hover:text-emerald-700 transition select-none">
                        <div class="flex items-center gap-1">
                            <span>Nama Brand</span>
                            @if($sortField === 'name')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @else
                                <span class="text-slate-300">↕</span>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('slug')" class="py-3.5 px-4 cursor-pointer hover:text-emerald-700 transition select-none">
                        <div class="flex items-center gap-1">
                            <span>Slug URL</span>
                            @if($sortField === 'slug')
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
                @forelse($brands as $b)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900 text-sm">{{ $b->name }}</td>
                        <td class="py-3.5 px-4 font-mono">
                            <span class="bg-slate-100 border border-slate-200 text-emerald-700 px-2.5 py-0.5 rounded-md font-mono font-bold text-xs">{{ $b->slug }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openModal({{ $b->id }})" class="h-9 px-3.5 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 rounded-xl text-sm font-bold transition cursor-pointer">Edit</button>
                                <button wire:click="deleteBrand({{ $b->id }})" wire:confirm="Hapus brand ini?" class="h-9 px-3.5 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-xl text-sm font-bold transition cursor-pointer">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-12 text-center text-slate-400 font-medium">Belum ada brand.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200/90">{{ $brands->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl space-y-4 border border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-900">{{ $editingBrandId ? 'Edit Brand' : 'Tambah Brand' }}</h3>
                <form wire:submit.prevent="saveBrand" class="space-y-4 text-sm">
                    <div>
                        <label class="block text-slate-800 font-bold mb-1.5">Nama Brand *</label>
                        <input type="text" wire:model="name" class="w-full h-11 p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm font-semibold text-slate-800 bg-slate-50" required />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 h-11 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl text-sm transition uppercase tracking-wider shadow-2xs flex items-center justify-center cursor-pointer">Simpan</button>
                        <button type="button" wire:click="$set('showModal', false)" class="h-11 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-sm transition flex items-center justify-center cursor-pointer">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
