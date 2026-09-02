<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Kategori Produk</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola pengelompokan jenis barang toko.</p>
        </div>
        <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
            + Tambah Kategori
        </button>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between text-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..." class="w-1/3 px-3 py-2 border border-slate-200 rounded-xl text-xs" />
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold">
                <tr>
                    <th class="py-3 px-4">Nama Kategori</th>
                    <th class="py-3 px-4">Slug URL</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($categories as $cat)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $cat->name }}</td>
                        <td class="py-3 px-4 font-mono text-slate-500">{{ $cat->slug }}</td>
                        <td class="py-3 px-4 text-center">
                            <button wire:click="openModal({{ $cat->id }})" class="p-1.5 bg-slate-100 text-slate-700 rounded-lg font-semibold mr-1">Edit</button>
                            <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Hapus kategori ini?" class="p-1.5 bg-rose-50 text-rose-600 rounded-lg font-semibold">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-6 text-center text-slate-400">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3 border-t">{{ $categories->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4 border">
                <h3 class="text-base font-extrabold text-slate-900">{{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
                <form wire:submit.prevent="saveCategory" class="space-y-3 text-xs">
                    <div>
                        <label class="block text-slate-700 font-semibold mb-1">Nama Kategori *</label>
                        <input type="text" wire:model="name" class="w-full p-2.5 border rounded-xl" required />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white font-bold rounded-xl">Simpan</button>
                        <button type="button" wire:click="$set('showModal', false)" class="py-2.5 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
