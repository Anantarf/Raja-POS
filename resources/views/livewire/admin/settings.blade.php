<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Pengaturan Owner</h1>
        <p class="text-xs text-[#718379] font-medium mt-0.5">Pengaturan khusus Owner untuk user, role & hak akses, metode pembayaran, lokasi toko, dan preferensi toko.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 text-xs font-bold pb-2">
        @foreach([
            'STORE_SETTINGS' => ['Pengaturan Toko', '/admin/settings/store-settings'],
            'USERS' => ['User', '/admin/settings/users'],
            'ROLES' => ['Role & Hak Akses', '/admin/settings/roles'],
            'PAYMENT_METHODS' => ['Metode Pembayaran', '/admin/settings/payment-methods'],
            'LOCATIONS' => ['Lokasi Toko', '/admin/settings/locations'],
        ] as $tab => [$label, $href])
            <a href="{{ $href }}" class="px-3.5 py-1.5 rounded-xl transition {{ $activeTab === $tab ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($activeTab === 'STORE_SETTINGS')
        @php
            $settingMap = $settings->pluck('value', 'key');
            $storeSettings = [
                ['label' => 'Nama Toko', 'value' => $settingMap->get('store_name'), 'hint' => 'Nama yang tampil di sistem dan struk kasir.'],
                ['label' => 'Mata Uang', 'value' => $settingMap->get('currency'), 'hint' => 'Format nominal untuk transaksi dan laporan.'],
                ['label' => 'Zona Waktu', 'value' => $settingMap->get('timezone'), 'hint' => 'Acuan waktu nota, laporan harian, dan audit.'],
                ['label' => 'Lebar Kertas Struk', 'value' => $settingMap->get('receipt_paper_width'), 'hint' => 'Ukuran default cetak struk thermal.'],
                ['label' => 'Batas Stok Minimum', 'value' => $settingMap->get('minimum_stock_default'), 'hint' => 'Patokan awal status stok menipis.'],
            ];
        @endphp

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            <div class="xl:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-base font-extrabold text-[#232E28]">Profil & Preferensi Toko</h3>
                        <p class="text-xs text-[#718379] font-medium mt-0.5">Ringkasan pengaturan dasar yang dipakai kasir, struk, laporan, dan stok.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold">Aktif</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($storeSettings as $item)
                        <div class="rounded-xl border border-slate-200/80 bg-[#F3F6F4]/50 p-4">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider text-[#718379]">{{ $item['label'] }}</div>
                            <div class="mt-1 text-sm font-bold text-[#232E28]">{{ filled($item['value']) ? $item['value'] : 'Belum diisi' }}</div>
                            <div class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $item['hint'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-[#232E28]">Catatan Operasional</h3>
                    <p class="text-xs text-[#718379] font-medium mt-1">Perubahan pengaturan toko sebaiknya dibatasi untuk Owner karena berdampak ke transaksi dan laporan.</p>
                </div>
                <div class="space-y-2 text-xs font-semibold text-[#52645B]">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2"><span>Struk kasir</span><span class="text-[#232E28] font-bold">{{ $settingMap->get('receipt_paper_width', '-') }}</span></div>
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2"><span>Laporan waktu</span><span class="text-[#232E28] font-bold">{{ $settingMap->get('timezone', '-') }}</span></div>
                    <div class="flex items-center justify-between gap-3"><span>Format nominal</span><span class="text-[#232E28] font-bold">{{ $settingMap->get('currency', '-') }}</span></div>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'USERS')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                    <tr><th class="py-3 px-4">Username</th><th class="py-3 px-4">Nama</th><th class="py-3 px-4">Role</th><th class="py-3 px-4 text-center">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($users as $user)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#3F7A5D]">{{ $user->username }}</td>
                            <td class="py-3 px-4 font-bold text-[#232E28]">{{ $user->name }}</td>
                            <td class="py-3 px-4 text-[#52645B]">{{ $user->role?->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20">{{ $user->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($activeTab === 'ROLES')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                    <tr><th class="py-3 px-4">Role</th><th class="py-3 px-4 text-center">Jumlah User</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($roles as $role)
                        <tr class="hover:bg-[#F3F6F4]/60 transition"><td class="py-3 px-4 font-mono font-bold text-[#232E28]">{{ $role->name }}</td><td class="py-3 px-4 text-center font-extrabold text-[#3F7A5D] font-mono">{{ $role->users_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($activeTab === 'LOCATIONS')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200/80 bg-white font-extrabold text-sm text-[#232E28]">Lokasi Toko</div>
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                        <tr><th class="py-3 px-4">Kode</th><th class="py-3 px-4">Nama</th><th class="py-3 px-4 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($locations as $loc)
                            <tr class="hover:bg-[#F3F6F4]/60 transition"><td class="py-3 px-4 font-mono font-bold text-[#3F7A5D]">{{ $loc->code }}</td><td class="py-3 px-4 font-bold text-[#232E28]">{{ $loc->name }}</td><td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20">{{ $loc->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-[#232E28]">Tambah Lokasi Toko</h3>
                <form wire:submit.prevent="addLocation" class="space-y-3 text-xs">
                    <input type="text" wire:model="locationCode" placeholder="RAJA-BANGO" class="w-full p-2.5 border border-slate-200 rounded-xl font-mono uppercase focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    <input type="text" wire:model="locationName" placeholder="Raja Aksesoris Bango" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-xs uppercase tracking-wider cursor-pointer">Tambah Lokasi Toko</button>
                </form>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200/80 bg-white font-extrabold text-sm text-[#232E28]">Metode Pembayaran</div>
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider">
                        <tr><th class="py-3 px-4">Nama</th><th class="py-3 px-4">Tipe</th><th class="py-3 px-4 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($paymentMethods as $pm)
                            <tr class="hover:bg-[#F3F6F4]/60 transition"><td class="py-3 px-4 font-bold text-[#232E28]">{{ $pm->name }}</td><td class="py-3 px-4 font-mono font-extrabold text-[#3F7A5D]">{{ $pm->type }}</td><td class="py-3 px-4 text-center"><span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20">{{ $pm->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-[#232E28]">Tambah Metode Pembayaran</h3>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3 text-xs">
                    <input type="text" wire:model="pmName" placeholder="Transfer Bank" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    <select wire:model="pmType" class="w-full p-2.5 border border-slate-200 rounded-xl bg-white font-bold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                        <option value="CASH">CASH</option>
                        <option value="QRIS">QRIS</option>
                        <option value="TRANSFER">TRANSFER</option>
                        <option value="E_WALLET">E_WALLET</option>
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-xs uppercase tracking-wider cursor-pointer">Tambah Metode Pembayaran</button>
                </form>
            </div>
        </div>
    @endif
</div>
