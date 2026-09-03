<div class="space-y-5">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Pengaturan Owner</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Pengaturan khusus Owner untuk user, role & hak akses, metode pembayaran, lokasi toko, dan pengaturan toko.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 text-xs font-bold pb-2">
        @foreach([
            'STORE_SETTINGS' => ['Pengaturan Toko', '/admin/settings/store-settings'],
            'USERS' => ['User', '/admin/settings/users'],
            'ROLES' => ['Role & Hak Akses', '/admin/settings/roles'],
            'PAYMENT_METHODS' => ['Metode Pembayaran', '/admin/settings/payment-methods'],
            'LOCATIONS' => ['Lokasi Toko', '/admin/settings/locations'],
        ] as $tab => [$label, $href])
            <a href="{{ $href }}" class="px-4 py-2 rounded-xl transition {{ $activeTab === $tab ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
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
            <div class="xl:col-span-2 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Profil & Preferensi Toko</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Ringkasan pengaturan dasar yang dipakai kasir, struk, laporan, dan stok.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-extrabold">Aktif</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($storeSettings as $item)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">{{ $item['label'] }}</div>
                            <div class="mt-1.5 text-sm font-extrabold text-slate-900">{{ filled($item['value']) ? $item['value'] : 'Belum diisi' }}</div>
                            <div class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $item['hint'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Catatan Operasional</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Perubahan pengaturan toko sebaiknya dibatasi untuk Owner karena berdampak ke transaksi dan laporan.</p>
                </div>
                <div class="space-y-2 text-xs font-semibold text-slate-600">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2"><span>Struk kasir</span><span class="text-slate-900">{{ $settingMap->get('receipt_paper_width', '-') }}</span></div>
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2"><span>Laporan waktu</span><span class="text-slate-900">{{ $settingMap->get('timezone', '-') }}</span></div>
                    <div class="flex items-center justify-between gap-3"><span>Format nominal</span><span class="text-slate-900">{{ $settingMap->get('currency', '-') }}</span></div>
                </div>
            </div>
        </div>    @elseif($activeTab === 'USERS')
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                    <tr><th class="py-2.5 px-3">Username</th><th class="py-2.5 px-3">Nama</th><th class="py-2.5 px-3">Role</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                </thead>
                <tbody class="divide-y font-medium">
                    @foreach($users as $user)
                        <tr>
                            <td class="py-2.5 px-3 font-mono font-bold">{{ $user->username }}</td>
                            <td class="py-2.5 px-3 font-bold text-slate-900">{{ $user->name }}</td>
                            <td class="py-2.5 px-3">{{ $user->role?->name ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">{{ $user->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($activeTab === 'ROLES')
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                    <tr><th class="py-2.5 px-3">Role</th><th class="py-2.5 px-3 text-center">Jumlah User</th></tr>
                </thead>
                <tbody class="divide-y font-medium">
                    @foreach($roles as $role)
                        <tr><td class="py-2.5 px-3 font-mono font-bold">{{ $role->name }}</td><td class="py-2.5 px-3 text-center font-bold">{{ $role->users_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($activeTab === 'LOCATIONS')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Lokasi Toko</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Kode</th><th class="py-2.5 px-3">Nama</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($locations as $loc)
                            <tr><td class="py-2.5 px-3 font-mono font-bold">{{ $loc->code }}</td><td class="py-2.5 px-3 font-bold text-slate-900">{{ $loc->name }}</td><td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">{{ $loc->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Lokasi Toko</h3>
                <form wire:submit.prevent="addLocation" class="space-y-3 text-xs">
                    <input type="text" wire:model="locationCode" placeholder="RAJA-BANGO" class="w-full p-2.5 border rounded-xl font-mono uppercase" required />
                    <input type="text" wire:model="locationName" placeholder="Raja Aksesoris Bango" class="w-full p-2.5 border rounded-xl" required />
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] text-white font-bold rounded-xl">Tambah Lokasi Toko</button>
                </form>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Metode Pembayaran</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Nama</th><th class="py-2.5 px-3">Tipe</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($paymentMethods as $pm)
                            <tr><td class="py-2.5 px-3 font-bold text-slate-900">{{ $pm->name }}</td><td class="py-2.5 px-3 font-mono font-bold text-[#3F7A5D]">{{ $pm->type }}</td><td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">{{ $pm->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Metode Pembayaran</h3>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3 text-xs">
                    <input type="text" wire:model="pmName" placeholder="Transfer Bank" class="w-full p-2.5 border rounded-xl" required />
                    <select wire:model="pmType" class="w-full p-2.5 border rounded-xl bg-white font-semibold">
                        <option value="CASH">CASH</option>
                        <option value="QRIS">QRIS</option>
                        <option value="TRANSFER">TRANSFER</option>
                        <option value="E_WALLET">E_WALLET</option>
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] text-white font-bold rounded-xl">Tambah Metode Pembayaran</button>
                </form>
            </div>
        </div>
    @endif
</div>


