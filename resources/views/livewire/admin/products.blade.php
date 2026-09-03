<div class="space-y-5">
    <!-- Header Controls (Clean Single-Row Enterprise Alignment) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Master Produk</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Kelola data barang fisik, produk digital, dan layanan dalam format Excel ritel terpadu.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <!-- Unduh Template Excel -->
            <a href="/admin/products/template-excel" title="Unduh Template Excel" class="px-3 py-2 bg-white hover:bg-slate-50 text-[#232E28] font-bold border border-slate-200 rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95 shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Template</span>
            </a>

            <!-- Export Data Excel -->
            <a href="/admin/products/export-excel?category_id={{ $selectedCategory }}&type={{ $selectedType }}" title="Export Data Produk" class="px-3 py-2 bg-white hover:bg-slate-50 text-[#232E28] font-bold border border-slate-200 rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95 shrink-0">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export</span>
            </a>

            <!-- Import CSV / Excel -->
            <button wire:click="$set('showImportModal', true)" title="Import File CSV / Excel" class="px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Import</span>
            </button>

            <!-- Tambah Barang/Layanan -->
            <button wire:click="openCreateModal" class="px-3.5 py-2 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Barang/Layanan</span>
            </button>
        </div>
    </div>

    <!-- Filter & View Mode Toolbar (Clean Balanced Flex Layout) -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <!-- Search Input -->
        <div class="w-full md:w-72 relative shrink-0">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari barang, barcode, SKU..."
                class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="w-full md:w-auto flex flex-wrap items-center justify-between md:justify-end gap-2.5">
            <!-- Category Filter Dropdown -->
            <select wire:model.live="selectedCategory" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold bg-white text-[#232E28] focus:ring-2 focus:ring-[#3F7A5D]/20 shrink-0">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Jenis Stok Filter Pills -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl shrink-0">
                <button type="button" @click="$wire.filterType('ALL')" wire:click="filterType('ALL')" class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $selectedType === 'ALL' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Semua</button>
                <button type="button" @click="$wire.filterType('PHYSICAL')" wire:click="filterType('PHYSICAL')" class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $selectedType === 'PHYSICAL' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Fisik</button>
                <button type="button" @click="$wire.filterType('DIGITAL')" wire:click="filterType('DIGITAL')" class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $selectedType === 'DIGITAL' ? 'bg-emerald-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Digital</button>
                <button type="button" @click="$wire.filterType('SERVICE')" wire:click="filterType('SERVICE')" class="px-3 py-1 rounded-lg font-bold text-xs transition cursor-pointer {{ $selectedType === 'SERVICE' ? 'bg-[#C2AC7C] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Service</button>
            </div>

            <!-- View Mode Switcher (Card vs Table) -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/60 shrink-0">
                <button
                    type="button"
                    @click="$wire.setViewMode('card')"
                    wire:click="setViewMode('card')"
                    title="Tampilan Kartu Grid"
                    class="p-1.5 rounded-lg transition cursor-pointer {{ $viewMode === 'card' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-400 hover:text-slate-700' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="$wire.setViewMode('table')"
                    wire:click="setViewMode('table')"
                    title="Tampilan Tabel List"
                    class="p-1.5 rounded-lg transition cursor-pointer {{ $viewMode === 'table' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-400 hover:text-slate-700' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN PRODUCT CONTENT: CARD GRID VIEW (Default) -->
    @if($viewMode === 'card')
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($products as $product)
                @php
                    $isIncomplete = $product->cost_price <= 0 || $product->selling_price <= 0;
                    $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->first() : null;
                    $stockQty = $inv?->quantity ?? 0;
                    $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                @endphp

                <div class="bg-white rounded-2xl border {{ $isIncomplete ? 'border-rose-200 bg-rose-50/10' : 'border-slate-200/80' }} p-4 shadow-sm hover:shadow-md hover:border-[#3F7A5D]/50 transition-all duration-200 flex flex-col justify-between h-[350px] group">
                    <div class="space-y-2.5">
                        <!-- Top Metadata & Type Badge -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800') }}">
                                {{ $product->product_type === 'PHYSICAL' ? 'Fisik' : ($product->product_type === 'DIGITAL' ? 'Digital' : 'Service') }}
                            </span>

                            @if($product->product_type === 'PHYSICAL')
                                <span class="font-semibold text-slate-500 text-xs">
                                    Stok: <strong class="{{ $stockStatus === 'OUT_OF_STOCK' ? 'text-rose-600' : 'text-slate-800' }}">{{ $stockQty }}</strong>
                                </span>
                            @else
                                <span class="font-mono text-[11px] text-slate-400">SKU: {{ $product->code }}</span>
                            @endif
                        </div>

                        <!-- Product Image / Initials Banner -->
                        <div class="h-28 bg-[#F3F6F4]/80 rounded-xl relative flex items-center justify-center overflow-hidden border border-slate-100/80">
                            @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                            @else
                                <span class="text-2xl font-mono font-extrabold text-[#3F7A5D]/80 tracking-wider">
                                    {{ strtoupper(substr($product->code, 0, 2)) }}
                                </span>
                            @endif
                        </div>

                        <!-- Product Title & Barcode -->
                        <div>
                            <h3 class="font-bold text-[#232E28] text-sm leading-snug line-clamp-2 group-hover:text-[#3F7A5D] transition-colors">
                                {{ $product->name }}
                            </h3>
                            <div class="text-xs font-mono text-slate-400 mt-0.5">
                                Barcode: {{ $product->effective_barcode }}
                            </div>
                        </div>

                        <!-- 3 Dimensions: Kategori • Jenis • Merk (Clean Text Dots) -->
                        <div class="text-xs text-slate-500 flex items-center gap-1.5 flex-wrap font-medium">
                            <span class="text-slate-700 font-semibold">{{ $product->category?->name ?? 'Umum' }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-indigo-600 font-bold">{{ $product->product_subtype ?: '-' }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-slate-600">{{ $product->brand?->name ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Card Footer: Prices & Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-end justify-between gap-2">
                        <div>
                            @if(auth()->user()->can('cost_price.view'))
                                <div class="text-[11px] text-slate-500 font-medium">
                                    Modal: 
                                    @if($product->cost_price > 0)
                                        <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="font-bold text-rose-500">*Harus dilengkapi</span>
                                    @endif
                                </div>
                            @endif

                            <div class="text-base font-extrabold text-[#232E28] font-mono tracking-tight">
                                @if($product->selling_price > 0)
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                @else
                                    <span class="text-xs font-bold text-rose-600">*Harga jual belum diisi</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button wire:click="openEditModal({{ $product->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 rounded-lg text-xs font-bold transition">
                                Edit
                            </button>
                            <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus barang/layanan ini?" class="px-2.5 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-lg text-xs font-bold transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                    <div class="font-bold text-[#232E28] text-base mb-1">Tidak ada barang/layanan ditemukan.</div>
                    <div class="text-xs text-[#718379]">Gunakan kata kunci pencarian lain atau tambah data barang baru.</div>
                </div>
            @endforelse
        </div>

    <!-- MAIN PRODUCT CONTENT: TABLE LIST VIEW -->
    @else
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Barang / Layanan</th>
                            <th class="py-3.5 px-4">Jenis Stok</th>
                            <th class="py-3.5 px-4">Kategori &bull; Jenis &bull; Merk</th>
                            @if(auth()->user()->can('cost_price.view'))
                                <th class="py-3.5 px-4 text-right">Modal</th>
                            @endif
                            <th class="py-3.5 px-4 text-right">Harga Jual</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($products as $product)
                            <tr class="hover:bg-[#F3F6F4]/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                            <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-9 h-9 object-cover rounded-xl bg-slate-100 border border-slate-200/80 shrink-0">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-[#F3F6F4] border border-slate-200/80 text-[#3F7A5D] font-mono font-extrabold text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($product->code, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-[#232E28] text-sm leading-snug tracking-tight">{{ $product->name }}</div>
                                            <div class="text-xs font-mono text-slate-400 mt-0.5">
                                                Barcode: {{ $product->effective_barcode }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800') }}">
                                        {{ $product->product_type === 'PHYSICAL' ? 'Fisik' : ($product->product_type === 'DIGITAL' ? 'Digital' : 'Service') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-xs">
                                    <span class="text-slate-700 font-semibold">{{ $product->category?->name ?? 'Umum' }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-indigo-600 font-bold">{{ $product->product_subtype ?: '-' }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-600">{{ $product->brand?->name ?? '-' }}</span>
                                </td>
                                @if(auth()->user()->can('cost_price.view'))
                                    <td class="py-3.5 px-4 text-right font-mono font-semibold text-xs">
                                        @if($product->cost_price > 0)
                                            <span class="text-slate-700">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="font-bold text-rose-500">*Harus dilengkapi</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#232E28] text-sm">
                                    @if($product->selling_price > 0)
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-xs font-bold text-rose-600">*Harga jual belum diisi</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="openEditModal({{ $product->id }})" class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 rounded-lg text-xs font-bold transition">
                                            Edit
                                        </button>
                                        <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus barang/layanan ini?" class="px-2.5 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 rounded-lg text-xs font-bold transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 font-medium text-xs">Tidak ada barang/layanan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Pagination Container -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        {{ $products->links() }}
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-xl w-full shadow-xl space-y-4 border border-slate-100 overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between border-b pb-3.5">
                    <h3 class="text-lg font-extrabold text-[#232E28]">
                        {{ $editingProductId ? 'Edit Barang / Layanan' : 'Tambah Barang / Layanan Baru' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveProduct" class="space-y-4 text-xs font-semibold">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Kode / Barcode *</label>
                            <input type="text" wire:model="code" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Barcode Fisik (Scan)</label>
                            <input type="text" wire:model="barcode" placeholder="Opsional" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#232E28] font-bold mb-1">Nama Barang/Layanan *</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Casing Premium Softcase / Top Up Saldo DANA" class="w-full p-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-bold" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Jenis Stok *</label>
                            <select wire:model="product_type" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="PHYSICAL">Fisik (Barang Stok)</option>
                                <option value="DIGITAL">Digital (Pulsa/E-Wallet/Voucher)</option>
                                <option value="SERVICE">Service (Jasa / Layanan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Jenis (Subtipe)</label>
                            <input type="text" wire:model="product_subtype" placeholder="Contoh: KABEL DATA, MULTI, TRANSFER" class="w-full p-3 border border-slate-300 rounded-2xl font-bold" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Kategori</label>
                            <select wire:model="category_id" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Merk / Brand</label>
                            <select wire:model="brand_id" class="w-full p-3 border border-slate-300 rounded-2xl bg-white font-bold">
                                <option value="">-- Pilih Merk / Brand --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Modal *</label>
                            <input type="number" wire:model="cost_price" placeholder="0" class="w-full p-3 border border-slate-300 rounded-2xl font-mono font-bold" required />
                        </div>
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Harga Jual *</label>
                            <input type="number" wire:model="selling_price" placeholder="0" class="w-full p-3 border border-slate-300 rounded-2xl font-mono text-[#232E28] font-extrabold text-base" required />
                        </div>
                    </div>

                    @if(!$editingProductId && $product_type === 'PHYSICAL')
                        <div>
                            <label class="block text-[#232E28] font-bold mb-1">Stok Awal Fisik</label>
                            <input type="number" wire:model="initial_stock" placeholder="0" class="w-full p-3 border border-slate-300 rounded-2xl font-mono font-bold" />
                        </div>
                    @endif

                    <div class="pt-3 flex gap-3">
                        <button type="submit" class="flex-1 py-4 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl transition shadow-emco-primary text-xs uppercase tracking-wider">
                            Simpan Barang/Layanan
                        </button>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="py-4 px-5 bg-slate-100 text-slate-700 font-semibold rounded-2xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import CSV / Excel Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3.5">
                    <h3 class="text-lg font-extrabold text-[#232E28]">Import Data Barang dari File Excel / CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processImport" class="space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-[#232E28] font-bold mb-1">Pilih File CSV / Excel (.csv)</label>
                        <input type="file" wire:model="importFile" class="w-full p-3 border border-slate-300 rounded-2xl bg-[#F3F6F4] font-medium" required />
                    </div>

                    <div class="bg-[#E3EEE8] text-[#3F7A5D] p-4 rounded-2xl text-xs leading-relaxed font-medium space-y-1">
                        <div class="font-bold">Informasi Mapping Format Excel:</div>
                        <div>&bull; <strong>Nama Barang/Layanan</strong>, <strong>Jenis Stok</strong> (Fisik/Digital/Service), <strong>Jenis</strong>, <strong>Kategori</strong>, dan <strong>Merk</strong>.</div>
                        <div>&bull; Kategori dan Merk otomatis dibuat jika belum ada di database.</div>
                        <div>&bull; Modal atau Harga Jual 0 otomatis ditandai butuh dilengkapi.</div>
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="submit" class="flex-1 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl transition shadow-sm text-xs uppercase tracking-wider">
                            Mulai Import Data
                        </button>
                        <button type="button" wire:click="$set('showImportModal', false)" class="py-3.5 px-5 bg-slate-100 text-slate-700 font-semibold rounded-2xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
