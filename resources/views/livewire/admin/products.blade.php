<div class="space-y-6">
    <!-- Header Controls (Enlarged Title text-2xl & Buttons) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Master Katalog Produk</h1>
            <p class="text-xs text-[#718379] font-medium mt-1">Kelola daftar seluruh produk fisik, digital, dan layanan service.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/products/template-csv" class="px-5 py-3 bg-white hover:bg-slate-50 text-[#232E28] font-bold border border-[#E3EEE8] rounded-2xl text-xs shadow-emco transition flex items-center gap-2 active:scale-95">
                <span>📄 Unduh Template CSV</span>
            </a>
            <button wire:click="$set('showImportModal', true)" class="px-5 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl text-xs shadow-sm transition flex items-center gap-2 active:scale-95">
                <span>📥 Import CSV</span>
            </button>
            <button wire:click="openCreateModal" class="px-5 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-xs shadow-emco-primary transition flex items-center gap-2 active:scale-95">
                <span>+ Tambah Produk Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar (Scaled UP) -->
    <div class="bg-white p-5 rounded-3xl border border-[#E3EEE8] shadow-emco flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
        <div class="w-full md:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama produk, SKU, barcode..."
                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379]"
            />
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="w-full md:w-auto flex items-center gap-3 overflow-x-auto">
            <select wire:model.live="selectedCategory" class="px-4 py-3 border border-slate-200 rounded-2xl text-xs font-bold bg-white text-[#232E28]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-1.5 bg-[#F3F6F4] p-1.5 rounded-2xl border border-slate-200">
                <button wire:click="$set('selectedType', 'ALL')" class="px-4 py-2 rounded-xl font-bold text-xs {{ $selectedType === 'ALL' ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B]' }}">Semua</button>
                <button wire:click="$set('selectedType', 'PHYSICAL')" class="px-4 py-2 rounded-xl font-bold text-xs {{ $selectedType === 'PHYSICAL' ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B]' }}">Fisik</button>
                <button wire:click="$set('selectedType', 'DIGITAL')" class="px-4 py-2 rounded-xl font-bold text-xs {{ $selectedType === 'DIGITAL' ? 'bg-emerald-700 text-white shadow-sm' : 'text-[#52645B]' }}">Digital</button>
                <button wire:click="$set('selectedType', 'SERVICE')" class="px-4 py-2 rounded-xl font-bold text-xs {{ $selectedType === 'SERVICE' ? 'bg-[#C2AC7C] text-white shadow-sm' : 'text-[#52645B]' }}">Service</button>
            </div>
        </div>
    </div>

    <!-- Products Table (Scaled UP Rows & Headers) -->
    <div class="bg-white border border-[#E3EEE8] rounded-3xl shadow-emco overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-[#F3F6F4] border-b border-[#E3EEE8] text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                    <tr>
                        <th class="py-4 px-5">Produk</th>
                        <th class="py-4 px-5">Tipe</th>
                        <th class="py-4 px-5">Kategori & Brand</th>
                        @if(auth()->user()->can('cost_price.view'))
                            <th class="py-4 px-5 text-right">Modal (HPP)</th>
                        @endif
                        <th class="py-4 px-5 text-right">Harga Jual</th>
                        <th class="py-4 px-5 text-center">Status Harga</th>
                        <th class="py-4 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($products as $product)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3.5">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-2xl bg-slate-100 border border-slate-200/80 shrink-0">
                                    <div>
                                        <div class="font-bold text-[#232E28] text-sm leading-snug tracking-tight">{{ $product->name }}</div>
                                        <div class="text-xs font-mono text-[#718379] mt-0.5">
                                            <span class="bg-[#E3EEE8] text-[#3F7A5D] px-2 py-0.5 rounded-md font-bold">SKU: {{ $product->code }}</span>
                                            @if($product->effective_barcode) &bull; Barcode: {{ $product->effective_barcode }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-[#C2AC7C]/20 text-[#8F794B] border border-[#C2AC7C]/40') }}">
                                    {{ $product->product_type === 'PHYSICAL' ? '📦 FISIK' : ($product->product_type === 'DIGITAL' ? '⚡ DIGITAL/PULSA' : '🛠️ SERVICE') }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-[#232E28] font-semibold text-sm">
                                {{ $product->category?->name ?? '-' }} / <span class="text-[#718379] font-normal">{{ $product->brand?->name ?? '-' }}</span>
                            </td>
                            @if(auth()->user()->can('cost_price.view'))
                                <td class="py-4 px-5 text-right font-mono font-bold text-[#232E28] text-sm">
                                    Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                </td>
                            @endif
                            <td class="py-4 px-5 text-right font-mono font-extrabold text-[#3F7A5D] text-base">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wider {{ $product->price_status === 'COMPLETE' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $product->price_status }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEditModal({{ $product->id }})" class="px-3.5 py-2 bg-[#F3F6F4] hover:bg-slate-200 text-[#232E28] rounded-xl text-xs font-bold transition">
                                        Edit
                                    </button>
                                    <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus produk ini?" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-medium text-sm">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#E3EEE8]">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-xl w-full shadow-2xl space-y-4 border border-slate-100 overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between border-b pb-3.5">
                    <h3 class="text-lg font-extrabold text-[#232E28]">
                        {{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveProduct" class="space-y-4 text-xs font-semibold">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Kode SKU *</label>
                            <input type="text" wire:model="code" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Barcode scanner</label>
                            <input type="text" wire:model="barcode" placeholder="Opsional" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#232E28] font-bold mb-1">Nama Produk *</label>
                        <input type="text" wire:model="name" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-bold" required />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Tipe Produk</label>
                            <select wire:model="product_type" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="PHYSICAL">📦 Fisik</option>
                                <option value="DIGITAL">⚡ Digital</option>
                                <option value="SERVICE">🛠️ Service</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Kategori</label>
                            <select wire:model="category_id" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="">-- Pilih --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Brand</label>
                            <select wire:model="brand_id" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="">-- Pilih --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Harga Modal (HPP) *</label>
                            <input type="number" wire:model="cost_price" class="w-full p-3 border border-slate-300 rounded-2xl font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Harga Jual Kasir *</label>
                            <input type="number" wire:model="selling_price" class="w-full p-3 border border-slate-300 rounded-2xl font-mono text-[#3F7A5D] font-extrabold text-base" required />
                        </div>
                    </div>

                    @if(!$editingProductId && $product_type === 'PHYSICAL')
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Stok Awal Fisik</label>
                            <input type="number" wire:model="initial_stock" class="w-full p-3 border border-slate-300 rounded-2xl font-mono font-bold" />
                        </div>
                    @endif

                    <div class="pt-3 flex gap-3">
                        <button type="submit" class="flex-1 py-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl transition shadow-emco-primary text-xs uppercase tracking-wider">
                            Simpan Produk
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-4 px-5 bg-slate-100 text-slate-700 font-semibold rounded-2xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import CSV Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3.5">
                    <h3 class="text-lg font-extrabold text-[#232E28]">Import Produk dari File CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processImport" class="space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-[#232E28] font-bold mb-1">Pilih File CSV</label>
                        <input type="file" wire:model="importFile" class="w-full p-3 border border-slate-300 rounded-2xl bg-[#F3F6F4] font-medium" required />
                    </div>

                    <div class="bg-[#E3EEE8] text-[#3F7A5D] p-4 rounded-2xl text-xs leading-relaxed font-medium">
                        💡 Auto-create Kategori & Brand jika belum ada di database. HPP & Harga Jual 0 otomatis ditandai `INCOMPLETE`.
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 py-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl transition shadow-sm uppercase tracking-wider text-xs">
                            Mulai Import Data
                        </button>
                        <button type="button" wire:click="$set('showImportModal', false)" class="py-4 px-5 bg-slate-100 text-slate-700 font-semibold rounded-2xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
