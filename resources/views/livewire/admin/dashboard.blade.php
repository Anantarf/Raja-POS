<div class="space-y-5">
    <!-- Welcome Banner Card -->
    <div class="bg-gradient-to-r from-[#047857]/10 via-[#047857]/5 to-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="space-y-1.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <div class="pt-1">
                <a href="/pos" class="h-10 px-4 text-xs sm:text-sm bg-[#047857] hover:bg-[#065F46] text-white font-extrabold rounded-xl inline-flex items-center gap-2 transition shadow-sm active:scale-95">
                    <span>Buka Layar Kasir</span> &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards Grid (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- 1. Total Omzet -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 border-t-4 border-t-[#047857] shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-default">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#047857] border border-emerald-200/60 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Omzet</span>
                </div>
                @if(($metrics['omzet_growth'] ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#047857] bg-emerald-50 border border-emerald-200/80 shrink-0">
                        &uarr; +{{ $metrics['omzet_growth'] }}%
                    </span>
                @elseif(($metrics['omzet_growth'] ?? 0) < 0)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 shrink-0">
                        &darr; {{ $metrics['omzet_growth'] }}%
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 shrink-0">
                        Hari Ini
                    </span>
                @endif
            </div>
            <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#2C3E35] font-mono tracking-tight truncate">
                Rp {{ number_format($metrics['omzet'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Penjualan Kotor Hari Ini</div>
        </div>

        <!-- 2. Margin Toko -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 border-t-4 border-t-[#047857] shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-default">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#047857] border border-emerald-200/60 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Margin Toko</span>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#047857] bg-emerald-50 border border-emerald-200/80 shrink-0">
                    Laba Bersih
                </span>
            </div>
            @if(auth()->user()->hasRole('OWNER') || auth()->user()->hasPermission('report.profit.view') || auth()->user()->can('report.profit.view'))
                <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#047857] font-mono tracking-tight truncate">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Omzet dikurangi Modal</div>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2 italic bg-[#F3F6F4] px-3 py-1.5 rounded-xl border border-slate-200 text-center">
                    [Akses Terbatas]
                </div>
            @endif
        </div>

        <!-- 3. Total Saldo Toko -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 border-t-4 border-t-[#047857] shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-default">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#047857] border border-emerald-200/60 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Saldo</span>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#047857] bg-emerald-50 border border-emerald-200/80 shrink-0">
                    Aktif
                </span>
            </div>
            <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#2C3E35] font-mono tracking-tight truncate">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <a href="/admin/balances" class="inline-flex mt-1 text-[11px] sm:text-xs text-[#047857] font-bold hover:text-[#065F46] hover:underline">Lihat rincian saldo &rarr;</a>
        </div>

        <!-- 4. Total Transaksi -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 border-t-4 border-t-[#047857] shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-default">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#047857] border border-emerald-200/60 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Transaksi</span>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#047857] bg-emerald-50 border border-emerald-200/80 shrink-0">
                    Sukses
                </span>
            </div>
            <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#2C3E35] font-mono tracking-tight truncate">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs sm:text-sm text-[#4F6258] font-normal">Trx</span>
            </div>
            <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Transaksi berhasil diproses</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 sm:gap-6 items-start">
        
        <!-- Left Panel: Daily Sales Chart -->
        <div class="lg:col-span-3 space-y-5">
            <!-- Daily Omzet Bar Chart -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-[#2C3E35] tracking-tight uppercase">
                            Grafik Omzet Harian (7 Hari Terakhir)
                        </h2>
                        <p class="text-xs text-[#5F7167] font-medium mt-0.5">
                            Visualisasi tren performa omzet penjualan ritel toko.
                        </p>
                    </div>
                    <a href="/admin/reports/sales" class="text-xs font-extrabold text-[#047857] hover:text-[#065F46] hover:underline">Lihat laporan</a>
                </div>

                <!-- ApexCharts Canvas -->
                <div id="dashboard-omzet-chart" class="w-full" style="min-height:320px;"></div>
            </div>
        </div>

        <!-- Right Panel: Best Sellers & Activity -->
        <div class="lg:col-span-2 space-y-5">
            <!-- 1. Top 5 Selling Products Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-[#2C3E35] tracking-tight uppercase">
                            5 Produk Terlaris
                        </h2>
                        <p class="text-xs text-[#5F7167] font-medium mt-0.5">
                            Barang paling cepat laku (Fast Moving Items).
                        </p>
                    </div>
                    <a href="/admin/reports/sales" class="text-xs font-extrabold text-amber-700 hover:text-amber-800 hover:underline">Lihat laporan</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($topProducts ?? [] as $index => $item)
                        <div class="py-2 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-[#E3EEE8] text-[#047857] font-extrabold flex items-center justify-center text-[11px] shrink-0">
                                    #{{ $index + 1 }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-[#2C3E35] truncate">{{ ucwords(strtolower(data_get($item, 'product_name', ''))) }}</div>
                                    <div class="text-[10px] text-[#718379] font-mono">{{ data_get($item, 'code', '-') }}</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-mono font-extrabold text-[#047857] text-xs">{{ data_get($item, 'total_qty', 0) }} pcs</div>
                                <div class="text-[10px] text-[#718379] font-mono">Rp {{ number_format((float) data_get($item, 'total_omzet', 0), 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 italic text-center py-4">Belum ada transaksi produk terlaris.</div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Payment Method Distribution Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-[#2C3E35] tracking-tight uppercase">
                            Distribusi Metode Pembayaran
                        </h2>
                        <p class="text-xs text-[#5F7167] font-medium mt-0.5">
                            Perbandingan transaksi Tunai, Transfer Bank, QRIS, &amp; E-Wallet.
                        </p>
                    </div>
                    <a href="/admin/reports/payment" class="text-xs font-extrabold text-[#047857] hover:text-[#065F46] hover:underline">Lihat laporan</a>
                </div>

                @php
                    $totalPayAmount = array_sum($paymentDistribution ?? []);
                @endphp

                <div class="space-y-2.5 pt-1">
                    @forelse($paymentDistribution ?? [] as $method => $amount)
                        @php
                            $percentage = $totalPayAmount > 0 ? round(($amount / $totalPayAmount) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-[#2C3E35] uppercase tracking-wider">{{ $method }}</span>
                                <div class="space-x-1 font-mono">
                                    <span class="text-[#047857] font-extrabold">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                    <span class="text-slate-400">({{ $percentage }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#047857] h-1.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 italic text-center py-3">Belum ada data pembayaran terdeteksi.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (function () {
        var labels = @json($dailyTrends['labels']);
        var data   = @json($dailyTrends['data']);

        var formattedLabels = labels.map(function (l) { return l.toUpperCase(); });

        var options = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, Roboto, sans-serif',
                animations: { enabled: true, easing: 'easeinout', speed: 500 }
            },
            colors: ['#047857'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '42%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [{ name: 'Total Omzet', data: data }],
            xaxis: {
                categories: formattedLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#5F7167', fontSize: '11px', fontWeight: 700 } }
            },
            yaxis: {
                min: 0,
                labels: {
                    style: { colors: '#5F7167', fontSize: '11px', fontWeight: 700 },
                    formatter: function (val) {
                        if (val === 0) return 'Rp 0';
                        if (val >= 1000000000) return 'Rp ' + (val / 1000000000).toFixed(1) + ' M';
                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
                        return 'Rp ' + val;
                    }
                }
            },
            grid: { borderColor: '#E3EEE8', strokeDashArray: 4 },
            states: { hover: { filter: { type: 'darken', value: 0.88 } } },
            tooltip: {
                theme: 'light',
                style: { fontSize: '12px', fontFamily: 'Inter, sans-serif' },
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        var el = document.getElementById('dashboard-omzet-chart');
        if (el) {
            var chart = new ApexCharts(el, options);
            chart.render();
        }
    })();
</script>
