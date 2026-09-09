<div class="space-y-5">
    <!-- Welcome Banner Card -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/10 via-[#3F7A5D]/5 to-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="space-y-1.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-[#34463D] max-w-2xl leading-relaxed font-medium">
                <span class="font-bold text-[#2C3E35]">Ringkasan Operasional:</span> Anda memiliki akses penuh sebagai <span class="font-extrabold text-[#3F7A5D]">{{ auth()->user()->role?->name ?? 'Kasir' }}</span> pada sistem kasir &amp; manajemen ritel Raja Aksesoris.
            </p>
            <div class="pt-1">
                <a href="/pos" class="h-10 px-4 text-xs sm:text-sm bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl inline-flex items-center gap-2 transition shadow-sm active:scale-95">
                    <span>Buka Layar Kasir</span> &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards Grid (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
        <!-- 1. Total Omzet -->
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-xs transition cursor-default">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] sm:text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Omzet</span>
                @if(($metrics['omzet_growth'] ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8] border border-[#3F7A5D]/20 shrink-0">
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
            <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Transaksi Selesai</div>
        </div>

        <!-- 2. Margin -->
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 hover:border-[#C2AC7C]/50 shadow-xs transition cursor-default">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] sm:text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Margin Toko</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#8F794B] bg-[#C2AC7C]/20 border border-[#C2AC7C]/40 shrink-0">
                    Margin
                </span>
            </div>
            @if(auth()->user()->hasRole('OWNER') || auth()->user()->hasPermission('report.profit.view') || auth()->user()->can('report.profit.view'))
                <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#8F794B] font-mono tracking-tight truncate">
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
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-xs transition cursor-default">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] sm:text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Saldo</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8] shrink-0">
                    Aktif
                </span>
            </div>
            <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#2C3E35] font-mono tracking-tight truncate">
                Rp {{ number_format($metrics['total_balance'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Saldo riil seluruh akun &amp; cash</div>
        </div>

        <!-- 4. Total Transaksi -->
        <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 hover:border-[#3F7A5D]/50 shadow-xs transition cursor-default">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] sm:text-xs text-[#5F7167] font-extrabold uppercase tracking-wider truncate">Total Transaksi</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-[#3F7A5D] bg-[#E3EEE8] shrink-0">
                    Sukses
                </span>
            </div>
            <div class="text-lg sm:text-xl lg:text-2xl font-extrabold text-[#2C3E35] font-mono tracking-tight truncate">
                {{ number_format($metrics['sales_count'], 0, ',', '.') }} <span class="text-xs sm:text-sm text-[#4F6258] font-normal">Trx</span>
            </div>
            <div class="text-[11px] sm:text-xs text-[#4F6258] mt-1 font-medium">Transaksi berhasil diproses</div>
        </div>
    </div>

    <!-- Golden Ratio Main Layout Grid (3:2 Ratio / 60% : 40% Width) -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 sm:gap-6 items-start">
        
        <!-- Left Main Panel (60% Golden Ratio Width: lg:col-span-3) -->
        <div class="lg:col-span-3 space-y-5">
            <!-- 1. Daily Omzet Bar Chart Card -->
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
                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20">
                        Realtime
                    </span>
                </div>

                <!-- ApexCharts Canvas -->
                <div id="dashboard-omzet-chart" class="w-full" style="min-height:320px;"></div>
            </div>
        </div>

        <!-- Right Side Panel (40% Golden Ratio Width: lg:col-span-2) -->
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
                    <span class="text-xs font-extrabold text-amber-700 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-lg">Top 5</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($topProducts ?? [] as $index => $item)
                        <div class="py-2 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-[#E3EEE8] text-[#3F7A5D] font-extrabold flex items-center justify-center text-[11px] shrink-0">
                                    #{{ $index + 1 }}
                                </span>
                                <div class="truncate">
                                    <div class="font-bold text-[#2C3E35] truncate">{{ ucwords(strtolower(data_get($item, 'product_name', ''))) }}</div>
                                    <div class="text-[10px] text-[#718379] font-mono">{{ data_get($item, 'code', '-') }}</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-mono font-extrabold text-[#3F7A5D] text-xs">{{ data_get($item, 'total_qty', 0) }} pcs</div>
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
                    <span class="text-xs font-extrabold text-[#3F7A5D] bg-[#E3EEE8] px-2.5 py-1 rounded-lg">Audit Saldo</span>
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
                                    <span class="text-[#3F7A5D] font-extrabold">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                    <span class="text-slate-400">({{ $percentage }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#3F7A5D] h-1.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
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
            colors: ['#3F7A5D'],
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
