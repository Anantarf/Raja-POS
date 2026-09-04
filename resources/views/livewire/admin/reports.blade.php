<div class="space-y-6">
    <!-- Page Header & Period Filter Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Laporan Toko</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Analisis lengkap performa penjualan, margin, kasir, stok barang, dan saldo toko.</p>
        </div>

        <!-- Filter Period & Print Control -->
        <div class="flex items-center gap-2 flex-wrap text-xs">
            <select wire:model.live="period" class="p-2 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#232E28] font-bold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                <option value="all_time">Semua Waktu</option>
                <option value="today">Hari Ini</option>
                <option value="7_days">7 Hari Terakhir</option>
                <option value="this_month">Bulan Ini</option>
                <option value="custom">Kustom Tanggal</option>
            </select>

            @if($period === 'custom')
                <div class="flex items-center gap-1">
                    <input type="date" wire:model.live="startDate" class="p-2 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4]" />
                    <span class="text-slate-400 font-bold">&rarr;</span>
                    <input type="date" wire:model.live="endDate" class="p-2 border border-slate-200 rounded-xl text-xs font-semibold bg-[#F3F6F4]" />
                </div>
            @endif

            <button onclick="window.print()" class="px-3 py-2 bg-[#F3F6F4] hover:bg-[#E3EEE8] text-[#3F7A5D] border border-slate-200 font-extrabold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Export</span>
            </button>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200/80 pb-3 text-xs font-bold print:hidden">
        @foreach([
            'sales' => 'Penjualan & Produk Terlaris',
            'cashier' => 'Performa Kasir',
            'inventory' => 'Stok & Valuasi Barang',
            'payment' => 'Metode Pembayaran',
            'balance' => 'Saldo Toko'
        ] as $key => $label)
            <a href="/admin/reports/{{ $key }}" class="px-3.5 py-2 rounded-xl transition {{ $type === $key ? 'bg-[#3F7A5D] text-white shadow-sm' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Contextual Executive KPI Cards for Sales, Cashier, and Payment tabs -->
    @if(in_array($type, ['sales', 'cashier', 'payment']))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- 1. Omzet -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Omzet Penjualan</div>
                <div class="text-2xl font-black font-mono tracking-tight text-[#232E28]">
                    Rp {{ number_format($metrics['omzet'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Total penerimaan penjualan</div>
            </div>

            <!-- 2. Modal -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Modal Barang</div>
                <div class="text-2xl font-black font-mono tracking-tight text-slate-600">
                    Rp {{ number_format($metrics['cogs'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Total modal produk terjual</div>
            </div>

            <!-- 3. Margin -->
            @php
                $marginRatio = $metrics['omzet'] > 0 ? ($metrics['gross_profit'] / $metrics['omzet']) * 100 : 0;
            @endphp
            <div class="bg-white rounded-2xl p-4 border border-[#3F7A5D]/40 bg-gradient-to-b from-[#E3EEE8]/40 to-white shadow-sm space-y-1">
                <div class="text-[11px] text-[#3F7A5D] font-extrabold uppercase tracking-wider flex items-center justify-between">
                    <span>Margin Toko</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-[#3F7A5D] text-white font-bold">{{ number_format($marginRatio, 1) }}%</span>
                </div>
                <div class="text-2xl font-black font-mono tracking-tight text-[#3F7A5D]">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Omzet dikurangi Modal</div>
            </div>

            <!-- 4. Total Transaksi -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Transaksi</div>
                <div class="text-2xl font-black font-mono tracking-tight text-[#232E28]">
                    {{ number_format($salesCount, 0, ',', '.') }} Trx
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Transaksi berhasil</div>
            </div>

            <!-- 5. Rata-rata Transaksi -->
            @php
                $avgTicket = $salesCount > 0 ? $metrics['omzet'] / $salesCount : 0;
            @endphp
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Rata-rata Transaksi</div>
                <div class="text-2xl font-black font-mono tracking-tight text-[#232E28]">
                    Rp {{ number_format($avgTicket, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Nilai per transaksi</div>
            </div>
        </div>
    @endif

    <!-- TAB CONTENT SECTIONS -->

    <!-- Tab 1: Sales, Trend, Top Products, & Categories -->
    @if($type === 'sales')
        <div class="space-y-6">
            <!-- Daily Sales Trend Chart Bar (7 Days) -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Grafik Omzet Harian (7 Hari Terakhir)</h3>
                        <p class="text-xs text-[#718379] font-medium">Grafik visualisasi omzet penjualan harian toko.</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-[#3F7A5D] bg-[#E3EEE8] px-2.5 py-1 rounded-lg">Realtime</span>
                </div>

                @php
                    $maxVal = max($dailyTrend['data']) > 0 ? max($dailyTrend['data']) : 1;
                @endphp

                <div class="grid grid-cols-7 gap-2 pt-4 items-end h-40">
                    @foreach($dailyTrend['labels'] as $idx => $label)
                        @php
                            $val = $dailyTrend['data'][$idx] ?? 0;
                            $pct = round(($val / $maxVal) * 100);
                        @endphp
                        <div class="flex flex-col items-center gap-2 h-full justify-end group">
                            <div class="text-[10px] font-mono font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                                Rp {{ number_format($val, 0, ',', '.') }}
                            </div>
                            <div class="w-full bg-[#E3EEE8] rounded-t-xl transition-all duration-300 group-hover:bg-[#3F7A5D] relative" style="height: {{ max($pct, 8) }}%;">
                            </div>
                            <div class="text-[10px] font-extrabold text-[#718379] uppercase tracking-wider">{!! $label !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Selling Products -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden space-y-3">
                    <div class="p-4 sm:p-5 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Top 5 Produk Terlaris</h3>
                        <p class="text-xs text-[#718379] mt-0.5">Produk dengan jumlah unit terjual terbanyak dalam periode terpilih.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">Peringkat &amp; Nama Produk</th>
                                    <th class="py-3 px-4 text-center">Unit Terjual</th>
                                    <th class="py-3 px-4 text-right">Total Omzet</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($topProducts as $index => $prod)
                                    <tr class="hover:bg-[#F3F6F4]/60 transition">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-lg text-[11px] font-black flex items-center justify-center {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-600')) }}">
                                                    #{{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <div class="font-bold text-[#232E28]">{{ $prod->product_name }}</div>
                                                    <div class="text-[10px] text-[#718379] font-mono">Kode: {{ $prod->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono font-extrabold text-[#232E28]">
                                            {{ number_format($prod->total_qty, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-black text-[#3F7A5D]">
                                            Rp {{ number_format($prod->total_omzet, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-slate-400 font-medium">Belum ada transaksi penjualan pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Sales Breakdown -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider border-b border-slate-100 pb-3">Penjualan Berdasarkan Kategori</h3>
                    
                    <div class="space-y-3 text-xs">
                        @forelse($categoryBreakdown as $cat)
                            @php
                                $catPct = $metrics['omzet'] > 0 ? ($cat->total_omzet / $metrics['omzet']) * 100 : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="flex justify-between items-center font-semibold">
                                    <span class="text-[#232E28] font-bold">{{ $cat->category_name }} ({{ $cat->total_qty }} Unit)</span>
                                    <span class="font-mono font-extrabold text-[#3F7A5D]">Rp {{ number_format($cat->total_omzet, 0, ',', '.') }} ({{ number_format($catPct, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-[#F3F6F4] h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#3F7A5D] h-full rounded-full transition-all duration-300" style="width: {{ min($catPct, 100) }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400 font-medium">Belum ada data kategori penjualan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    <!-- Tab 2: Cashier Performance -->
    @elseif($type === 'cashier')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Performa &amp; Produktivitas Kasir</h3>
                <p class="text-xs text-[#718379] mt-0.5">Laporan total transaksi, omzet, dan margin yang dihasilkan oleh masing-masing petugas kasir.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Petugas Kasir</th>
                            <th class="py-3.5 px-4 text-center">Total Transaksi</th>
                            <th class="py-3.5 px-4 text-right">Omzet Dihasilkan</th>
                            <th class="py-3.5 px-4 text-right">Margin Dihasilkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($cashierPerformance as $cashier)
                            <tr class="hover:bg-[#F3F6F4]/60 transition">
                                <td class="py-3.5 px-4 font-bold text-[#232E28] text-sm">
                                    {{ $cashier->cashier_name }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#232E28]">
                                    {{ number_format($cashier->total_sales, 0, ',', '.') }} Trx
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#232E28]">
                                    Rp {{ number_format($cashier->total_omzet, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-[#3F7A5D]">
                                    Rp {{ number_format($cashier->total_margin, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-400">Belum ada data performa kasir pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Tab 3: Inventory Valuation -->
    @elseif($type === 'inventory')
        <div class="space-y-6">
            <!-- Contextual Inventory KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Modal Stok (HPP)</div>
                    <div class="text-2xl font-black font-mono tracking-tight text-[#232E28]">
                        Rp {{ number_format($inventoryValuation['total_cost'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#718379]">Modal mengendap pada produk stok fisik toko</div>
                </div>

                <div class="bg-white border border-[#3F7A5D]/40 bg-gradient-to-b from-[#E3EEE8]/40 to-white rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#3F7A5D] font-extrabold uppercase tracking-wider">Potensi Omzet Stok</div>
                    <div class="text-2xl font-black font-mono tracking-tight text-[#3F7A5D]">
                        Rp {{ number_format($inventoryValuation['total_retail'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#718379]">Potensi omzet jika seluruh stok terjual</div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Fisik Unit Stok</div>
                    <div class="text-2xl font-black font-mono tracking-tight text-[#232E28]">
                        {{ number_format($inventoryValuation['total_units'], 0, ',', '.') }} Unit
                    </div>
                    <div class="text-[11px] text-[#718379]">Dari {{ number_format($inventoryCount, 0, ',', '.') }} jenis produk</div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Stok Menipis / Habis</div>
                    <div class="text-2xl font-black font-mono tracking-tight {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-[#3F7A5D]' }}">
                        {{ number_format($lowStockCount, 0, ',', '.') }} Item
                    </div>
                    <div class="text-[11px] text-[#718379]">Perlu restock segera</div>
                </div>
            </div>

            @if($lowStockCount > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between text-xs text-amber-900 font-semibold">
                    <div>
                        <span class="font-bold text-amber-950">Peringatan Stok Toko:</span> Terdapat <span class="font-extrabold underline">{{ number_format($lowStockCount, 0, ',', '.') }} item barang</span> yang berada pada status menipis atau habis.
                    </div>
                    <a href="/admin/inventories" class="px-3 py-1.5 bg-amber-800 text-white rounded-xl text-[11px] font-bold hover:bg-amber-900 transition">Cek Stok Barang &rarr;</a>
                </div>
            @endif
        </div>

    <!-- Tab 4: Payment Methods Distribution -->
    @elseif($type === 'payment')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Rincian Pembayaran Masuk</h3>
                <p class="text-xs text-[#718379] mt-0.5">Distribusi penerimaan uang berdasarkan metode pembayaran yang digunakan pelanggan.</p>
            </div>
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Metode Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Total Nominal Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($paymentDistribution as $method => $amount)
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 font-bold text-[#232E28] text-sm">{{ $method }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#3F7A5D] text-sm">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-10 text-center text-slate-400 font-medium">Belum ada data pembayaran completed pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Tab 5: Account Balance Position -->
    @elseif($type === 'balance')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($balanceAccounts as $account)
                @php
                    $hasBal = $account->current_balance > 0;
                @endphp
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">{{ $account->name }}</div>
                    <div class="text-2xl font-mono font-extrabold {{ $hasBal ? 'text-[#3F7A5D]' : 'text-slate-400' }}">
                        Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
