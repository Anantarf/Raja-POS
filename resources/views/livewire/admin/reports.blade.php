<div class="space-y-6">
    <!-- Page Header & Period Filter Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">Laporan Toko</h1>
            <p class="text-xs sm:text-sm text-[#718379] font-medium mt-0.5">Analisis lengkap performa penjualan, margin, kasir, rekap harian, dan saldo toko.</p>
        </div>

        <!-- Filter Period & Print Control -->
        <div class="flex items-center gap-2 flex-wrap text-sm">
            @if($type === 'daily_summary')
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="text-xs font-bold text-[#718379] uppercase tracking-wider">Tanggal Rekap:</label>
                    <input type="date" wire:model.live="summaryDate" class="h-11 px-3.5 py-2 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-extrabold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer text-xs sm:text-sm" />
                    <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200">
                        <button type="button" wire:click="setSummaryDateToToday" class="px-2.5 py-1.5 rounded-lg text-xs font-extrabold transition {{ $summaryDate === \Carbon\Carbon::today()->toDateString() ? 'bg-white text-[#2C3E35] shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Hari Ini</button>
                        <button type="button" wire:click="setSummaryDateToYesterday" class="px-2.5 py-1.5 rounded-lg text-xs font-extrabold transition {{ $summaryDate === \Carbon\Carbon::yesterday()->toDateString() ? 'bg-white text-[#2C3E35] shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Kemarin</button>
                    </div>
                </div>
            @else
                <select wire:model.live="period" class="h-11 px-3 py-2.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer">
                    <option value="all_time">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="7_days">7 Hari Terakhir</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">Kustom Tanggal</option>
                </select>

                @if($period === 'custom')
                    <div class="flex items-center gap-1">
                        <input type="date" wire:model.live="startDate" class="h-11 px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-[#F3F6F4]" />
                        <span class="text-slate-400 font-bold">&rarr;</span>
                        <input type="date" wire:model.live="endDate" class="h-11 px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold bg-[#F3F6F4]" />
                    </div>
                @endif
            @endif

        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 pb-3 text-xs sm:text-sm font-bold print:hidden overflow-x-auto no-scrollbar whitespace-nowrap">
        @foreach([
            'sales' => 'Penjualan & Produk Terlaris',
            'daily_summary' => 'Summary Harian (Tutup Kas)',
            'inventory' => 'Stok & Valuasi Barang',
            'payment' => 'Metode Pembayaran'
        ] as $key => $label)
            <a href="/admin/reports/{{ $key }}" class="px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-xl transition shrink-0 {{ $type === $key ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#2C3E35]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Contextual Executive KPI Cards for Sales and Payment tabs -->
    @if(in_array($type, ['sales', 'payment']))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- 1. Omzet -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-xs space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider truncate">Omzet Penjualan</div>
                <div class="text-lg sm:text-xl lg:text-2xl font-extrabold font-mono tracking-tight text-[#2C3E35] truncate">
                    Rp {{ number_format($metrics['omzet'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Total penerimaan penjualan</div>
            </div>

            <!-- 2. Modal -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Modal Barang</div>
                <div class="text-2xl font-extrabold font-mono tracking-tight text-slate-600">
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
                <div class="text-2xl font-extrabold font-mono tracking-tight text-[#3F7A5D]">
                    Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Omzet dikurangi Modal</div>
            </div>

            <!-- 4. Total Transaksi -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-1">
                <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Transaksi</div>
                <div class="text-2xl font-extrabold font-mono tracking-tight text-[#2C3E35]">
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
                <div class="text-2xl font-extrabold font-mono tracking-tight text-[#2C3E35]">
                    Rp {{ number_format($avgTicket, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#718379] font-medium">Nilai per transaksi</div>
            </div>
        </div>
    @endif

    <!-- TAB CONTENT SECTIONS -->

    <!-- Tab 0: Daily Summary (Summary Harian / Tutup Kas) -->
    @if($type === 'daily_summary')
        @php
            $formattedDate = \Carbon\Carbon::parse($dailySummaryData['summary_date'])->locale('id')->translatedFormat('l, d F Y');
            $status = $dailySummaryData['status'];
            $isLocked = ($status === 'SUDAH_DICEK');
            $hasDiscrepancy = $dailySummaryData['has_discrepancy'];
        @endphp
        <div class="space-y-6">
            <!-- Header Toolbar for Daily Summary -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#E3EEE8] text-[#3F7A5D] font-extrabold flex items-center justify-center border border-[#3F7A5D]/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h55.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-lg font-black text-[#2C3E35] tracking-tight uppercase">LAPORAN SUMMARY HARIAN</h2>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-black uppercase bg-amber-200 text-amber-900 border border-amber-300">
                                {{ strtoupper($formattedDate) }}
                            </span>

                            <!-- Status Badge -->
                            @if($status === 'SUDAH_DICEK')
                                @if($hasDiscrepancy)
                                    <span class="px-3 py-1 rounded-xl bg-rose-100 text-rose-800 text-xs font-black border border-rose-300 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                        SUDAH DI-CEK (ADA SELISIH)
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black border border-emerald-300 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        SUDAH DI-CEK (SESUAI)
                                    </span>
                                @endif
                            @elseif($status === 'SEDANG_DICEK')
                                <span class="px-3 py-1 rounded-xl bg-amber-100 text-amber-900 text-xs font-extrabold border border-amber-300 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    SEDANG DI-CEK (DRAFT)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                    ⚪ BELUM DI-CEK
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[#718379] font-medium mt-0.5">
                            @if($isLocked)
                                Terkunci oleh {{ $dailySummaryData['verifier_name'] ?? 'Supervisor' }} pada {{ \Carbon\Carbon::parse($dailySummaryData['verified_at'])->format('d/m/Y H:i') }}.
                            @else
                                Laporan Rekap Penjualan, Saldo Aplikasi, &amp; Setoran Kasir.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-2 w-full lg:w-auto justify-end flex-wrap">
                    @if(! $isLocked)
                        <button wire:click="openInputModal" style="background-color: #d97706 !important; color: #ffffff !important; border: 1px solid #b45309 !important;" class="px-4 py-2.5 font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer active-press">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span class="text-white">Input / Edit Saldo Aktual</span>
                        </button>

                        <button wire:click="openInputModal" wire:loading.attr="disabled" style="background-color: #3F7A5D !important; color: #ffffff !important; border: 1px solid #32634B !important;" class="px-5 py-2.5 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer active-press">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-white">Validasi &amp; Kunci Rekap</span>
                        </button>
                    @else
                        @if(auth()->user()->can('balance.adjust') || auth()->user()->role?->name === 'OWNER')
                            <button wire:click="unlockReport" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer active-press">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                <span>Buka Kunci (Revisi)</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            @if($status === 'BELUM_DICEK')
                <!-- Unstarted Placeholder Card -->
                <div class="bg-white border-2 border-dashed border-slate-200/90 rounded-2xl p-8 sm:p-12 text-center space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-[#E3EEE8] text-[#3F7A5D] font-black flex items-center justify-center border border-[#3F7A5D]/20 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div class="max-w-md mx-auto space-y-1">
                        <h3 class="text-base sm:text-lg font-black text-[#2C3E35] tracking-tight">Rekapitulasi Harian Belum Dimulai</h3>
                        <p class="text-xs text-[#718379] font-medium leading-relaxed">
                            Penjualan POS pada tanggal <span class="font-bold text-[#2C3E35]">{{ $formattedDate }}</span> terekam otomatis sebesar <span class="font-mono font-extrabold text-[#3F7A5D]">Rp {{ number_format($dailySummaryData['total_penjualan'], 0, ',', '.') }}</span>.
                            Saldo awal e-wallet otomatis ditarik dari saldo akhir kemarin. Klik tombol di bawah untuk mengisi saldo aktual aplikasi.
                        </p>
                    </div>
                    <div>
                        <button wire:click="openInputModal" class="px-6 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer active-press hover-lift">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span>Mulai Cek Rekap Harian</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- PURE READ-ONLY REPORT DOCUMENT VIEW (ENTERPRISE COMPACT) -->
                <div class="space-y-5">
                    
                    <!-- TOP KPI SUMMARY STRIP (4 Executive Cards) -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                        <!-- Card 1: Total Omzet POS -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-[#718379] uppercase tracking-wider">PENJUALAN POS</span>
                                <span class="p-1.5 rounded-lg bg-[#E3EEE8] text-[#3F7A5D]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-base sm:text-lg font-black font-mono text-[#2C3E35] tracking-tight">
                                    Rp {{ number_format($dailySummaryData['total_penjualan'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-[#718379] font-medium">Penjualan POS Otomatis</span>
                            </div>
                        </div>

                        <!-- Card 2: Margin Profit -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-[#718379] uppercase tracking-wider">PROFIT TOKO</span>
                                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-base sm:text-lg font-black font-mono text-[#3F7A5D] tracking-tight">
                                    Rp {{ number_format($dailySummaryData['margin'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-emerald-700 font-medium">Keuntungan Kotor Toko</span>
                            </div>
                        </div>

                        <!-- Card 3: Setoran Tunai Laci Kasir -->
                        <div class="bg-white border border-slate-200 rounded-2xl p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-[#718379] uppercase tracking-wider">WAJIB SETOR LACI</span>
                                <span class="p-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-base sm:text-lg font-black font-mono text-amber-950 tracking-tight">
                                    Rp {{ number_format($dailySummaryData['setoran_tunai'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-amber-800 font-semibold">Setoran Tunai Kasir</span>
                            </div>
                        </div>

                        <!-- Card 4: Total Saldo Digital -->
                        @php
                            $totalSaldoDigital = ($dailySummaryData['dana_saldo_android'] ?? 0) 
                                               + ($dailySummaryData['qris_saldo_android'] ?? 0) 
                                               + ($dailySummaryData['bankmas_saldo_android'] ?? 0) 
                                               + ($dailySummaryData['bca_saldo_android'] ?? 0) 
                                               + ($dailySummaryData['multi_saldo_android'] ?? 0)
                                               + ($dailySummaryData['wahana_saldo_android'] ?? 0);
                        @endphp
                        <div class="bg-white border border-slate-200 rounded-2xl p-3.5 sm:p-4 shadow-xs flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-[#718379] uppercase tracking-wider">TOTAL SALDO AKUN</span>
                                <span class="p-1.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-base sm:text-lg font-black font-mono text-slate-800 tracking-tight">
                                    Rp {{ number_format($totalSaldoDigital, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-[#718379] font-medium">DANA + QRIS + BCA + MAS + Multi + Wahana</span>
                            </div>
                        </div>
                    </div>

                    <!-- MAIN CONTENT GRID (8 cols left / 4 cols right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                        
                        <!-- Left Column: E-Wallet & Bank Tables Grid (8 cols desktop, 2x3 grid inside) -->
                        <div class="lg:col-span-7 xl:col-span-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- 1. DANA Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">DANA</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">E-Wallet / Digital</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Saldo Awal</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['dana_saldo_awal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Top Up Saldo</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['dana_topup'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Saldo Tersedia</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['dana_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penggunaan Transaksi (Trx)</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['dana_trx'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-100/70 font-extrabold text-[#2C3E35]">
                                            <span>Saldo Akhir Sistem</span>
                                            <span class="text-right font-mono">Rp {{ number_format($dailySummaryData['dana_saldo_akhir'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                        <span class="text-[11px]">Saldo Aktual</span>
                                        <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['dana_saldo_android'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['dana_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['dana_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['dana_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['dana_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. QRIS Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">QRIS</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">Gateway Pembayaran</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penjualan QRIS POS</span>
                                            <span class="text-right font-mono font-bold text-[#3F7A5D]">Rp {{ number_format($dailySummaryData['qris_pembayaran'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penerimaan Tarik Tunai</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['qris_tarik_tunai'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Penerimaan QRIS</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['qris_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                            <span class="text-[11px]">Saldo Aktual</span>
                                            <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['qris_saldo_android'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['qris_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['qris_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['qris_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['qris_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. BANK BCA Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">BANK BCA</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">Rekening Utama</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Saldo Awal</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bca_saldo_awal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Top Up Saldo</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bca_topup'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Saldo Tersedia</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['bca_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penggunaan Transaksi (Trx)</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bca_trx'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-100/70 font-extrabold text-[#2C3E35]">
                                            <span>Saldo Akhir Sistem</span>
                                            <span class="text-right font-mono">Rp {{ number_format($dailySummaryData['bca_sisa'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                        <span class="text-[11px]">Saldo Aktual</span>
                                        <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['bca_saldo_android'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['bca_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['bca_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['bca_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['bca_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. BANK MAS Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">BANK MAS</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">Rekening Operasional</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Saldo Awal</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bankmas_saldo_awal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Top Up Saldo</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bankmas_topup'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Saldo Tersedia</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['bankmas_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penggunaan Transaksi (Trx)</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['bankmas_trx'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-100/70 font-extrabold text-[#2C3E35]">
                                            <span>Saldo Akhir Sistem</span>
                                            <span class="text-right font-mono">Rp {{ number_format($dailySummaryData['bankmas_sisa'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                        <span class="text-[11px]">Saldo Aktual</span>
                                        <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['bankmas_saldo_android'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['bankmas_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['bankmas_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['bankmas_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['bankmas_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. MULTI Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">MULTI</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">Distributor Pulsa &amp; PPOB</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Saldo Awal</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['multi_saldo_awal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Top Up Saldo</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['multi_topup'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Saldo Tersedia</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['multi_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penggunaan Transaksi (Trx)</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['multi_trx'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-100/70 font-extrabold text-[#2C3E35]">
                                            <span>Saldo Akhir Sistem</span>
                                            <span class="text-right font-mono">Rp {{ number_format($dailySummaryData['multi_sisa'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                        <span class="text-[11px]">Saldo Aktual</span>
                                        <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['multi_saldo_android'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['multi_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['multi_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['multi_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['multi_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. WAHANA Table -->
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between">
                                    <div class="bg-[#F3F6F4] px-3.5 py-2 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                                        <span class="uppercase tracking-wider">WAHANA</span>
                                        <span class="text-[10px] text-[#718379] font-semibold">Ekspedisi &amp; Keagenan</span>
                                    </div>
                                    <div class="divide-y divide-slate-100 text-xs font-medium flex-1">
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Saldo Awal</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['wahana_saldo_awal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Top Up Saldo</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['wahana_topup'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50/80 font-bold text-[#2C3E35]">
                                            <span>Total Saldo Tersedia</span>
                                            <span class="text-right font-mono font-black">Rp {{ number_format($dailySummaryData['wahana_total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center hover:bg-slate-50">
                                            <span class="text-[#718379]">Penggunaan Transaksi (Trx)</span>
                                            <span class="text-right font-mono font-bold text-[#2C3E35]">Rp {{ number_format($dailySummaryData['wahana_trx'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-100/70 font-extrabold text-[#2C3E35]">
                                            <span>Saldo Akhir Sistem</span>
                                            <span class="text-right font-mono">Rp {{ number_format($dailySummaryData['wahana_sisa'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-amber-100 text-amber-950 font-black border-t border-amber-200">
                                        <span class="text-[11px]">Saldo Aktual</span>
                                        <span class="text-right font-mono text-xs">Rp {{ number_format($dailySummaryData['wahana_saldo_android'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 px-3 py-2 items-center bg-slate-50 border-t border-slate-200">
                                        <span class="text-[11px] font-bold text-[#2C3E35]">Status Pengecekan</span>
                                        <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                            @if($dailySummaryData['wahana_selisih'] == 0)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-600 text-white font-black tracking-wider uppercase">SESUAI</span>
                                            @else
                                                <span class="font-bold {{ $dailySummaryData['wahana_selisih'] > 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $dailySummaryData['wahana_selisih'] > 0 ? '+' : '' }}Rp {{ number_format($dailySummaryData['wahana_selisih'], 0, ',', '.') }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-600 text-white font-black tracking-wider uppercase">ADA SELISIH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Right Column: Sales, Profit, & Settlement (4 cols desktop) -->
                        <div class="lg:col-span-5 xl:col-span-4 space-y-4">
                            
                            <!-- Settlement Box -->
                            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                                <div class="bg-[#2C3E35] text-white px-3.5 py-2.5 font-extrabold text-xs uppercase tracking-wider flex items-center justify-between">
                                    <span>RINCIAN PENJUALAN &amp; SETORAN</span>
                                    <span class="text-[10px] text-emerald-300 font-mono font-bold">OTOMATIS</span>
                                </div>
                                <div class="divide-y divide-slate-100 text-xs">
                                    
                                    <!-- MARGIN -->
                                    <div class="flex items-center justify-between px-3.5 py-2.5 bg-emerald-50/60">
                                        <span class="font-extrabold text-[#2C3E35]">Laba Kotor Toko</span>
                                        <span class="font-mono font-black text-xs sm:text-sm text-[#3F7A5D]">Rp {{ number_format($dailySummaryData['margin'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- PENJUALAN -->
                                    <div class="flex items-center justify-between px-3.5 py-2.5 bg-amber-100/80 text-amber-950 font-extrabold">
                                        <span>Total Omzet Penjualan</span>
                                        <span class="font-mono text-xs sm:text-sm">Rp {{ number_format($dailySummaryData['total_penjualan'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- TARIK TUNAI -->
                                    <div class="flex items-center justify-between px-3.5 py-2 bg-slate-50 font-bold text-[#2C3E35]">
                                        <span>Penerimaan Tarik Tunai</span>
                                        <span class="font-mono text-xs">Rp {{ number_format($dailySummaryData['tarik_tunai_kasir'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- SUBTOTAL NETTO -->
                                    <div class="flex items-center justify-between px-3.5 py-2 bg-white font-extrabold text-rose-700">
                                        <span>Omzet Penjualan Netto</span>
                                        <span class="font-mono text-xs">Rp {{ number_format($dailySummaryData['subtotal_netto'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- QRIS POS -->
                                    <div class="flex items-center justify-between px-3.5 py-2 bg-slate-50 font-medium text-[#2C3E35]">
                                        <span class="text-[#718379]">Penerimaan QRIS</span>
                                        <span class="font-mono text-xs font-bold text-slate-700">Rp {{ number_format($dailySummaryData['qris_pembayaran_pos'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- TUNAI POS -->
                                    <div class="flex items-center justify-between px-3.5 py-2 bg-white font-medium text-[#2C3E35]">
                                        <span class="text-[#718379]">Penerimaan Tunai</span>
                                        <span class="font-mono text-xs font-bold text-slate-700">Rp {{ number_format($dailySummaryData['tunai_pembayaran_pos'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- TRANSFER POS -->
                                    <div class="flex items-center justify-between px-3.5 py-2 bg-white font-medium text-[#2C3E35]">
                                        <span class="text-[#718379]">Penerimaan Transfer Bank</span>
                                        <span class="font-mono text-xs font-bold text-slate-700">Rp {{ number_format($dailySummaryData['transfer_pembayaran_pos'], 0, ',', '.') }}</span>
                                    </div>

                                    <!-- SETORAN TUNAI FISIK (BOX HIJAU ENTERPRISE) -->
                                    <div class="p-4 bg-[#3F7A5D] text-white space-y-1">
                                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 flex items-center justify-between">
                                            <span>WAJIB SETOR UANG TUNAI LACI</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-[#2C3E35] text-emerald-200 font-bold uppercase tracking-wider">FISIK KASIR</span>
                                        </div>
                                        <div class="text-xl sm:text-2xl font-black font-mono tracking-tight text-white">
                                            Rp {{ number_format($dailySummaryData['setoran_tunai'], 0, ',', '.') }}
                                        </div>
                                        <p class="text-[10px] text-emerald-100/90 font-medium">Total uang fisik tunai di laci yang wajib disetorkan kasir pada akhir hari.</p>
                                    </div>

                                </div>
                            </div>

                            <!-- Notes Box -->
                            <div class="bg-white border border-slate-200 rounded-2xl p-3.5 space-y-1.5 shadow-xs">
                                <label class="text-[11px] font-extrabold text-[#2C3E35] uppercase tracking-wider block">Catatan Rekap / Penjelasan Selisih</label>
                                <p class="text-xs text-[#2C3E35] bg-[#F3F6F4] p-3 rounded-xl min-h-[60px] border border-slate-200/80 font-medium italic leading-relaxed">
                                    {{ $notes ? $notes : 'Tidak ada catatan tambahan.' }}
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            @endif
        </div>

        <!-- MODAL DIALOG INPUT SALDO AKTUAL & REKAP HARIAN (PAGINATED & ENTERPRISE POLISHED) -->
        @if($showInputModal)
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
                <div class="bg-white rounded-3xl max-w-3xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4 my-auto transition-all">
                    
                    <!-- Modal Header (Distinct Top Visual Hierarchy) -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#E3EEE8] text-[#3F7A5D] font-extrabold flex items-center justify-center border border-[#3F7A5D]/20 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002-2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-[#2C3E35] tracking-tight uppercase">INPUT SALDO AKTUAL &amp; REKAP HARIAN</h3>
                                <p class="text-xs text-[#718379] font-medium">Tanggal Rekap: <span class="font-bold text-[#2C3E35]">{{ $formattedDate }}</span></p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeInputModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition cursor-pointer" title="Tutup Modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Step Navigation Tabs Bar (Enforced 1-Line Segmented Bar) -->
                    <div class="flex items-center justify-between gap-1.5 p-1.5 bg-[#F3F6F4] rounded-2xl border border-slate-200/80">
                        <button type="button" wire:click="setModalStep(1)" class="flex-1 px-2.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2 cursor-pointer {{ $modalStep === 1 ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:bg-slate-200/60 hover:text-[#2C3E35]' }}">
                            <span class="w-4 h-4 rounded-full text-[10px] shrink-0 flex items-center justify-center font-black {{ $modalStep === 1 ? 'bg-white text-[#3F7A5D]' : 'bg-slate-300 text-slate-700' }}">1</span>
                            <span class="truncate text-[11px] font-black">E-Wallet &amp; QRIS</span>
                        </button>

                        <button type="button" wire:click="setModalStep(2)" class="flex-1 px-2.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2 cursor-pointer {{ $modalStep === 2 ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:bg-slate-200/60 hover:text-[#2C3E35]' }}">
                            <span class="w-4 h-4 rounded-full text-[10px] shrink-0 flex items-center justify-center font-black {{ $modalStep === 2 ? 'bg-white text-[#3F7A5D]' : 'bg-slate-300 text-slate-700' }}">2</span>
                            <span class="truncate text-[11px] font-black">BCA &amp; MAS</span>
                        </button>

                        <button type="button" wire:click="setModalStep(3)" class="flex-1 px-2.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2 cursor-pointer {{ $modalStep === 3 ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:bg-slate-200/60 hover:text-[#2C3E35]' }}">
                            <span class="w-4 h-4 rounded-full text-[10px] shrink-0 flex items-center justify-center font-black {{ $modalStep === 3 ? 'bg-white text-[#3F7A5D]' : 'bg-slate-300 text-slate-700' }}">3</span>
                            <span class="truncate text-[11px] font-black">Multi &amp; Wahana</span>
                        </button>

                        <button type="button" wire:click="setModalStep(4)" class="flex-1 px-2.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2 cursor-pointer {{ $modalStep === 4 ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#718379] hover:bg-slate-200/60 hover:text-[#2C3E35]' }}">
                            <span class="w-4 h-4 rounded-full text-[10px] shrink-0 flex items-center justify-center font-black {{ $modalStep === 4 ? 'bg-white text-[#3F7A5D]' : 'bg-slate-300 text-slate-700' }}">4</span>
                            <span class="truncate text-[11px] font-black">Kasir &amp; Catatan</span>
                        </button>
                    </div>

                    <!-- Live Calculation Helpers -->
                    @php
                        $mDanaAkhir = (float)($danaSaldoAwal ?? 0) + (float)($danaTopup ?? 0) - (float)($danaTrx ?? 0);
                        $mDanaSelisih = (float)($danaSaldoAndroid ?? 0) - $mDanaAkhir;

                        $mQrisExpected = (float)($dailySummaryData['qris_pembayaran'] ?? 0) + (float)($qrisTarikTunai ?? 0);
                        $mQrisSelisih = (float)($qrisSaldoAndroid ?? 0) - $mQrisExpected;

                        $mBcaAkhir = (float)($bcaSaldoAwal ?? 0) + (float)($bcaTopup ?? 0) - (float)($bcaTrx ?? 0);
                        $mBcaSelisih = (float)($bcaSaldoAndroid ?? 0) - $mBcaAkhir;

                        $mBankmasAkhir = (float)($bankmasSaldoAwal ?? 0) + (float)($bankmasTopup ?? 0) - (float)($bankmasTrx ?? 0);
                        $mBankmasSelisih = (float)($bankmasSaldoAndroid ?? 0) - $mBankmasAkhir;

                        $mMultiAkhir = (float)($multiSaldoAwal ?? 0) + (float)($multiTopup ?? 0) - (float)($multiTrx ?? 0);
                        $mMultiSelisih = (float)($multiSaldoAndroid ?? 0) - $mMultiAkhir;

                        $mWahanaAkhir = (float)($wahanaSaldoAwal ?? 0) + (float)($wahanaTopup ?? 0) - (float)($wahanaTrx ?? 0);
                        $mWahanaSelisih = (float)($wahanaSaldoAndroid ?? 0) - $mWahanaAkhir;

                        $mHasDiscrepancy = ($mDanaSelisih != 0 || $mQrisSelisih != 0 || $mBcaSelisih != 0 || $mBankmasSelisih != 0 || $mMultiSelisih != 0 || $mWahanaSelisih != 0);
                    @endphp

                    <!-- Modal Body Form Grid -->
                    <div class="space-y-3.5 max-h-[58vh] overflow-y-auto pr-1">
                        
                        <!-- Live Owner Status Banner -->
                        @if(! $mHasDiscrepancy)
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-xs text-emerald-950 font-bold shadow-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Status Audit Owner: <strong class="text-emerald-700">SEMUA SALDO SESUAI (Rp 0 Selisih)</strong></span>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-black uppercase tracking-wider shadow-xs">SIAP VALIDASI</span>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded-2xl flex items-center justify-between text-xs text-amber-950 font-bold shadow-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Status Audit Owner: <strong class="text-amber-900">TERDAPAT SELISIH SALDO</strong> &mdash; Harap isi catatan di Hal 4.</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-amber-600 text-white text-[10px] font-black uppercase tracking-wider shadow-xs">BUTUH CATATAN</span>
                            </div>
                        @endif

                        <!-- STEP 1: DANA & QRIS -->
                        @if($modalStep === 1)
                            <div class="space-y-3">
                                <div class="text-xs font-black text-[#2C3E35] uppercase tracking-wider pb-1 border-b border-slate-100 flex justify-between items-center">
                                    <span>LANGKAH 1 DARI 4: E-WALLET DANA &amp; QRIS GATEWAY</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    <!-- DANA -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">DANA</span>
                                                <span class="text-[10px] text-[#718379] font-medium uppercase">(E-Wallet)</span>
                                            </div>
                                            @if($mDanaSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mDanaSelisih > 0 ? '+' : '' }}Rp {{ number_format($mDanaSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Saldo Awal <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('danaSaldoAwal', val ? parseInt(val) : 0);" value="{{ $danaSaldoAwal ? number_format((float)$danaSaldoAwal, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-slate-50 font-mono font-bold text-right text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Otomatis diisi dari Saldo Akhir kemarin" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Top Up <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('danaTopup', val ? parseInt(val) : 0);" value="{{ $danaTopup ? number_format((float)$danaTopup, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Trx Dipakai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('danaTrx', val ? parseInt(val) : 0);" value="{{ $danaTrx ? number_format((float)$danaTrx, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual di HP (Android)</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('danaSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $danaSaldoAndroid ? number_format((float)$danaSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- QRIS -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">QRIS</span>
                                                <span class="text-[10px] text-[#718379] font-medium uppercase">(Gateway)</span>
                                            </div>
                                            @if($mQrisSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mQrisSelisih > 0 ? '+' : '' }}Rp {{ number_format($mQrisSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">POS Pembayaran <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <div class="mt-1 p-1.5 bg-slate-100 rounded-lg font-mono font-bold text-right text-xs text-[#3F7A5D]">
                                                    Rp {{ number_format($dailySummaryData['qris_pembayaran'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Tarik Tunai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('qrisTarikTunai', val ? parseInt(val) : 0);" value="{{ $qrisTarikTunai ? number_format((float)$qrisTarikTunai, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual di HP / EDC</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('qrisSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $qrisSaldoAndroid ? number_format((float)$qrisSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- STEP 2: BANK BCA & BANK MAS -->
                        @elseif($modalStep === 2)
                            <div class="space-y-3">
                                <div class="text-xs font-black text-[#2C3E35] uppercase tracking-wider pb-1 border-b border-slate-100 flex justify-between items-center">
                                    <span>LANGKAH 2 DARI 4: BANK BCA &amp; BANK MAS</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    <!-- BANK BCA -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">BANK BCA</span>
                                            </div>
                                            @if($mBcaSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mBcaSelisih > 0 ? '+' : '' }}Rp {{ number_format($mBcaSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Saldo Awal <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bcaSaldoAwal', val ? parseInt(val) : 0);" value="{{ $bcaSaldoAwal ? number_format((float)$bcaSaldoAwal, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-slate-50 font-mono font-bold text-right text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Otomatis diisi dari Saldo Akhir kemarin" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Top Up <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bcaTopup', val ? parseInt(val) : 0);" value="{{ $bcaTopup ? number_format((float)$bcaTopup, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Trx Dipakai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bcaTrx', val ? parseInt(val) : 0);" value="{{ $bcaTrx ? number_format((float)$bcaTrx, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual M-Banking BCA</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bcaSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $bcaSaldoAndroid ? number_format((float)$bcaSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- BANK MAS -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">BANK MAS</span>
                                            </div>
                                            @if($mBankmasSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mBankmasSelisih > 0 ? '+' : '' }}Rp {{ number_format($mBankmasSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Saldo Awal <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bankmasSaldoAwal', val ? parseInt(val) : 0);" value="{{ $bankmasSaldoAwal ? number_format((float)$bankmasSaldoAwal, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-slate-50 font-mono font-bold text-right text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Otomatis diisi dari Saldo Akhir kemarin" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Top Up <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bankmasTopup', val ? parseInt(val) : 0);" value="{{ $bankmasTopup ? number_format((float)$bankmasTopup, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Trx Dipakai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bankmasTrx', val ? parseInt(val) : 0);" value="{{ $bankmasTrx ? number_format((float)$bankmasTrx, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual di HP Bank MAS</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('bankmasSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $bankmasSaldoAndroid ? number_format((float)$bankmasSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- STEP 3: MULTI & WAHANA -->
                        @elseif($modalStep === 3)
                            <div class="space-y-3">
                                <div class="text-xs font-black text-[#2C3E35] uppercase tracking-wider pb-1 border-b border-slate-100 flex justify-between items-center">
                                    <span>LANGKAH 3 DARI 4: SALDO MULTI &amp; EKSPEDISI WAHANA</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    <!-- MULTI -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">MULTI</span>
                                            </div>
                                            @if($mMultiSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mMultiSelisih > 0 ? '+' : '' }}Rp {{ number_format($mMultiSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Saldo Awal <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('multiSaldoAwal', val ? parseInt(val) : 0);" value="{{ $multiSaldoAwal ? number_format((float)$multiSaldoAwal, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-slate-50 font-mono font-bold text-right text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Otomatis diisi dari Saldo Akhir kemarin" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Top Up <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('multiTopup', val ? parseInt(val) : 0);" value="{{ $multiTopup ? number_format((float)$multiTopup, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Trx Dipakai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('multiTrx', val ? parseInt(val) : 0);" value="{{ $multiTrx ? number_format((float)$multiTrx, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual Aplikasi Multi</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('multiSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $multiSaldoAndroid ? number_format((float)$multiSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- WAHANA -->
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-3 flex flex-col justify-between">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full bg-amber-600"></span>
                                                <span class="font-extrabold text-sm text-[#2C3E35]">WAHANA</span>
                                            </div>
                                            @if($mWahanaSelisih == 0)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-emerald-100 text-emerald-800 font-black">SESUAI</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-rose-100 text-rose-800 font-black">{{ $mWahanaSelisih > 0 ? '+' : '' }}Rp {{ number_format($mWahanaSelisih, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 bg-[#F3F6F4]/90 p-2.5 rounded-xl text-[11px] border border-slate-100">
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Saldo Awal <span class="text-[9px] font-normal text-slate-400">(Auto)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('wahanaSaldoAwal', val ? parseInt(val) : 0);" value="{{ $wahanaSaldoAwal ? number_format((float)$wahanaSaldoAwal, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-slate-50 font-mono font-bold text-right text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-emerald-500" title="Otomatis diisi dari Saldo Akhir kemarin" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Top Up <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('wahanaTopup', val ? parseInt(val) : 0);" value="{{ $wahanaTopup ? number_format((float)$wahanaTopup, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                            <div>
                                                <span class="text-[#718379] font-bold block text-[10px] uppercase">Trx Dipakai <span class="text-[9px] font-normal text-slate-400">(Opsional)</span></span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('wahanaTrx', val ? parseInt(val) : 0);" value="{{ $wahanaTrx ? number_format((float)$wahanaTrx, 0, ',', '.') : '' }}" placeholder="0" class="w-full mt-1 p-1.5 border border-slate-200 rounded-lg bg-white font-mono font-bold text-right text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                                            </div>
                                        </div>

                                        <div class="p-3 bg-amber-50/90 border border-amber-300 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-black text-amber-950 text-xs">Saldo Aktual Agen Wahana</label>
                                                <span class="text-[9px] font-extrabold text-amber-800 bg-amber-200/80 px-1.5 py-0.5 rounded uppercase">Utama</span>
                                            </div>
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 font-mono font-extrabold text-xs text-amber-800 pointer-events-none">Rp</span>
                                                <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('wahanaSaldoAndroid', val ? parseInt(val) : 0);" value="{{ $wahanaSaldoAndroid ? number_format((float)$wahanaSaldoAndroid, 0, ',', '.') : '' }}" wire:keydown.enter.prevent="nextModalStep" class="w-full pl-9 pr-3 py-2 border border-amber-300 rounded-xl bg-white font-mono font-black text-right text-sm text-amber-950 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- STEP 4: KASIR & CATATAN -->
                        @elseif($modalStep === 4)
                            <div class="space-y-3">
                                <div class="text-xs font-black text-[#2C3E35] uppercase tracking-wider pb-1 border-b border-slate-100 flex justify-between items-center">
                                    <span>LANGKAH 4 DARI 4: TARIK TUNAI KASIR &amp; FINALISASI REKAP</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-2">
                                        <label class="block font-extrabold text-xs text-[#2C3E35] uppercase tracking-wider">Tarik Tunai Kasir Toko</label>
                                        <div class="relative flex items-center">
                                            <span class="absolute left-3 font-mono font-extrabold text-xs text-slate-500 pointer-events-none">Rp</span>
                                            <input type="text" x-data x-on:input="let val = $el.value.replace(/\D/g, ''); $el.value = val ? parseInt(val).toLocaleString('id-ID') : ''; $wire.set('tarikTunaiKasir', val ? parseInt(val) : 0);" value="{{ $tarikTunaiKasir ? number_format((float)$tarikTunaiKasir, 0, ',', '.') : '' }}" placeholder="0" class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl bg-[#F3F6F4] font-mono font-bold text-right text-sm focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" />
                                        </div>
                                        <span class="text-[10px] text-[#718379]">Nominal tunai ditarik kasir untuk setoran/operasional.</span>
                                    </div>

                                    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs space-y-2">
                                        <label class="block font-extrabold text-xs text-[#2C3E35] uppercase tracking-wider">Catatan / Penjelasan Selisih</label>
                                        <textarea wire:model="notes" rows="2" placeholder="Tuliskan alasan jika ada selisih saldo..." class="w-full p-2.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-xs font-medium text-[#2C3E35] leading-normal focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] transition resize-none"></textarea>
                                    </div>
                                </div>

                                <!-- Executive Summary Overview Grid -->
                                <div class="bg-[#F3F6F4]/90 border border-slate-200 p-3.5 rounded-2xl space-y-2 shadow-xs">
                                    <div class="text-xs font-black text-[#2C3E35] uppercase tracking-wider border-b border-slate-200 pb-1.5 flex justify-between items-center">
                                        <span>Ringkasan Selisih 6 Akun Saldo</span>
                                        <span class="font-mono text-[10px] text-[#718379] font-bold">HASIL AUDIT OWNER</span>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs font-extrabold">
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mDanaSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>DANA:</span>
                                            <span class="font-mono text-xs">{{ $mDanaSelisih == 0 ? 'SESUAI' : ($mDanaSelisih > 0 ? '+' : '').'Rp '.number_format($mDanaSelisih,0,',','.') }}</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mQrisSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>QRIS:</span>
                                            <span class="font-mono text-xs">{{ $mQrisSelisih == 0 ? 'SESUAI' : ($mQrisSelisih > 0 ? '+' : '').'Rp '.number_format($mQrisSelisih,0,',','.') }}</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mBcaSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>BCA:</span>
                                            <span class="font-mono text-xs">{{ $mBcaSelisih == 0 ? 'SESUAI' : ($mBcaSelisih > 0 ? '+' : '').'Rp '.number_format($mBcaSelisih,0,',','.') }}</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mBankmasSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>MAS:</span>
                                            <span class="font-mono text-xs">{{ $mBankmasSelisih == 0 ? 'SESUAI' : ($mBankmasSelisih > 0 ? '+' : '').'Rp '.number_format($mBankmasSelisih,0,',','.') }}</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mMultiSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>MULTI:</span>
                                            <span class="font-mono text-xs">{{ $mMultiSelisih == 0 ? 'SESUAI' : ($mMultiSelisih > 0 ? '+' : '').'Rp '.number_format($mMultiSelisih,0,',','.') }}</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl border flex items-center justify-between shadow-2xs {{ $mWahanaSelisih == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                                            <span>WAHANA:</span>
                                            <span class="font-mono text-xs">{{ $mWahanaSelisih == 0 ? 'SESUAI' : ($mWahanaSelisih > 0 ? '+' : '').'Rp '.number_format($mWahanaSelisih,0,',','.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Modal Footer Controls -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-2">
                        <!-- Left Action: Close -->
                        <button type="button" wire:click="closeInputModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#2C3E35] font-extrabold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>

                        <!-- Middle Step Pagination Controls -->
                        <div class="flex items-center gap-2">
                            @if($modalStep > 1)
                                <button type="button" wire:click="prevModalStep" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#2C3E35] font-extrabold text-xs rounded-xl transition flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                                    <span>Kembali</span>
                                </button>
                            @endif

                            <span class="text-xs font-mono font-extrabold text-[#718379] px-1.5">
                                Hal {{ $modalStep }} / 4
                            </span>

                            @if($modalStep < 4)
                                <button type="button" wire:click="nextModalStep" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <span>Lanjut</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            @endif
                        </div>

                        <!-- Right Actions: Save Draft & Validate -->
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="saveInputModal" style="background-color: #d97706 !important; color: #ffffff !important; border: 1px solid #b45309 !important;" class="px-4 py-2 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer active-press">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <span class="text-white">Simpan Draf</span>
                            </button>

                            <button type="button" wire:click="validateAndLock" wire:loading.attr="disabled" style="background-color: #3F7A5D !important; color: #ffffff !important; border: 1px solid #32634B !important;" class="px-4 py-2 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer active-press">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-white">Validasi &amp; Kunci</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endif

    <!-- Tab 1: Sales, Trend, Top Products, & Categories -->
    @elseif($type === 'sales')
        <div class="space-y-6">
            <!-- Daily Sales Trend Chart Bar (7 Days) -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#2C3E35] uppercase tracking-wider">Grafik Omzet Harian (7 Hari Terakhir)</h3>
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

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 sm:gap-6">
                <!-- Top Selling Products -->
                <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden space-y-3">
                    <div class="p-4 sm:p-5 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-[#2C3E35] uppercase tracking-wider">Top 5 Produk Terlaris</h3>
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
                                                <span class="w-6 h-6 rounded-lg text-[11px] font-extrabold flex items-center justify-center {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-600')) }}">
                                                    #{{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <div class="font-bold text-[#2C3E35]">{{ $prod->product_name }}</div>
                                                    <div class="text-[10px] text-[#718379] font-mono">Kode: {{ $prod->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono font-extrabold text-[#2C3E35]">
                                            {{ number_format($prod->total_qty, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-extrabold text-[#3F7A5D]">
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
                <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-[#2C3E35] uppercase tracking-wider border-b border-slate-100 pb-3">Penjualan Berdasarkan Kategori</h3>
                    
                    <div class="space-y-3 text-xs">
                        @forelse($categoryBreakdown as $cat)
                            @php
                                $catPct = $metrics['omzet'] > 0 ? ($cat->total_omzet / $metrics['omzet']) * 100 : 0;
                            @endphp
                            <div class="space-y-1">
                                <div class="flex justify-between items-center font-semibold">
                                    <span class="text-[#2C3E35] font-bold">{{ $cat->category_name }} ({{ $cat->total_qty }} Unit)</span>
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

    <!-- Tab 3: Inventory Valuation -->
    @elseif($type === 'inventory')
        <div class="space-y-6">
            <!-- Contextual Inventory KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Modal Stok (HPP)</div>
                    <div class="text-2xl font-extrabold font-mono tracking-tight text-[#2C3E35]">
                        Rp {{ number_format($inventoryValuation['total_cost'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#718379]">Modal mengendap pada produk stok fisik toko</div>
                </div>

                <div class="bg-white border border-[#3F7A5D]/40 bg-gradient-to-b from-[#E3EEE8]/40 to-white rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#3F7A5D] font-extrabold uppercase tracking-wider">Potensi Omzet Stok</div>
                    <div class="text-2xl font-extrabold font-mono tracking-tight text-[#3F7A5D]">
                        Rp {{ number_format($inventoryValuation['total_retail'], 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-[#718379]">Potensi omzet jika seluruh stok terjual</div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Total Fisik Unit Stok</div>
                    <div class="text-2xl font-extrabold font-mono tracking-tight text-[#2C3E35]">
                        {{ number_format($inventoryValuation['total_units'], 0, ',', '.') }} Unit
                    </div>
                    <div class="text-[11px] text-[#718379]">Dari {{ number_format($inventoryCount, 0, ',', '.') }} jenis produk</div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm space-y-1">
                    <div class="text-[11px] text-[#718379] font-extrabold uppercase tracking-wider">Stok Menipis / Habis</div>
                    <div class="text-2xl font-extrabold font-mono tracking-tight {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-[#3F7A5D]' }}">
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
                <h3 class="text-sm font-extrabold text-[#2C3E35] uppercase tracking-wider">Rincian Pembayaran Masuk</h3>
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
                            <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">{{ $method }}</td>
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
    @endif
</div>
