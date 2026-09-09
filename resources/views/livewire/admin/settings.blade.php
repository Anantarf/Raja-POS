<div class="space-y-6">
    <!-- Page Header Banner -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/10 via-[#3F7A5D]/5 to-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-0.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">Pengaturan Toko &amp; Sistem</h1>
            <p class="text-xs sm:text-sm text-[#52645B] font-medium leading-relaxed">Kelola profil toko, pengguna sistem, role &amp; hak akses, metode pembayaran, dan lokasi cabang.</p>
        </div>

        <div class="flex items-center gap-2 text-xs font-extrabold text-[#3F7A5D] bg-[#E3EEE8] px-3 py-2 rounded-xl border border-[#3F7A5D]/30 shrink-0">
            <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <span>Akses Hak Khusus Owner</span>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 pb-3 text-xs sm:text-sm font-extrabold overflow-x-auto no-scrollbar whitespace-nowrap">
        @foreach([
            'STORE_SETTINGS' => ['Profil Toko', '/admin/settings/store-settings'],
            'USERS' => ['Pengguna & Akses', '/admin/settings/users'],
            'ROLES' => ['Role & Hak Akses', '/admin/settings/roles'],
            'PAYMENT_METHODS' => ['Metode Pembayaran', '/admin/settings/payment-methods'],
            'LOCATIONS' => ['Lokasi Cabang', '/admin/settings/locations'],
        ] as $tab => [$label, $href])
            <a href="{{ $href }}" class="px-3 py-2 rounded-xl transition-all duration-200 shrink-0 {{ $activeTab === $tab ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#2C3E35]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Tab 1: Store Settings -->
    @if($activeTab === 'STORE_SETTINGS')
        @php
            $settingMap = $settings->pluck('value', 'key');
            $storeSettings = [
                ['label' => 'Nama Toko', 'value' => $settingMap->get('store_name', 'Raja Aksesoris'), 'hint' => 'Nama resmi toko yang tampil di sistem dan struk belanja.'],
                ['label' => 'Mata Uang', 'value' => $settingMap->get('currency', 'Rupiah (Rp)'), 'hint' => 'Format simbol mata uang untuk transaksi & laporan.'],
                ['label' => 'Zona Waktu', 'value' => $settingMap->get('timezone', 'Asia/Jakarta (WIB)'), 'hint' => 'Acuan waktu tanggal transaksi, laporan harian, dan audit.'],
                ['label' => 'Mode Transaksi', 'value' => 'Multi-Payment & Split Account', 'hint' => 'Mendukung Tunai, Bank, QRIS, & E-Wallet dalam 1 transaksi.'],
                ['label' => 'Validasi Stok', 'value' => 'Server-Side Strict Guard', 'hint' => 'Mencegah transaksi jika stok barang fisik di toko kosong.'],
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Main Panel -->
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-5">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3.5">
                    <div>
                        <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Profil &amp; Preferensi Toko</h3>
                        <p class="text-sm text-[#718379] font-medium mt-0.5">Konfigurasi dasar operasional kasir, cetak struk, dan pencatatan laporan.</p>
                    </div>
                    <span class="px-3 py-1 rounded-lg bg-[#E3EEE8] text-[#3F7A5D] text-xs font-extrabold uppercase tracking-wider border border-[#3F7A5D]/20">Aktif &amp; Berjalan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach($storeSettings as $item)
                        <div class="rounded-xl border border-slate-200/80 bg-[#F3F6F4]/60 hover:border-[#3F7A5D]/30 p-4 space-y-1.5 transition-all">
                            <div class="text-xs font-extrabold uppercase tracking-wider text-[#3F7A5D]">{{ $item['label'] }}</div>
                            <div class="text-base font-extrabold text-[#2C3E35] font-mono tracking-tight">{{ filled($item['value']) ? $item['value'] : 'Belum diisi' }}</div>
                            <div class="text-sm text-[#718379] leading-relaxed font-medium">{{ $item['hint'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Info Panel -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3.5">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Panduan &amp; Spesifikasi Sistem</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Ringkasan status integrasi operasional toko.</p>
                </div>

                <div class="space-y-3.5 text-sm font-medium">
                    <div class="p-3.5 rounded-xl bg-[#E3EEE8]/60 border border-[#3F7A5D]/30 text-[#3F7A5D] leading-relaxed font-semibold">
                        Perubahan preferensi toko ini berlaku otomatis di seluruh terminal kasir dan laporan finansial.
                    </div>
                    <div class="space-y-2.5 pt-1">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-[#718379] font-bold">Kertas Printer Struk:</span>
                            <span class="font-extrabold text-[#2C3E35] font-mono">{{ $settingMap->get('receipt_paper_width', '80mm') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-[#718379] font-bold">Acuan Zona Waktu:</span>
                            <span class="font-extrabold text-[#2C3E35] font-mono">Asia/Jakarta (WIB)</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-[#718379] font-bold">Status Server Toko:</span>
                            <span class="font-extrabold text-[#3F7A5D] font-mono uppercase">ONLINE &amp; SYNCED</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Tab 2: USERS -->
    @elseif($activeTab === 'USERS')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Table Panel (60% Golden Width) -->
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Daftar Pengguna Sistem</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Petugas kasir dan pengelola toko yang memiliki hak akses login ke sistem.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Username Login</th>
                                <th class="py-3.5 px-4">Nama Lengkap</th>
                                <th class="py-3.5 px-4">Role / Jabatan</th>
                                <th class="py-3.5 px-4 text-center">Status Akun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($users as $user)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $user->username }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $user->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-[#52645B]">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $user->role?->name ?? 'Kasir' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Pengguna (40% Golden Width) -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Pengguna Baru</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan petugas kasir atau admin baru.</p>
                </div>
                <form wire:submit.prevent="addUser" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="userName" placeholder="Budi Santoso" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Username Login *</label>
                        <input type="text" wire:model="userUsername" placeholder="kasir_budi" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-mono font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Password *</label>
                        <input type="password" wire:model="userPassword" placeholder="••••••••" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Role / Wewenang *</label>
                        <select wire:model="userRoleId" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer" required>
                            <option value="">Pilih Role / Wewenang</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Lokasi Kerja *</label>
                        <select wire:model="userLocationId" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer" required>
                            <option value="">Pilih Lokasi Kerja</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Pengguna Sistem
                    </button>
                </form>
            </div>
        </div>

    <!-- Tab 3: ROLES -->
    @elseif($activeTab === 'ROLES')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Role &amp; Wewenang Akses</h3>
                <p class="text-sm text-[#718379] mt-0.5 font-medium">Tingkatan jabatan dan batas wewenang operasional pengguna dalam sistem POS.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Role / Jabatan</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Pengguna Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($roles as $role)
                            <tr class="hover:bg-[#F3F6F4]/60 transition">
                                <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                    {{ $role->name }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-extrabold text-[#3F7A5D] text-sm">
                                    {{ number_format($role->users_count, 0, ',', '.') }} Pengguna
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Tab 4: Locations -->
    @elseif($activeTab === 'LOCATIONS')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Daftar Lokasi Cabang Toko</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Cabang dan lokasi tempat operasional transaksi POS berlangsung.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Kode Cabang</th>
                                <th class="py-3.5 px-4">Nama Cabang Toko</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($locations as $loc)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $loc->code }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $loc->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $loc->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Lokasi -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Cabang Toko</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan lokasi outlet baru.</p>
                </div>
                <form wire:submit.prevent="addLocation" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Kode Cabang *</label>
                        <input type="text" wire:model="locationCode" placeholder="RAJA-BANGO" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl font-mono uppercase bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Cabang Toko *</label>
                        <input type="text" wire:model="locationName" placeholder="Raja Aksesoris Bango" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Cabang Toko
                    </button>
                </form>
            </div>
        </div>

    <!-- Tab 5: Payment Methods -->
    @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Metode Pembayaran Toko</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Opsi cara pembayaran yang tersedia bagi pelanggan di kasir.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Nama Metode</th>
                                <th class="py-3.5 px-4">Kategori Tipe</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($paymentMethods as $pm)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $pm->name }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $pm->type }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $pm->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Metode Pembayaran -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Metode Pembayaran</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan opsi pembayaran baru.</p>
                </div>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Metode *</label>
                        <input type="text" wire:model="pmName" placeholder="Transfer Bank BCA / QRIS" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Kategori Tipe *</label>
                        <select wire:model="pmType" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer">
                            <option value="CASH">Tunai (Cash)</option>
                            <option value="QRIS">QRIS (Nontunai)</option>
                            <option value="TRANSFER">Transfer Bank</option>
                            <option value="E_WALLET">E-Wallet (Dompet Digital)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Metode Pembayaran
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
