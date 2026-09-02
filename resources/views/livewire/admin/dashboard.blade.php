<div class="space-y-6">
    <!-- EMCO Welcome Banner Card -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/15 via-[#3F7A5D]/5 to-white border border-[#3F7A5D]/20 rounded-2xl p-6 shadow-emco relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="space-y-2">
            <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}! 🌿
            </h1>
            <p class="text-xs text-[#52645B] max-w-xl leading-relaxed">
                <span class="font-bold text-[#232E28]">Dashboard Overview:</span> Anda memiliki akses penuh sebagai <span class="font-bold text-[#3F7A5D]">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir & manajemen ritel Raja Aksesoris POS.
            </p>
            <div class="pt-1">
                <a href="/pos" class="px-4 py-2 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs shadow-emco-primary inline-flex items-center gap-1.5 transition active:scale-95">
                    <span>Buka Layar Kasir POS</span> &rarr;
                </a>
            </div>
        </div>

        <div class="hidden md:block shrink-0 pr-4">
            <div class="w-24 h-24 rounded-full bg-[#3F7A5D]/10 border-2 border-[#3F7A5D]/30 flex items-center justify-center text-4xl shadow-inner">
                🏬
            </div>
        </div>
    </div>

    <!-- EMCO Executive Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Omset -->
        <div class="bg-white rounded-2xl p-5 shadow-emco border border-[#E3EEE8] hover:shadow-emco-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#E3EEE8] text-[#3F7A5D] flex items-center justify-center font-bold text-lg">
                    📊
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8] border border-[#3F7A5D]/20">
                    ↑ +100%
                </span>
            </div>
            <div class="text-xs text-[#86968E] font-bold uppercase tracking-wider">Total Omset</div>
            <div class="text-2xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1">
                Rp {{ number_format($metrics['omset'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#86968E] mt-1 font-medium">Transaksi Completed</div>
        </div>

        <!-- 2. Gross Profit (RBAC Protection - EMCO 79 Sand Ochre) -->
        <div class="bg-white rounded-2xl p-5 shadow-emco border border-[#E3EEE8] hover:shadow-emco-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#C2AC7C]/20 text-[#8F794B] flex items-center justify-center font-bold text-lg">
                    💰
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#8F794B] bg-[#C2AC7C]/20 border border-[#C2AC7C]/40">
                    Laba Kotor
                </span>
            </div>
            <div class="text-xs text-[#86968E] font-bold uppercase tracking-wider">Gross Profit</div>
            @if(auth()->user()->can('report.profit.view'))
                <div class="text-2xl font-extrabold text-[#8F794B] font-mono tracking-tight mt-1">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#86968E] mt-1 font-medium">Omset dikurangi HPP</div>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2 italic bg-[#F3F6F4] px-3 py-1.5 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Kas & Bank -->
        <div class="bg-white rounded-2xl p-5 shadow-emco border border-[#E3EEE8] hover:shadow-emco-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#E3EEE8] text-[#3F7A5D] flex items-center justify-center font-bold text-lg">
                    🏦
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Aktif
                </span>
            </div>
            <div class="text-xs text-[#86968E] font-bold uppercase tracking-wider">Total Kas & Bank</div>
            <div class="text-2xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#86968E] mt-1 font-medium">Saldo riil seluruh akun</div>
        </div>

        <!-- 4. Jumlah Transaksi -->
        <div class="bg-white rounded-2xl p-5 shadow-emco border border-[#E3EEE8] hover:shadow-emco-hover transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-[#E3EEE8] text-[#3F7A5D] flex items-center justify-center font-bold text-lg">
                    🧾
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Sukses
                </span>
            </div>
            <div class="text-xs text-[#86968E] font-bold uppercase tracking-wider">Jumlah Transaksi</div>
            <div class="text-2xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs text-[#86968E] font-normal">Nota</span>
            </div>
            <div class="text-[11px] text-[#86968E] mt-1 font-medium">Nota berhasil diproses</div>
        </div>
    </div>

    <!-- EMCO Data Visualization Chart & Table Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. ApexCharts Bar Chart (EMCO Jade Emerald Color) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-emco border border-[#E3EEE8] space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-[#232E28] tracking-tight">Grafik Omset Penjualan (7 Hari)</h2>
                    <p class="text-xs text-[#86968E] font-medium">Visualisasi tren omset ritel toko.</p>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-[#E3EEE8] text-[#3F7A5D]">
                    7 Hari Terakhir
                </span>
            </div>

            <!-- ApexCharts Container -->
            <div id="emco-sales-chart" class="w-full h-64"></div>
        </div>

        <!-- 2. Breakdown Tabel Harian -->
        <div class="bg-white rounded-2xl p-6 shadow-emco border border-[#E3EEE8] space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-extrabold text-[#232E28] tracking-tight">Rincian Omset</h2>
                    <a href="/admin/sales" class="text-xs font-bold text-[#3F7A5D] hover:underline">
                        Riwayat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-[#E3EEE8]">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F3F6F4] text-[#86968E] uppercase text-[10px] font-extrabold tracking-wider border-b border-[#E3EEE8]">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3 text-right">Omset (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($dailyTrends['labels'] as $index => $label)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-2.5 px-3 font-bold text-[#232E28]">{{ $label }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-[#3F7A5D]">
                                        Rp {{ number_format($dailyTrends['data'][$index] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-slate-400 font-medium">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include ApexCharts for EMCO Sage Bar Chart -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($dailyTrends['labels']);
        const data = @json($dailyTrends['data']);

        const options = {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'Public Sans, Poppins, sans-serif'
            },
            colors: ['#3F7A5D'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    borderRadius: 8,
                    startingShape: 'rounded'
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [{
                name: 'Total Omset',
                data: data
            }],
            xaxis: {
                categories: labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#86968E',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#86968E',
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function (val) {
                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'k';
                        return 'Rp ' + val;
                    }
                }
            },
            grid: {
                borderColor: '#E3EEE8',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#emco-sales-chart"), options);
        chart.render();
    });
</script>
