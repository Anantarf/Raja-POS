<div class="space-y-6">
    <style>
        :root, html {
            font-size: 20px !important;
            --text-xs: 1.1rem !important;
            --text-sm: 1.25rem !important;
            --text-base: 1.4rem !important;
            --text-lg: 1.65rem !important;
            --text-xl: 2rem !important;
            --text-2xl: 2.5rem !important;
            --text-3xl: 3rem !important;
            --text-4xl: 3.75rem !important;
        }
        body {
            font-size: 1.4rem !important;
        }
        /* Direct high-specificity utility overrides */
        html body .text-xs { font-size: 1.1rem !important; line-height: 1.5rem !important; }
        html body .text-sm { font-size: 1.25rem !important; line-height: 1.75rem !important; }
        html body .text-base { font-size: 1.4rem !important; line-height: 2rem !important; }
        html body .text-lg { font-size: 1.65rem !important; line-height: 2.25rem !important; }
        html body .text-xl { font-size: 2rem !important; line-height: 2.5rem !important; }
        html body .text-2xl { font-size: 2.5rem !important; line-height: 3rem !important; }
        html body .text-3xl { font-size: 3rem !important; line-height: 3.5rem !important; }
        html body .text-4xl { font-size: 3.75rem !important; line-height: 4.25rem !important; }
    </style>

    <!-- Welcome Banner Card (Flat Crisp Border) -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/10 via-[#3F7A5D]/5 to-white border border-slate-200/80 rounded-2xl p-6 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-5 shadow-sm">
        <div class="space-y-2.5">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#232E28] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-base sm:text-lg text-[#52645B] max-w-3xl leading-relaxed font-medium">
                <span class="font-bold text-[#232E28]">Ringkasan Operasional:</span> Anda memiliki akses penuh sebagai <span class="font-extrabold text-[#3F7A5D]">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir &amp; manajemen ritel Raja Aksesoris.
            </p>
            <div class="pt-2">
                <a href="/pos" class="h-12 px-5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-base inline-flex items-center gap-2 transition active:scale-95 shadow-sm">
                    <span>Buka Layar Kasir</span> &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards Grid (Golden Ratio Clean Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Total Omzet -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[#718379] font-black uppercase tracking-wider">Total Omzet</span>
                @if(($metrics['omzet_growth'] ?? 0) > 0)
                    <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8] border border-[#3F7A5D]/20">
                        ↑ +{{ $metrics['omzet_growth'] }}%
                    </span>
                @elseif(($metrics['omzet_growth'] ?? 0) < 0)
                    <span class="px-3 py-1 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200">
                        ↓ {{ $metrics['omzet_growth'] }}%
                    </span>
                @else
                    <span class="px-3 py-1 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200">
                        Hari Ini
                    </span>
                @endif
            </div>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#232E28] font-mono tracking-tight mt-2">
                Rp {{ number_format($metrics['omzet'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-[#718379] mt-2 font-medium">Transaksi Selesai</div>
        </div>

        <!-- 2. Margin -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C2AC7C]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[#718379] font-black uppercase tracking-wider">Margin Toko</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#8F794B] bg-[#C2AC7C]/20 border border-[#C2AC7C]/40">
                    Margin
                </span>
            </div>
            @if(auth()->user()->hasRole('OWNER') || auth()->user()->hasPermission('report.profit.view') || auth()->user()->can('report.profit.view'))
                <div class="text-3xl sm:text-4xl font-extrabold text-[#8F794B] font-mono tracking-tight mt-2">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-sm text-[#718379] mt-2 font-medium">Omzet dikurangi Modal</div>
            @else
                <div class="text-sm font-bold text-slate-400 mt-3 italic bg-[#F3F6F4] px-3.5 py-2 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Saldo Toko -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[#718379] font-black uppercase tracking-wider">Total Saldo Toko</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Aktif
                </span>
            </div>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#232E28] font-mono tracking-tight mt-2">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-[#718379] mt-2 font-medium">Saldo riil seluruh akun &amp; cash</div>
        </div>

        <!-- 4. Total Transaksi -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[#718379] font-black uppercase tracking-wider">Total Transaksi</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Sukses
                </span>
            </div>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#232E28] font-mono tracking-tight mt-2">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-base text-[#718379] font-normal">Trx</span>
            </div>
            <div class="text-sm text-[#718379] mt-2 font-medium">Transaksi berhasil diproses</div>
        </div>
    </div>

    <!-- Data Visualization Chart & Table Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- 1. ApexCharts Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-[#232E28] tracking-tight">Grafik Omzet Penjualan Harian</h2>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Visualisasi tren omzet ritel toko 7 hari terakhir.</p>
                </div>
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D]">
                    7 Hari Terakhir
                </span>
            </div>

            <!-- ApexCharts Container -->
            <div id="emco-sales-chart" class="w-full h-72"></div>
        </div>

        <!-- 2. Breakdown Tabel Harian -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg sm:text-xl font-extrabold text-[#232E28] tracking-tight">Rincian Omzet</h2>
                    <a href="/admin/sales" class="text-sm font-bold text-[#3F7A5D] hover:underline">
                        Riwayat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                    <table class="w-full text-base text-left">
                        <thead class="bg-[#F3F6F4] text-[#718379] uppercase text-xs font-extrabold tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-4 text-right">Omzet (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($dailyTrends['labels'] as $index => $label)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-bold text-[#232E28] text-base">{{ $label }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#3F7A5D] text-lg">
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

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($dailyTrends['labels']);
        const data = @json($dailyTrends['data']);

        const options = {
            chart: {
                type: 'bar',
                height: 310,
                toolbar: { show: false },
                fontFamily: 'Public Sans, Poppins, sans-serif'
            },
            colors: ['#3F7A5D'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 10,
                    startingShape: 'rounded'
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [{
                name: 'Total Omzet',
                data: data
            }],
            xaxis: {
                categories: labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#718379',
                        fontSize: '13px',
                        fontWeight: 700
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#718379',
                        fontSize: '13px',
                        fontWeight: 700
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

