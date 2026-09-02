<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Ringkasan performa penjualan, laba kotor, dan saldo kas toko real-time.</p>
        </div>
        <div class="text-xs font-bold bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm text-slate-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>📅 {{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Executive Stat Cards (Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Omset -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition-all duration-200">
            <div class="flex items-center justify-between text-xs text-slate-500 font-extrabold uppercase tracking-wider mb-1.5">
                <span>Total Omset Penjualan</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-sm font-bold">📊</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">
                Rp {{ number_format($metrics['omset'], 0, ',', '.') }}
            </div>
            <div class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">
                Total transaksi COMPLETED
            </div>
        </div>

        <!-- 2. Gross Profit (RBAC Protection) -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition-all duration-200">
            <div class="flex items-center justify-between text-xs text-slate-500 font-extrabold uppercase tracking-wider mb-1.5">
                <span>Laba Kotor (Gross Profit)</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl text-sm font-bold">💰</span>
            </div>
            @if(auth()->user()->can('report.profit.view'))
                <div class="text-2xl font-extrabold text-amber-600 font-mono tracking-tight">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200">
                    Omset dikurangi HPP/Modal
                </div>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2 italic bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Khusus Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Kas & Bank -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition-all duration-200">
            <div class="flex items-center justify-between text-xs text-slate-500 font-extrabold uppercase tracking-wider mb-1.5">
                <span>Total Akumulasi Saldo</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold">🏦</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200">
                Saldo riil seluruh akun aktif
            </div>
        </div>

        <!-- 4. Jumlah Transaksi -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:border-blue-500 transition-all duration-200">
            <div class="flex items-center justify-between text-xs text-slate-500 font-extrabold uppercase tracking-wider mb-1.5">
                <span>Jumlah Transaksi Sukses</span>
                <span class="p-2 bg-sky-50 text-sky-600 rounded-xl text-sm font-bold">🧾</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs text-slate-500 font-normal">Nota</span>
            </div>
            <div class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200">
                Transaksi berhasil
            </div>
        </div>
    </div>

    <!-- Daily Sales Trend Table & Quick Shortcut -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Tren Omset Penjualan Harian (7 Hari Terakhir)</h2>
            <a href="/admin/sales" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                <span>Lihat Seluruh Penjualan</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Hari & Tanggal</th>
                        <th class="py-3 px-4 text-right">Total Omset Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($dailyTrends['labels'] as $index => $label)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ $label }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-blue-600 text-sm">
                                Rp {{ number_format($dailyTrends['data'][$index] ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-8 text-center text-slate-400 font-medium">Belum ada data penjualan 7 hari terakhir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
