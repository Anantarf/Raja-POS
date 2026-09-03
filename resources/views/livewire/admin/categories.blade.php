<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">Kategori Produk</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Kelola pengelompokan jenis barang toko.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between text-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..." class="w-full sm:w-1/3 px-3.5 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4]" />
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                <tr>
                    <th class="py-3.5 px-4">Nama Kategori</th>
                    <th class="py-3.5 px-4">Slug URL</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($categories as $cat)
                    <tr class="hover:bg-[#F3F6F4]/60 transition">
                        <td class="py-3.5 px-4 font-bold text-[#232E28] text-sm">{{ $cat->name }}</td>
                        <td class="py-3.5 px-4 font-mono">
                            <span class="bg-[#F3F6F4] border border-slate-200/80 text-[#3F7A5D] px-2.5 py-0.5 rounded-md font-mono font-bold text-[11px]">{{ $cat->slug }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openModal({{ $cat->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 rounded-lg text-xs font-bold transition">Edit</button>
                                <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Hapus kategori ini?" class="px-3 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-lg text-xs font-bold transition">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-12 text-center text-slate-400 font-medium">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200/80">{{ $categories->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl space-y-4 border border-slate-100">
                <h3 class="text-base font-extrabold text-[#232E28]">{{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                <form wire:submit.prevent="saveCategory" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-[#232E28] font-bold mb-1.5">Nama Kategori *</label>
                        <input type="text" wire:model="name" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs transition uppercase tracking-wider shadow-sm">Simpan</button>
                        <button type="button" wire:click="$set('showModal', false)" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-xl text-xs transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
