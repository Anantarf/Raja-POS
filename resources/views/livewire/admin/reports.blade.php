<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Laporan System & Analytics</h1>
        <p class="text-xs text-[#718379] font-medium mt-0.5">Ringkasan laporan penjualan, stok, pembayaran, saldo, dan produk terpadu.</p>
    </div>

    <div class="flex flex-wrap gap-2 border-b border-slate-200/80 pb-3 text-xs font-bold">
        @foreach(['sales' => 'Penjualan', 'inventory' => 'Stok', 'payment' => 'Pembayaran', 'balance' => 'Saldo', 'product' => 'Produk'] as $key => $label)
            <a href="/admin/reports/{{ $key }}" class="px-3.5 py-1.5 rounded-xl transition {{ $type === $key ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-slate-600 hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($type === 'sales')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Omzet Penjualan</div><div class="text-2xl font-mono font-extrabold text-[#232E28] mt-2">Rp {{ number_format($metrics['omset'], 0, ',', '.') }}</div></div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Margin Kotor</div><div class="text-2xl font-mono font-extrabold text-[#8F794B] mt-2">Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}</div></div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Jumlah Transaksi</div><div class="text-2xl font-mono font-extrabold text-[#232E28] mt-2">{{ number_format($salesCount, 0, ',', '.') }} Nota</div></div>
        </div>
    @elseif($type === 'inventory')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Data Stok Fisik</div><div class="text-2xl font-mono font-extrabold text-[#232E28] mt-2">{{ number_format($inventoryCount, 0, ',', '.') }} SKU</div></div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Stok Menipis / Habis</div><div class="text-2xl font-mono font-extrabold text-rose-600 mt-2">{{ number_format($lowStockCount, 0, ',', '.') }} SKU</div></div>
        </div>
    @elseif($type === 'payment')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[11px] font-extrabold tracking-wider"><tr><th class="py-3.5 px-4">Metode Pembayaran</th><th class="py-3.5 px-4 text-right">Total Nominal</th></tr></thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($paymentDistribution as $method => $amount)
                        <tr class="hover:bg-[#F3F6F4]/60 transition"><td class="py-3.5 px-4 font-bold text-[#232E28] text-sm">{{ $method }}</td><td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#232E28] text-sm">Rp {{ number_format($amount, 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-10 text-center text-slate-400">Belum ada payment completed.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif($type === 'balance')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($balanceAccounts as $account)
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">{{ $account->name }}</div><div class="text-xl font-mono font-extrabold text-[#232E28] mt-2">Rp {{ number_format($account->current_balance, 0, ',', '.') }}</div></div>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Total Produk</div><div class="text-2xl font-mono font-extrabold text-[#232E28] mt-2">{{ number_format($productCount, 0, ',', '.') }} Barang</div></div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm"><div class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Harga Belum Lengkap</div><div class="text-2xl font-mono font-extrabold text-rose-600 mt-2">{{ number_format($incompleteProductCount, 0, ',', '.') }} Barang</div></div>
        </div>
    @endif
</div>


