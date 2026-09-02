<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Ringkasan performa penjualan, laba kotor, dan saldo kas toko real-time.</p>
        </div>
        <div class="text-xs font-semibold bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm text-slate-600">
            📅 {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Executive Stat Cards (Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Omset -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold mb-1">
                <span>Total Omset Penjualan</span>
                <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">📊</span>
            </div>
            <div class="text-xl font-extrabold text-slate-900 font-mono">
                Rp {{ number_format($metrics['omset'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-600 font-bold mt-1">
                Total transaksi COMPLETED
            </div>
        </div>

        <!-- 2. Gross Profit (RBAC Protection) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold mb-1">
                <span>Laba Kotor (Gross Profit)</span>
                <span class="p-1.5 bg-amber-50 text-amber-600 rounded-lg">💰</span>
            </div>
            @if(auth()->user()->can('report.profit.view'))
                <div class="text-xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-amber-600 font-bold mt-1">
                    Omset dikurangi HPP/Modal
                </div>
            @else
                <div class="text-sm font-bold text-slate-400 mt-2 italic">
                    [Akses Terbatas - Khusus Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Kas & Bank -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold mb-1">
                <span>Total Akumulasi Saldo</span>
                <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">🏦</span>
            </div>
            <div class="text-xl font-extrabold text-slate-900 font-mono">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-500 font-medium mt-1">
                Saldo riil seluruh akun aktif
            </div>
        </div>

        <!-- 4. Jumlah Transaksi -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold mb-1">
                <span>Jumlah Transaksi Sukses</span>
                <span class="p-1.5 bg-sky-50 text-sky-600 rounded-lg">🧾</span>
            </div>
            <div class="text-xl font-extrabold text-slate-900 font-mono">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs text-slate-500 font-normal">Transaksi</span>
            </div>
            <div class="text-[11px] text-sky-600 font-bold mt-1">
                Transaksi berhasil
            </div>
        </div>
    </div>

    <!-- Daily Sales Trend Table & Quick Shortcut -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Tren Omset Penjualan Harian (7 Hari Terakhir)</h2>
            <a href="/admin/sales" class="text-xs font-bold text-blue-600 hover:underline">
                Lihat Seluruh Penjualan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-y border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold">
                    <tr>
                        <th class="py-2.5 px-4">Hari & Tanggal</th>
                        <th class="py-2.5 px-4 text-right">Total Omset Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($dailyTrends['labels'] as $index => $label)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $label }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-blue-600 text-sm">
                                Rp {{ number_format($dailyTrends['data'][$index] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-6 text-center text-slate-400">Belum ada data penjualan 7 hari terakhir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
