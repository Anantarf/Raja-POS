<div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Master Produk</h1>
            <p class="text-xs text-[#718379] font-medium mt-1">Kelola data barang fisik, produk digital, dan layanan dalam format Excel ritel terpadu.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/products/template-csv" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-[#232E28] font-bold border border-[#E3EEE8] rounded-2xl text-xs shadow-emco transition flex items-center gap-2 active:scale-95">
                <span>Unduh Template CSV</span>
            </a>
            <button wire:click="$set('showImportModal', true)" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl text-xs shadow-sm transition flex items-center gap-2 active:scale-95">
                <span>Import CSV</span>
            </button>
            <button wire:click="openCreateModal" class="px-4 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-xs shadow-emco-primary transition flex items-center gap-2 active:scale-95">
                <span>+ Tambah Barang/Layanan</span>
            </button>
        </div>
    </div>

    <!-- Filter & View Mode Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-[#E3EEE8] shadow-emco flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
        <!-- Search Input -->
        <div class="w-full md:w-1/3 relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama barang/layanan, SKU, atau barcode..."
                class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4] placeholder:text-[#718379]"
            />
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="w-full md:w-auto flex flex-wrap items-center gap-3">
            <!-- Category Filter -->
            <select wire:model.live="selectedCategory" class="px-4 py-3 border border-slate-200 rounded-2xl text-xs font-bold bg-white text-[#232E28]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Jenis Stok Pills -->
            <div class="flex items-center gap-1.5 bg-[#F3F6F4] p-1.5 rounded-2xl border border-slate-200">
                <button wire:click="$set('selectedType', 'ALL')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs {{ $selectedType === 'ALL' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-[#52645B] hover:text-[#232E28]' }}">Semua Jenis Stok</button>
                <button wire:click="$set('selectedType', 'PHYSICAL')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs {{ $selectedType === 'PHYSICAL' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-[#52645B] hover:text-[#232E28]' }}">Fisik</button>
                <button wire:click="$set('selectedType', 'DIGITAL')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs {{ $selectedType === 'DIGITAL' ? 'bg-emerald-700 text-white shadow-sm' : 'text-[#52645B] hover:text-[#232E28]' }}">Digital</button>
                <button wire:click="$set('selectedType', 'SERVICE')" class="px-3.5 py-1.5 rounded-xl font-bold text-xs {{ $selectedType === 'SERVICE' ? 'bg-[#C2AC7C] text-white shadow-sm' : 'text-[#52645B] hover:text-[#232E28]' }}">Service</button>
            </div>

            <!-- View Mode Switcher (Card vs Table) -->
            <div class="flex items-center gap-1 bg-[#F3F6F4] p-1.5 rounded-2xl border border-slate-200">
                <button
                    wire:click="setViewMode('card')"
                    title="Tampilan Card Grid"
                    class="p-2 rounded-xl transition {{ $viewMode === 'card' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button
                    wire:click="setViewMode('table')"
                    title="Tampilan Tabel List"
                    class="p-2 rounded-xl transition {{ $viewMode === 'table' ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}"
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @forelse($products as $product)
                @php
                    $isIncomplete = $product->price_status === 'INCOMPLETE';
                    $inv = $product->product_type === 'PHYSICAL' ? \App\Models\Inventory::where('product_id', $product->id)->first() : null;
                    $stockQty = $inv?->quantity ?? 0;
                    $stockStatus = $inv?->stock_status ?? 'AVAILABLE';
                @endphp

                <div class="bg-white rounded-3xl border border-[#E3EEE8] hover:border-[#3F7A5D]/40 transition-all duration-200 overflow-hidden flex flex-col justify-between shadow-emco hover:shadow-md group">
                    <div>
                        <!-- Top Header Badges -->
                        <div class="p-3.5 pb-2 flex items-center justify-between gap-2 border-b border-slate-100 bg-[#F3F6F4]/40">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-[#C2AC7C]/20 text-[#8F794B] border border-[#C2AC7C]/40') }}">
                                {{ $product->product_type === 'PHYSICAL' ? 'STOK FISIK' : ($product->product_type === 'DIGITAL' ? 'DIGITAL' : 'SERVICE') }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider {{ $product->price_status === 'COMPLETE' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $product->price_status === 'COMPLETE' ? 'Lengkap' : 'Harga Belum Lengkap' }}
                            </span>
                        </div>

                        <!-- Product Thumbnail / Initials Banner -->
                        <div class="h-28 bg-gradient-to-b from-[#F3F6F4] to-white relative flex items-center justify-center p-3 overflow-hidden">
                            @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-300">
                            @else
                                <span class="text-2xl font-mono font-extrabold text-[#3F7A5D]/80 tracking-wider">
                                    {{ strtoupper(substr($product->code, 0, 2)) }}
                                </span>
                            @endif
                        </div>

                        <!-- Product Main Info -->
                        <div class="p-4 space-y-2.5">
                            <div>
                                <h3 class="font-extrabold text-[#232E28] text-sm leading-snug tracking-tight line-clamp-2 overflow-hidden text-ellipsis group-hover:text-[#3F7A5D] transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-indigo-50 text-indigo-600 border border-indigo-200/70 px-2 py-0.5 rounded-md font-mono font-bold text-[10px]">
                                        Barcode: {{ $product->effective_barcode }}
                                    </span>
                                </div>
                            </div>

                            <!-- Dimensions: Kategori, Jenis, Merk/Brand -->
                            <div class="bg-[#F3F6F4]/60 p-2.5 rounded-2xl space-y-1 text-[11px] font-semibold text-[#232E28] border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-[#718379]">Kategori:</span>
                                    <span class="font-bold text-[#232E28]">{{ $product->category?->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[#718379]">Jenis:</span>
                                    <span class="font-bold text-indigo-600">{{ $product->product_subtype ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[#718379]">Merk / Brand:</span>
                                    <span class="font-bold text-[#232E28]">{{ $product->brand?->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Price, Stock & Actions -->
                    <div class="border-t border-slate-100 p-4 pt-3 bg-white space-y-3">
                        <div class="flex items-end justify-between gap-2">
                            <div>
                                @if(auth()->user()->can('cost_price.view'))
                                    <div class="text-[10px] font-semibold text-[#718379]">
                                        Modal: <span class="font-mono font-bold text-[#232E28]">Rp {{ number_format($product->cost_price ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="text-base font-extrabold text-[#232E28] font-mono tracking-tight mt-0.5">
                                    Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                                </div>
                            </div>

                            @if($product->product_type === 'PHYSICAL')
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] shrink-0 {{ $stockStatus === 'OUT_OF_STOCK' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($stockStatus === 'LOW_STOCK' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-[#E3EEE8] text-[#3F7A5D]') }}">
                                    Stok: {{ $stockQty }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] shrink-0 bg-slate-100 text-[#718379]">
                                    {{ $product->product_type === 'DIGITAL' ? 'DIGITAL' : 'SERVICE' }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                            <button wire:click="openEditModal({{ $product->id }})" class="flex-1 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-extrabold transition border border-indigo-200/80 text-center active:scale-95">
                                Edit
                            </button>
                            <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus barang/layanan ini?" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-extrabold transition border border-rose-200/80 active:scale-95">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-[#E3EEE8]">
                    <div class="font-bold text-[#232E28] text-base mb-1">Tidak ada barang/layanan ditemukan.</div>
                    <div class="text-xs text-[#718379]">Gunakan kata kunci pencarian lain atau tambah data barang baru.</div>
                </div>
            @endforelse
        </div>

    <!-- MAIN PRODUCT CONTENT: TABLE LIST VIEW (Optional) -->
    @else
        <div class="bg-white border border-[#E3EEE8] rounded-3xl shadow-emco overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-[#F3F6F4] border-b border-[#E3EEE8] text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                        <tr>
                            <th class="py-4 px-5">Nama Barang / Layanan</th>
                            <th class="py-4 px-5">Jenis Stok</th>
                            <th class="py-4 px-5">Kategori / Jenis / Merk</th>
                            @if(auth()->user()->can('cost_price.view'))
                                <th class="py-4 px-5 text-right">Modal</th>
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
                                        @if(!empty($product->image_path) && Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path))
                                            <img src="{{ Illuminate\Support\Facades\Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-2xl bg-slate-100 border border-slate-200/80 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-2xl bg-[#E3EEE8] border border-[#3F7A5D]/20 text-[#3F7A5D] font-mono font-extrabold text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($product->code, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-[#232E28] text-sm leading-snug tracking-tight">{{ $product->name }}</div>
                                            <div class="text-xs font-mono text-[#718379] mt-0.5">
                                                <span class="bg-indigo-50 text-indigo-600 border border-indigo-200/70 px-2 py-0.5 rounded-md font-bold text-[11px]">Barcode: {{ $product->effective_barcode }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $product->product_type === 'PHYSICAL' ? 'bg-[#E3EEE8] text-[#3F7A5D]' : ($product->product_type === 'DIGITAL' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-[#C2AC7C]/20 text-[#8F794B] border border-[#C2AC7C]/40') }}">
                                        {{ $product->product_type === 'PHYSICAL' ? 'FISIK' : ($product->product_type === 'DIGITAL' ? 'DIGITAL' : 'SERVICE') }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-[#232E28] font-semibold text-sm">
                                    <div class="font-bold">{{ $product->category?->name ?? '-' }}</div>
                                    <div class="text-xs text-[#718379] mt-0.5">
                                        <span class="text-indigo-600 font-bold">{{ $product->product_subtype ?: '-' }}</span> / {{ $product->brand?->name ?? '-' }}
                                    </div>
                                </td>
                                @if(auth()->user()->can('cost_price.view'))
                                    <td class="py-4 px-5 text-right font-mono font-bold text-[#232E28] text-sm">
                                        Rp {{ number_format($product->cost_price ?? 0, 0, ',', '.') }}
                                    </td>
                                @endif
                                <td class="py-4 px-5 text-right font-mono font-extrabold text-[#232E28] text-sm">
                                    Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wider {{ $product->price_status === 'COMPLETE' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $product->price_status === 'COMPLETE' ? 'Lengkap' : 'Harga Belum Lengkap' }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openEditModal({{ $product->id }})" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-extrabold transition border border-indigo-200/80 active:scale-95">
                                            Edit
                                        </button>
                                        <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Yakin ingin menghapus barang/layanan ini?" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-extrabold transition border border-rose-200/80 active:scale-95">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 font-medium text-sm">Tidak ada barang/layanan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    <div class="bg-white p-4 rounded-3xl border border-[#E3EEE8]">
        {{ $products->links() }}
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-xl w-full shadow-2xl space-y-4 border border-slate-100 overflow-y-auto max-h-[90vh]">
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

    <!-- Import CSV Modal -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-7 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3.5">
                    <h3 class="text-lg font-extrabold text-[#232E28]">Import Data Barang dari File CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processImport" class="space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-[#232E28] font-bold mb-1">Pilih File CSV</label>
                        <input type="file" wire:model="importFile" class="w-full p-3 border border-slate-300 rounded-2xl bg-[#F3F6F4] font-medium" required />
                    </div>

                    <div class="bg-[#E3EEE8] text-[#3F7A5D] p-4 rounded-2xl text-xs leading-relaxed font-medium space-y-1">
                        <div class="font-bold">Informasi Mapping Format Excel:</div>
                        <div>&bull; <strong>Nama Barang/Layanan</strong>, <strong>Jenis Stok</strong> (Fisik/Digital/Service), <strong>Jenis</strong>, <strong>Kategori</strong>, dan <strong>Merk / Brand</strong>.</div>
                        <div>&bull; Kategori dan Merk / Brand otomatis dibuat jika belum ada di database.</div>
                        <div>&bull; Modal atau Harga Jual 0 otomatis ditandai <strong>Harga Belum Lengkap</strong>.</div>
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
