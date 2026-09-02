<div class="space-y-5">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Pengaturan Toko & Master Cabang</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola lokasi cabang toko dan metode pembayaran kasir.</p>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-bold pb-2">
        <button wire:click="$set('activeTab', 'LOCATIONS')" class="px-4 py-2 rounded-xl transition {{ $activeTab === 'LOCATIONS' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            🏢 Master Lokasi / Cabang Toko
        </button>
        <button wire:click="$set('activeTab', 'PAYMENT_METHODS')" class="px-4 py-2 rounded-xl transition {{ $activeTab === 'PAYMENT_METHODS' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            💳 Master Metode Pembayaran
        </button>
    </div>

    @if($activeTab === 'LOCATIONS')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Daftar Lokasi Cabang Toko</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Kode</th><th class="py-2.5 px-3">Nama Lokasi</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($locations as $loc)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-bold">{{ $loc->code }}</td>
                                <td class="py-2.5 px-3 font-bold text-slate-900">{{ $loc->name }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">Aktif</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Cabang Toko Baru</h3>
                <form wire:submit.prevent="addLocation" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Kode Cabang *</label>
                        <input type="text" wire:model="locationCode" placeholder="contoh: BNG-01" class="w-full p-2.5 border rounded-xl font-mono uppercase" required />
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Nama Lokasi Toko *</label>
                        <input type="text" wire:model="locationName" placeholder="contoh: Raja Aksesoris Bango" class="w-full p-2.5 border rounded-xl" required />
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl">Tambah Cabang</button>
                </form>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Daftar Metode Pembayaran</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Nama Metode</th><th class="py-2.5 px-3">Tipe Pembayaran</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($paymentMethods as $pm)
                            <tr>
                                <td class="py-2.5 px-3 font-bold text-slate-900">{{ $pm->name }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-blue-600">{{ $pm->type }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">Aktif</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Metode Pembayaran</h3>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold mb-1">Nama Metode *</label>
                        <input type="text" wire:model="pmName" placeholder="contoh: Transfer BCA" class="w-full p-2.5 border rounded-xl" required />
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tipe Pembayaran</label>
                        <select wire:model="pmType" class="w-full p-2.5 border rounded-xl bg-white font-semibold">
                            <option value="CASH">Tunai (CASH)</option>
                            <option value="QRIS">QRIS</option>
                            <option value="TRANSFER">Transfer Bank</option>
                            <option value="E_WALLET">E-Wallet</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl">Tambah Metode</button>
                </form>
            </div>
        </div>
    @endif
</div>
