<div class="space-y-6">
    <!-- Welcome Banner Card (Flat Crisp Border) -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/10 via-[#3F7A5D]/5 to-white border border-slate-200/80 rounded-2xl p-6 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-5 shadow-sm">
        <div class="space-y-2.5">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#232E28] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-lg text-[#34463D] max-w-3xl leading-relaxed font-medium">
                <span class="font-bold text-[#232E28]">Ringkasan Operasional:</span> Anda memiliki akses penuh sebagai <span class="font-extrabold text-[#3F7A5D]">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir &amp; manajemen ritel Raja Aksesoris.
            </p>
            <div class="pt-2">
                <a href="/pos" class="h-12 px-6 text-lg bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl inline-flex items-center gap-2 transition active:scale-95 shadow-sm">
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
                <span class="text-base text-[#5F7167] font-black uppercase tracking-wide">Total Omzet</span>
                @if(($metrics['omzet_growth'] ?? 0) > 0)
                    <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8] border border-[#3F7A5D]/20">
                        &uarr; +{{ $metrics['omzet_growth'] }}%
                    </span>
                @elseif(($metrics['omzet_growth'] ?? 0) < 0)
                    <span class="px-3 py-1 rounded-lg text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200">
                        &darr; {{ $metrics['omzet_growth'] }}%
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
            <div class="text-base text-[#4F6258] mt-2 font-medium">Transaksi Selesai</div>
        </div>

        <!-- 2. Margin -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C2AC7C]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-base text-[#5F7167] font-black uppercase tracking-wide">Margin Toko</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#8F794B] bg-[#C2AC7C]/20 border border-[#C2AC7C]/40">
                    Margin
                </span>
            </div>
            @if(auth()->user()->hasRole('OWNER') || auth()->user()->hasPermission('report.profit.view') || auth()->user()->can('report.profit.view'))
                <div class="text-3xl sm:text-4xl font-extrabold text-[#8F794B] font-mono tracking-tight mt-2">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-base text-[#4F6258] mt-2 font-medium">Omzet dikurangi Modal</div>
            @else
                <div class="text-sm font-bold text-slate-400 mt-3 italic bg-[#F3F6F4] px-3.5 py-2 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Saldo Toko -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-base text-[#5F7167] font-black uppercase tracking-wide">Total Saldo Toko</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Aktif
                </span>
            </div>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#232E28] font-mono tracking-tight mt-2">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-base text-[#4F6258] mt-2 font-medium">Saldo riil seluruh akun &amp; cash</div>
        </div>

        <!-- 4. Total Transaksi -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-base text-[#5F7167] font-black uppercase tracking-wide">Total Transaksi</span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Sukses
                </span>
            </div>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#232E28] font-mono tracking-tight mt-2">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-lg text-[#4F6258] font-normal">Trx</span>
            </div>
            <div class="text-base text-[#4F6258] mt-2 font-medium">Transaksi berhasil diproses</div>
        </div>
    </div>

    <!-- Data Visualization Chart & Table Grid -->
    @php
        $hasChartData = array_sum($dailyTrends['data'] ?? []) > 0;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- 1. ApexCharts Bar Chart Card -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-slate-200/80 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-[#232E28] tracking-tight">Grafik Omzet Penjualan Harian</h2>
                    <p class="text-xs text-[#5F7167] font-medium mt-0.5">Visualisasi tren omzet ritel toko 7 hari terakhir.</p>
                </div>
                <span class="px-3 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D]">
                    7 Hari Terakhir
                </span>
            </div>

            @if($hasChartData)
                <!-- ApexCharts Container (Compact 240px) -->
                <div id="emco-sales-chart" class="w-full h-60"></div>
            @else
                <!-- Clean Empty State (When 7-Day Revenue is Rp 0) -->
                <div class="h-60 border border-dashed border-slate-200 rounded-xl bg-[#F3F6F4]/50 flex flex-col items-center justify-center text-center p-5 space-y-2">
                    <div class="w-11 h-11 rounded-full bg-[#E3EEE8] text-[#3F7A5D] flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div class="font-extrabold text-[#232E28] text-sm">Belum Ada Omzet pada Periode Ini</div>
                    <div class="text-xs text-[#5F7167] max-w-xs leading-relaxed font-medium">Grafik tren harian akan otomatis aktif begitu transaksi pertama berhasil diproses pada 7 hari terakhir.</div>
                </div>
            @endif
        </div>

        <!-- 2. Breakdown Tabel Harian -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between space-y-3">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-extrabold text-[#232E28] tracking-tight">Rincian Omzet</h2>
                    <a href="/admin/sales" class="text-xs font-bold text-[#3F7A5D] hover:underline">
                        Riwayat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] text-[#5F7167] uppercase text-xs font-extrabold tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3 text-right">Omzet (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($dailyTrends['labels'] as $index => $label)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-2.5 px-3 font-semibold text-[#232E28] text-sm">{{ $label }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-extrabold text-[#3F7A5D] text-base">
                                        Rp {{ number_format($dailyTrends['data'][$index] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-5 text-center text-slate-400 font-medium text-xs">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($hasChartData)
    <!-- Include ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = @json($dailyTrends['labels']);
            const data = @json($dailyTrends['data']);

            const options = {
                chart: {
                    type: 'bar',
                    height: 240,
                    toolbar: { show: false },
                    fontFamily: 'Inter, Roboto, sans-serif'
                },
                colors: ['#3F7A5D'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '38%',
                        borderRadius: 6,
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
                            fontSize: '12px',
                            fontWeight: 700
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    labels: {
                        style: {
                            colors: '#718379',
                            fontSize: '12px',
                            fontWeight: 700
                        },
                        formatter: function (val) {
                            if (val === 0) return 'Rp 0';
                            if (val >= 1000000000) return 'Rp ' + (val / 1000000000).toFixed(1) + ' M';
                            if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                            if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
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

            const chartEl = document.querySelector("#emco-sales-chart");
            if (chartEl) {
                const chart = new ApexCharts(chartEl, options);
                chart.render();
            }
        });
    </script>
@endif
