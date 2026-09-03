<div class="space-y-5">
    <div>
        <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">Laporan</h1>
        <p class="text-xs text-[#718379] font-medium mt-0.5">Ringkasan Sales, Inventory, Payment, Balance, dan Product sesuai MD utama.</p>
    </div>

    <div class="flex flex-wrap gap-2 border-b border-[#E3EEE8] pb-3 text-xs font-bold">
        @foreach(['sales' => 'Sales', 'inventory' => 'Inventory', 'payment' => 'Payment', 'balance' => 'Balance', 'product' => 'Product'] as $key => $label)
            <a href="/admin/reports/{{ $key }}" class="px-4 py-2 rounded-xl transition {{ $type === $key ? 'bg-[#3F7A5D] text-white' : 'text-[#52645B] hover:bg-[#F3F6F4]' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($type === 'sales')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Revenue</div><div class="text-2xl font-mono font-extrabold mt-2">Rp {{ number_format($metrics['omset'], 0, ',', '.') }}</div></div>
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Gross Profit</div><div class="text-2xl font-mono font-extrabold mt-2">Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}</div></div>
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Transactions</div><div class="text-2xl font-mono font-extrabold mt-2">{{ number_format($salesCount, 0, ',', '.') }}</div></div>
        </div>
    @elseif($type === 'inventory')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Current Stock Records</div><div class="text-2xl font-mono font-extrabold mt-2">{{ number_format($inventoryCount, 0, ',', '.') }}</div></div>
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Low / Out Of Stock</div><div class="text-2xl font-mono font-extrabold text-amber-700 mt-2">{{ number_format($lowStockCount, 0, ',', '.') }}</div></div>
        </div>
    @elseif($type === 'payment')
        <div class="bg-white border border-[#E3EEE8] rounded-2xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] text-[#718379] uppercase text-[10px] font-extrabold"><tr><th class="py-3 px-4">Payment Method</th><th class="py-3 px-4 text-right">Total Amount</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($paymentDistribution as $method => $amount)
                        <tr><td class="py-3 px-4 font-bold">{{ $method }}</td><td class="py-3 px-4 text-right font-mono font-extrabold">Rp {{ number_format($amount, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-10 text-center text-slate-400">Belum ada payment completed.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif($type === 'balance')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($balanceAccounts as $account)
                <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">{{ $account->name }}</div><div class="text-xl font-mono font-extrabold mt-2">Rp {{ number_format($account->current_balance, 0, ',', '.') }}</div></div>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Products</div><div class="text-2xl font-mono font-extrabold mt-2">{{ number_format($productCount, 0, ',', '.') }}</div></div>
            <div class="bg-white border border-[#E3EEE8] rounded-2xl p-5"><div class="text-xs text-[#718379] font-bold uppercase">Harga Incomplete</div><div class="text-2xl font-mono font-extrabold text-rose-600 mt-2">{{ number_format($incompleteProductCount, 0, ',', '.') }}</div></div>
        </div>
    @endif
</div>
