<div class="space-y-6">
    <!-- Sneat Welcome Banner Card -->
    <div class="bg-gradient-to-r from-[#696CFF]/10 via-[#696CFF]/5 to-white border border-[#696CFF]/20 rounded-2xl p-6 shadow-sneat relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="space-y-2">
            <h1 class="text-xl font-extrabold text-[#566A7F] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}! 🎉
            </h1>
            <p class="text-xs text-[#697A8D] max-w-xl leading-relaxed">
                <span class="font-bold text-[#566A7F]">Dashboard Overview:</span> Anda memiliki akses penuh sebagai <span class="font-bold text-[#696CFF]">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir & manajemen ritel Raja Aksesoris POS.
            </p>
            <div class="pt-1">
                <a href="/pos" class="px-4 py-2 bg-[#696CFF] hover:bg-[#5F61E6] text-white font-bold rounded-xl text-xs shadow-sneat-primary inline-flex items-center gap-1.5 transition active:scale-95">
                    <span>Buka Layar Kasir POS</span> &rarr;
                </a>
            </div>
        </div>

        <div class="hidden md:block shrink-0 pr-4">
            <div class="w-24 h-24 rounded-full bg-[#696CFF]/10 border-2 border-[#696CFF]/30 flex items-center justify-center text-4xl shadow-inner">
                🏬
            </div>
        </div>
    </div>

    <!-- Sneat Executive Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Omset -->
        <div class="bg-white rounded-2xl p-5 shadow-sneat border border-slate-100 hover:shadow-sneat-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                    📊
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200">
                    ↑ +100%
                </span>
            </div>
            <div class="text-xs text-[#697A8D] font-bold uppercase tracking-wider">Total Omset</div>
            <div class="text-2xl font-extrabold text-[#566A7F] font-mono tracking-tight mt-1">
                Rp {{ number_format($metrics['omset'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#A1ACB8] mt-1 font-medium">Transaksi Completed</div>
        </div>

        <!-- 2. Gross Profit (RBAC Protection) -->
        <div class="bg-white rounded-2xl p-5 shadow-sneat border border-slate-100 hover:shadow-sneat-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                    💰
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-200">
                    Laba Kotor
                </span>
            </div>
            <div class="text-xs text-[#697A8D] font-bold uppercase tracking-wider">Gross Profit</div>
            @if(auth()->user()->can('report.profit.view'))
                <div class="text-2xl font-extrabold text-amber-600 font-mono tracking-tight mt-1">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#A1ACB8] mt-1 font-medium">Omset dikurangi HPP</div>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2 italic bg-[#F5F5F9] px-3 py-1.5 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Kas & Bank -->
        <div class="bg-white rounded-2xl p-5 shadow-sneat border border-slate-100 hover:shadow-sneat-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    🏦
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200">
                    Aktif
                </span>
            </div>
            <div class="text-xs text-[#697A8D] font-bold uppercase tracking-wider">Total Kas & Bank</div>
            <div class="text-2xl font-extrabold text-[#566A7F] font-mono tracking-tight mt-1">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#A1ACB8] mt-1 font-medium">Saldo riil seluruh akun</div>
        </div>

        <!-- 4. Jumlah Transaksi -->
        <div class="bg-white rounded-2xl p-5 shadow-sneat border border-slate-100 hover:shadow-sneat-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#E7E7FF] text-[#696CFF] flex items-center justify-center font-bold text-lg">
                    🧾
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#696CFF] bg-[#E7E7FF]">
                    Sukses
                </span>
            </div>
            <div class="text-xs text-[#697A8D] font-bold uppercase tracking-wider">Jumlah Transaksi</div>
            <div class="text-2xl font-extrabold text-[#566A7F] font-mono tracking-tight mt-1">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs text-[#A1ACB8] font-normal">Nota</span>
            </div>
            <div class="text-[11px] text-[#A1ACB8] mt-1 font-medium">Nota berhasil diproses</div>
        </div>
    </div>

    <!-- Sneat Daily Sales Trend Table -->
    <div class="bg-white rounded-2xl p-6 shadow-sneat border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-[#566A7F] tracking-tight">Tren Omset Penjualan Harian</h2>
                <p class="text-xs text-[#A1ACB8] font-medium">Ringkasan transaksi 7 hari terakhir.</p>
            </div>
            <a href="/admin/sales" class="text-xs font-bold text-[#696CFF] hover:underline flex items-center gap-1">
                <span>Lihat Seluruh Penjualan</span> &rarr;
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F5F5F9] text-[#A1ACB8] uppercase text-[10px] font-extrabold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">Hari & Tanggal</th>
                        <th class="py-3.5 px-4 text-right">Total Omset Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($dailyTrends['labels'] as $index => $label)
                        <tr class="hover:bg-[#F5F5F9]/60 transition">
                            <td class="py-3.5 px-4 font-bold text-[#566A7F]">{{ $label }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#696CFF] text-sm">
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
