<div class="space-y-7">
    <!-- EMCO Welcome Banner Card (Flat Crisp Border) -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/15 via-[#3F7A5D]/5 to-white border border-[#3F7A5D]/20 rounded-3xl p-7 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-5">
        <div class="space-y-2.5">
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-sm text-[#52645B] max-w-2xl leading-relaxed">
                <span class="font-bold text-[#232E28]">Dashboard Overview:</span> Anda memiliki akses penuh sebagai <span class="font-bold text-[#3F7A5D] text-base">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir & manajemen ritel Raja Aksesoris POS.
            </p>
            <div class="pt-2">
                <a href="/pos" class="px-5 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-2xl text-sm inline-flex items-center gap-2 transition active:scale-95">
                    <span>Buka Layar Kasir POS</span> &rarr;
                </a>
            </div>
        </div>

        <!-- Stunning 3D Claymation Cash Register POS Terminal Element -->
        <div class="hidden md:block shrink-0 pr-2">
            <div class="w-32 h-32 rounded-3xl bg-white border border-[#E3EEE8] p-2 flex items-center justify-center shadow-md hover:scale-105 transition-transform duration-300">
                <img src="/images/pos_cash_register_3d.jpg" alt="3D Cash Register POS" class="w-full h-full object-contain rounded-2xl">
            </div>
        </div>
    </div>

    <!-- EMCO Executive Stat Cards Grid (Flat Crisp Borders) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 1. Total Omset -->
        <div class="bg-white rounded-3xl p-6 border border-[#E3EEE8] hover:border-[#3F7A5D]/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Total Omset</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8] border border-[#3F7A5D]/20">
                    ↑ +100%
                </span>
            </div>
            <div class="text-3xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1.5">
                Rp {{ number_format($metrics['omset'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-[#718379] mt-2 font-medium">Transaksi Completed</div>
        </div>

        <!-- 2. Gross Profit -->
        <div class="bg-white rounded-3xl p-6 border border-[#E3EEE8] hover:border-[#C2AC7C]/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Laba Kotor</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold text-[#8F794B] bg-[#C2AC7C]/20 border border-[#C2AC7C]/40">
                    Gross Profit
                </span>
            </div>
            @if(auth()->user()->can('report.profit.view'))
                <div class="text-3xl font-extrabold text-[#8F794B] font-mono tracking-tight mt-1.5">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-[#718379] mt-2 font-medium">Omset dikurangi HPP</div>
            @else
                <div class="text-xs font-bold text-slate-400 mt-3 italic bg-[#F3F6F4] px-3.5 py-2 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas - Owner]
                </div>
            @endif
        </div>

        <!-- 3. Total Kas & Bank -->
        <div class="bg-white rounded-3xl p-6 border border-[#E3EEE8] hover:border-[#3F7A5D]/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Total Kas & Bank</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Aktif
                </span>
            </div>
            <div class="text-3xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1.5">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-[#718379] mt-2 font-medium">Saldo riil seluruh akun</div>
        </div>

        <!-- 4. Jumlah Transaksi -->
        <div class="bg-white rounded-3xl p-6 border border-[#E3EEE8] hover:border-[#3F7A5D]/50 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-[#718379] font-extrabold uppercase tracking-wider">Jumlah Transaksi</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold text-[#3F7A5D] bg-[#E3EEE8]">
                    Sukses
                </span>
            </div>
            <div class="text-3xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1.5">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-sm text-[#718379] font-normal">Nota</span>
            </div>
            <div class="text-xs text-[#718379] mt-2 font-medium">Nota berhasil diproses</div>
        </div>
    </div>

    <!-- EMCO Data Visualization Chart & Table Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">
        <!-- 1. ApexCharts Bar Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-7 border border-[#E3EEE8] space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-[#232E28] tracking-tight">Grafik Omset Penjualan Harian</h2>
                    <p class="text-xs text-[#718379] font-medium mt-0.5">Visualisasi tren omset ritel toko 7 hari terakhir.</p>
                </div>
                <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-[#E3EEE8] text-[#3F7A5D]">
                    7 Hari Terakhir
                </span>
            </div>

            <!-- ApexCharts Container -->
            <div id="emco-sales-chart" class="w-full h-72"></div>
        </div>

        <!-- 2. Breakdown Tabel Harian -->
        <div class="bg-white rounded-3xl p-7 border border-[#E3EEE8] space-y-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-extrabold text-[#232E28] tracking-tight">Rincian Omset</h2>
                    <a href="/admin/sales" class="text-xs font-bold text-[#3F7A5D] hover:underline">
                        Riwayat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-[#E3EEE8]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] text-[#718379] uppercase text-xs font-extrabold tracking-wider border-b border-[#E3EEE8]">
                            <tr>
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-4 text-right">Omset (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($dailyTrends['labels'] as $index => $label)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-bold text-[#232E28] text-sm">{{ $label }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-[#3F7A5D] text-base">
                                        Rp {{ number_format($dailyTrends['data'][$index] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-slate-400 font-medium">Belum ada data.</td>
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
                height: 290,
                toolbar: { show: false },
                fontFamily: 'Public Sans, Poppins, sans-serif'
            },
            colors: ['#3F7A5D'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '38%',
                    borderRadius: 10,
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
                        colors: '#718379',
                        fontSize: '12px',
                        fontWeight: 700
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#718379',
                        fontSize: '12px',
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
