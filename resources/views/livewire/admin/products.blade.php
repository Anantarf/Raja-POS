<div class="space-y-5">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-[#566A7F] tracking-tight">Master Katalog Produk</h1>
            <p class="text-xs text-[#A1ACB8] font-medium mt-0.5">Kelola daftar seluruh produk fisik, digital, dan layanan service.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/admin/products/template-csv" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-[#566A7F] font-bold border border-slate-200 rounded-xl text-xs shadow-sneat transition flex items-center gap-1.5 active:scale-95">
                <span>📄 Unduh Template CSV</span>
            </a>
            <button wire:click="$set('showImportModal', true)" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95">
                <span>📥 Import CSV</span>
            </button>
            <button wire:click="openCreateModal" class="px-4 py-2.5 bg-[#696CFF] hover:bg-[#5F61E6] text-white font-bold rounded-xl text-xs shadow-sneat-primary transition flex items-center gap-1.5 active:scale-95">
                <span>+ Tambah Produk Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sneat flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <div class="w-full md:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama, SKU, barcode..."
                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] bg-[#F5F5F9] placeholder:text-[#A1ACB8]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="w-full md:w-auto flex items-center gap-2 overflow-x-auto">
            <select wire:model.live="selectedCategory" class="px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold bg-white text-[#566A7F]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-1 bg-[#F5F5F9] p-1 rounded-xl border border-slate-200">
                <button wire:click="$set('selectedType', 'ALL')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs {{ $selectedType === 'ALL' ? 'bg-[#696CFF] text-white shadow-sneat-primary' : 'text-[#697A8D]' }}">Semua</button>
                <button wire:click="$set('selectedType', 'PHYSICAL')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs {{ $selectedType === 'PHYSICAL' ? 'bg-[#696CFF] text-white shadow-sneat-primary' : 'text-[#697A8D]' }}">Fisik</button>
                <button wire:click="$set('selectedType', 'DIGITAL')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs {{ $selectedType === 'DIGITAL' ? 'bg-emerald-600 text-white shadow-sm' : 'text-[#697A8D]' }}">Digital</button>
                <button wire:click="$set('selectedType', 'SERVICE')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs {{ $selectedType === 'SERVICE' ? 'bg-amber-600 text-white shadow-sm' : 'text-[#697A8D]' }}">Service</button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sneat overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F5F5F9] border-b border-slate-100 text-[#A1ACB8] uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Produk</th>
                        <th class="py-3.5 px-4">Tipe</th>
                        <th class="py-3.5 px-4">Kategori & Brand</th>
                        @if(auth()->user()->can('cost_price.view'))
                            <th class="py-3.5 px-4 text-right">Modal (HPP)</th>
                        @endif
                        <th class="py-3.5 px-4 text-right">Harga Jual</th>
                        <th class="py-3.5 px-4 text-center">Status Harga</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#F5F5F9]/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-xl bg-slate-100 border border-slate-200/80 shrink-0">
                                    <div>
                                        <div class="font-bold text-[#566A7F] text-xs leading-snug tracking-tight">{{ $product->name }}</div>
                                        <div class="text-[10px] font-mono text-[#A1ACB8] mt-0.5">
                                            <span class="bg-[#E7E7FF] text-[#696CFF] px-1.5 py-0.5 rounded font-bold">SKU: {{ $product->code }}</span>
                                            @if($product->effective_barcode) &bull; Barcode: {{ $product->effective_barcode }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E7E7FF] text-[#696CFF]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $product->product_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#566A7F] font-semibold">
                                {{ $product->category?->name ?? '-' }} / <span class="text-[#A1ACB8] font-normal">{{ $product->brand?->name ?? '-' }}</span>
                            </td>
                            @if(auth()->user()->can('cost_price.view'))
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-[#566A7F] text-xs">
                                    Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                </td>
                            @endif
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#696CFF] text-sm">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $product->price_status === 'COMPLETE' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $product->price_status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openEditModal({{ $product->id }})" class="px-3 py-1.5 bg-[#F5F5F9] hover:bg-slate-200 text-[#566A7F] rounded-lg text-xs font-semibold transition">
                                        Edit
                                    </button>
                                    <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus produk ini?" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-medium">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232333]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 border border-slate-100 overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-extrabold text-[#566A7F]">
                        {{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveProduct" class="space-y-3.5 text-xs font-medium">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Kode SKU *</label>
                            <input type="text" wire:model="code" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Barcode scanner</label>
                            <input type="text" wire:model="barcode" placeholder="Opsional" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Nama Produk *</label>
                        <input type="text" wire:model="name" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] font-semibold" required />
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Tipe Produk</label>
                            <select wire:model="product_type" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold">
                                <option value="PHYSICAL">📦 Fisik</option>
                                <option value="DIGITAL">⚡ Digital</option>
                                <option value="SERVICE">🛠️ Service</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Kategori</label>
                            <select wire:model="category_id" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold">
                                <option value="">-- Pilih --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Brand</label>
                            <select wire:model="brand_id" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold">
                                <option value="">-- Pilih --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Harga Modal (HPP) *</label>
                            <input type="number" wire:model="cost_price" class="w-full p-2.5 border border-slate-300 rounded-xl font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Harga Jual Kasir *</label>
                            <input type="number" wire:model="selling_price" class="w-full p-2.5 border border-slate-300 rounded-xl font-mono text-[#696CFF] font-extrabold text-sm" required />
                        </div>
                    </div>

                    @if(!$editingProductId && $product_type === 'PHYSICAL')
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Stok Awal Fisik</label>
                            <input type="number" wire:model="initial_stock" class="w-full p-2.5 border border-slate-300 rounded-xl font-mono font-bold" />
                        </div>
                    @endif

                    <div class="pt-3 flex gap-2">
                        <button type="submit" class="flex-1 py-3.5 bg-[#696CFF] hover:bg-[#5F61E6] text-white font-bold rounded-xl transition shadow-sneat-primary text-xs uppercase tracking-wider">
                            Simpan Produk
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-3.5 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import CSV Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-[#232333]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-extrabold text-[#566A7F]">Import Produk dari File CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processImport" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Pilih File CSV</label>
                        <input type="file" wire:model="importFile" class="w-full p-2.5 border border-slate-300 rounded-xl bg-[#F5F5F9] font-medium" required />
                    </div>

                    <div class="bg-[#E7E7FF] text-[#696CFF] p-3 rounded-xl text-[11px] leading-relaxed font-medium">
                        💡 Auto-create Kategori & Brand jika belum ada di database. HPP & Harga Jual 0 otomatis ditandai `INCOMPLETE`.
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm uppercase tracking-wider text-xs">
                            Mulai Import Data
                        </button>
                        <button type="button" wire:click="$set('showImportModal', false)" class="py-3.5 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
