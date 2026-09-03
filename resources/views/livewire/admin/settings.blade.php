<div class="space-y-5">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Owner Settings</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Pengaturan owner untuk Users, Roles, Payment Methods, Locations, dan Store Settings sesuai MD utama.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 text-xs font-bold pb-2">
        @foreach([
            'STORE_SETTINGS' => ['Store Settings', '/admin/settings/store-settings'],
            'USERS' => ['Users', '/admin/settings/users'],
            'ROLES' => ['Roles', '/admin/settings/roles'],
            'PAYMENT_METHODS' => ['Payment Methods', '/admin/settings/payment-methods'],
            'LOCATIONS' => ['Locations', '/admin/settings/locations'],
        ] as $tab => [$label, $href])
            <a href="{{ $href }}" class="px-4 py-2 rounded-xl transition {{ $activeTab === $tab ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($activeTab === 'STORE_SETTINGS')
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                    <tr><th class="py-2.5 px-3">Key</th><th class="py-2.5 px-3">Value</th></tr>
                </thead>
                <tbody class="divide-y font-medium">
                    @foreach($settings as $setting)
                        <tr><td class="py-2.5 px-3 font-mono font-bold">{{ $setting->key }}</td><td class="py-2.5 px-3 font-bold text-slate-900">{{ $setting->value }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($activeTab === 'USERS')
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                    <tr><th class="py-2.5 px-3">Username</th><th class="py-2.5 px-3">Name</th><th class="py-2.5 px-3">Role</th><th class="py-2.5 px-3 text-center">Status</th></tr>
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
                    <tr><th class="py-2.5 px-3">Role</th><th class="py-2.5 px-3 text-center">Users</th></tr>
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
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Locations</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Code</th><th class="py-2.5 px-3">Name</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($locations as $loc)
                            <tr><td class="py-2.5 px-3 font-mono font-bold">{{ $loc->code }}</td><td class="py-2.5 px-3 font-bold text-slate-900">{{ $loc->name }}</td><td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">{{ $loc->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Location</h3>
                <form wire:submit.prevent="addLocation" class="space-y-3 text-xs">
                    <input type="text" wire:model="locationCode" placeholder="RAJA-BANGO" class="w-full p-2.5 border rounded-xl font-mono uppercase" required />
                    <input type="text" wire:model="locationName" placeholder="Raja Aksesoris Bango" class="w-full p-2.5 border rounded-xl" required />
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] text-white font-bold rounded-xl">Tambah Location</button>
                </form>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3">Payment Methods</h3>
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-extrabold border-b">
                        <tr><th class="py-2.5 px-3">Name</th><th class="py-2.5 px-3">Type</th><th class="py-2.5 px-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y font-medium">
                        @foreach($paymentMethods as $pm)
                            <tr><td class="py-2.5 px-3 font-bold text-slate-900">{{ $pm->name }}</td><td class="py-2.5 px-3 font-mono font-bold text-[#3F7A5D]">{{ $pm->type }}</td><td class="py-2.5 px-3 text-center"><span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border">{{ $pm->status }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900">Tambah Payment Method</h3>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3 text-xs">
                    <input type="text" wire:model="pmName" placeholder="Transfer Bank" class="w-full p-2.5 border rounded-xl" required />
                    <select wire:model="pmType" class="w-full p-2.5 border rounded-xl bg-white font-semibold">
                        <option value="CASH">CASH</option>
                        <option value="QRIS">QRIS</option>
                        <option value="TRANSFER">TRANSFER</option>
                        <option value="E_WALLET">E_WALLET</option>
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-[#3F7A5D] text-white font-bold rounded-xl">Tambah Payment Method</button>
                </form>
            </div>
        </div>
    @endif
</div>
