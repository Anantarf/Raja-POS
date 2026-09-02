<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">Brand / Merek Produk</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Kelola merek manufaktur aksesoris toko.</p>
        </div>
        <button wire:click="openModal" class="px-5 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition flex items-center gap-1.5 shadow-sm active:scale-95">
            <span>+ Tambah Brand</span>
        </button>
    </div>

    <div class="bg-white p-4 rounded-3xl border border-[#E3EEE8] shadow-emco flex items-center justify-between text-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama brand..." class="w-full sm:w-1/3 px-4 py-2.5 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4]" />
    </div>

    <div class="bg-white border border-[#E3EEE8] rounded-3xl shadow-emco overflow-hidden">
        <table class="w-full text-xs text-left">
            <thead class="bg-[#F3F6F4] border-b border-[#E3EEE8] text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                <tr>
                    <th class="py-4 px-5">Nama Brand</th>
                    <th class="py-4 px-5">Slug URL</th>
                    <th class="py-4 px-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($brands as $b)
                    <tr class="hover:bg-[#F3F6F4]/60 transition">
                        <td class="py-4 px-5 font-extrabold text-[#232E28] text-sm">{{ $b->name }}</td>
                        <td class="py-4 px-5 font-mono text-indigo-600">
                            <span class="bg-indigo-50 border border-indigo-200/70 px-2 py-0.5 rounded-md font-bold text-[11px]">{{ $b->slug }}</span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openModal({{ $b->id }})" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200/80 rounded-xl font-extrabold text-xs transition active:scale-95">Edit</button>
                                <button wire:click="deleteBrand({{ $b->id }})" wire:confirm="Hapus brand ini?" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 rounded-xl font-extrabold text-xs transition active:scale-95">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-12 text-center text-slate-400 font-medium">Belum ada brand.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-[#E3EEE8]">{{ $brands->links() }}</div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4 border border-slate-100">
                <h3 class="text-base font-extrabold text-[#232E28]">{{ $editingBrandId ? 'Edit Brand' : 'Tambah Brand' }}</h3>
                <form wire:submit.prevent="saveBrand" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-[#232E28] font-bold mb-1.5">Nama Brand *</label>
                        <input type="text" wire:model="name" class="w-full p-3 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition uppercase tracking-wider">Simpan</button>
                        <button type="button" wire:click="$set('showModal', false)" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
