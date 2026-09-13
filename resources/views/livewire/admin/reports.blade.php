<div class="space-y-6">
    <!-- Page Header & Period Filter Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">Laporan Toko</h1>
            <p class="text-xs sm:text-sm text-[#718379] font-medium mt-0.5">Analisis lengkap performa penjualan, margin, kasir, rekonsiliasi harian, dan saldo toko.</p>
        </div>

        <!-- Filter Period & Print Control -->
        <div class="flex items-center gap-2 flex-wrap text-sm">
            @if($type === 'daily_summary')
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-[#718379] uppercase tracking-wider">Tanggal Rekap:</label>
                    <input type="date" wire:model.live="summaryDate" class="h-11 px-3.5 py-2 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-extrabold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer text-sm" />
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

            <button onclick="window.print()" class="h-11 px-4 py-2 bg-[#F3F6F4] hover:bg-[#E3EEE8] text-[#3F7A5D] border border-slate-200 font-extrabold rounded-xl transition flex items-center gap-1.5 cursor-pointer active-press">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Export</span>
            </button>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 pb-3 text-xs sm:text-sm font-bold print:hidden overflow-x-auto no-scrollbar whitespace-nowrap">
        @foreach([
            'sales' => 'Penjualan & Produk Terlaris',
            'daily_summary' => 'Summary Harian (Tutup Kas)',
            'cashier' => 'Performa Kasir',
            'inventory' => 'Stok & Valuasi Barang',
            'payment' => 'Metode Pembayaran',
            'balance' => 'Saldo Toko'
        ] as $key => $label)
            <a href="/admin/reports/{{ $key }}" class="px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-xl transition shrink-0 {{ $type === $key ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#2C3E35]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Contextual Executive KPI Cards for Sales, Cashier, and Payment tabs -->
    @if(in_array($type, ['sales', 'cashier', 'payment']))
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
                            <h2 class="text-lg font-black text-[#2C3E35] tracking-tight uppercase">SUMMARY REPORT</h2>
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
                                Rekapitulasi kasir, saldo e-wallet, &amp; setoran uang fisik harian.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-2 w-full lg:w-auto justify-end flex-wrap">
                    @if($isLocked)
                        @if(auth()->user()->can('balance.adjust') || auth()->user()->role?->name === 'OWNER')
                            <button wire:click="unlockReport" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer active-press">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                <span>Buka Kunci (Revisi)</span>
                            </button>
                        @endif
                    @else
                        <button wire:click="saveDraft" wire:loading.attr="disabled" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer active-press">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>Simpan Draf</span>
                        </button>

                        <button wire:click="validateAndLock" wire:loading.attr="disabled" class="px-5 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer active-press">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Validasi &amp; Kunci Rekap</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Main Grid: 2 Columns (Digital E-Wallet Left, Settlement Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                <!-- Left Column: E-Wallet & Bank Tables (8 cols) -->
                <div class="lg:col-span-7 xl:col-span-8 space-y-5">
                    
                    <!-- 1. DANA Table -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-[#F3F6F4] px-4 py-2.5 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                            <span class="uppercase tracking-wider">DANA (E-Wallet)</span>
                            <span class="text-[10px] text-[#718379] font-semibold">Saldo Digital</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs font-medium">
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">SALDO AWAL</span>
                                <input type="number" step="1" wire:model.live="danaSaldoAwal" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TOP UP SALDO</span>
                                <input type="number" step="1" wire:model.live="danaTopup" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-slate-50 font-bold text-[#2C3E35]">
                                <span>TOTAL SALDO</span>
                                <span class="text-right font-mono font-black text-sm">Rp {{ number_format($dailySummaryData['dana_total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TRANSAKSI TERPAKAI (TRX)</span>
                                <input type="number" step="1" wire:model.live="danaTrx" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black">
                                <span>SALDO AKHIR</span>
                                <span class="text-right font-mono text-sm">Rp {{ number_format($dailySummaryData['dana_saldo_akhir'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black border-t border-amber-300">
                                <span>SALDO ANDROID</span>
                                <input type="number" step="1" wire:model.live="danaSaldoAndroid" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-black px-2 py-1 bg-amber-100 border border-amber-300 rounded-lg text-xs text-amber-950 disabled:opacity-85 disabled:bg-amber-100 disabled:cursor-not-allowed" />
                            </div>
                        </div>
                    </div>

                    <!-- 2. QRIS Table -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-[#F3F6F4] px-4 py-2.5 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                            <span class="uppercase tracking-wider">QRIS</span>
                            <span class="text-[10px] text-[#718379] font-semibold">Payment Gateway</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs font-medium">
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">PEMBAYARAN (POS AUTOMATED)</span>
                                <span class="text-right font-mono font-extrabold text-[#3F7A5D]">Rp {{ number_format($dailySummaryData['qris_pembayaran'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TARIK TUNAI</span>
                                <input type="number" step="1" wire:model.live="qrisTarikTunai" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-slate-50 font-bold text-[#2C3E35]">
                                <span>TOTAL</span>
                                <span class="text-right font-mono font-black text-sm">Rp {{ number_format($dailySummaryData['qris_total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black">
                                <span>SALDO ANDROID</span>
                                <input type="number" step="1" wire:model.live="qrisSaldoAndroid" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-black px-2 py-1 bg-amber-100 border border-amber-300 rounded-lg text-xs text-amber-950 disabled:opacity-85 disabled:bg-amber-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black border-t border-amber-300">
                                <span>CEK STATUS SELISIH</span>
                                <div class="text-right font-mono text-xs flex items-center justify-end gap-1.5">
                                    <span>Rp {{ number_format($dailySummaryData['qris_cek'], 0, ',', '.') }}</span>
                                    @if($dailySummaryData['qris_saldo_android'] == $dailySummaryData['qris_total'])
                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-emerald-600 text-white font-bold uppercase">SESUAI</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-rose-600 text-white font-bold uppercase">ADA SELISIH</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. BANK MAS Table -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-[#F3F6F4] px-4 py-2.5 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                            <span class="uppercase tracking-wider">BANK MAS</span>
                            <span class="text-[10px] text-[#718379] font-semibold">Rekening Operasional</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs font-medium">
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">SALDO AWAL</span>
                                <input type="number" step="1" wire:model.live="bankmasSaldoAwal" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs text-rose-700 disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TOP UP SALDO</span>
                                <input type="number" step="1" wire:model.live="bankmasTopup" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-slate-50 font-bold text-[#2C3E35]">
                                <span>TOTAL SALDO</span>
                                <span class="text-right font-mono font-black text-sm">Rp {{ number_format($dailySummaryData['bankmas_total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TRANSAKSI TERPAKAI (TRX)</span>
                                <input type="number" step="1" wire:model.live="bankmasTrx" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs text-rose-700 disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black">
                                <span>SALDO AKHIR</span>
                                <span class="text-right font-mono text-sm">Rp {{ number_format($dailySummaryData['bankmas_sisa'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black border-t border-amber-300">
                                <span>SALDO ANDROID</span>
                                <input type="number" step="1" wire:model.live="bankmasSaldoAndroid" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-black px-2 py-1 bg-amber-100 border border-amber-300 rounded-lg text-xs text-amber-950 disabled:opacity-85 disabled:bg-amber-100 disabled:cursor-not-allowed" />
                            </div>
                        </div>
                    </div>

                    <!-- 4. MULTI Table -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-[#F3F6F4] px-4 py-2.5 border-b border-slate-200 flex items-center justify-between font-extrabold text-xs text-[#2C3E35]">
                            <span class="uppercase tracking-wider">MULTI (PPOB / SERVER)</span>
                            <span class="text-[10px] text-[#718379] font-semibold">Distributor Pulsa/Kuota</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs font-medium">
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">SALDO AWAL</span>
                                <input type="number" step="1" wire:model.live="multiSaldoAwal" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TOP UP SALDO</span>
                                <input type="number" step="1" wire:model.live="multiTopup" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-slate-50 font-bold text-[#2C3E35]">
                                <span>TOTAL SALDO</span>
                                <span class="text-right font-mono font-black text-sm">Rp {{ number_format($dailySummaryData['multi_total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center hover:bg-slate-50">
                                <span class="font-bold text-[#2C3E35]">TRANSAKSI TERPAKAI (TRX)</span>
                                <input type="number" step="1" wire:model.live="multiTrx" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-extrabold px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed" />
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black">
                                <span>SALDO AKHIR</span>
                                <span class="text-right font-mono text-sm">Rp {{ number_format($dailySummaryData['multi_sisa'], 0, ',', '.') }}</span>
                            </div>
                            <div class="grid grid-cols-2 p-2.5 items-center bg-amber-200 text-amber-950 font-black border-t border-amber-300">
                                <span>SALDO ANDROID</span>
                                <input type="number" step="1" wire:model.live="multiSaldoAndroid" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-black px-2 py-1 bg-amber-100 border border-amber-300 rounded-lg text-xs text-amber-950 disabled:opacity-85 disabled:bg-amber-100 disabled:cursor-not-allowed" />
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Sales, Profit, & Physical Cash Settlement (4/5 cols) -->
                <div class="lg:col-span-5 xl:col-span-4 space-y-5">
                    
                    <!-- Settlement Box -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-[#2C3E35] text-white px-4 py-3 font-extrabold text-xs uppercase tracking-wider flex items-center justify-between">
                            <span>REKAPITULASI PENJUALAN</span>
                            <span class="text-[10px] text-emerald-400 font-mono">AUTOMATED</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            
                            <!-- MARGIN -->
                            <div class="flex items-center justify-between p-3 bg-slate-50">
                                <span class="font-extrabold text-[#2C3E35]">MARGIN (PROFIT TOKO)</span>
                                <span class="font-mono font-black text-sm text-[#3F7A5D]">Rp {{ number_format($dailySummaryData['margin'], 0, ',', '.') }}</span>
                            </div>

                            <!-- PENJUALAN (Kuning Highlight) -->
                            <div class="flex items-center justify-between p-3 bg-amber-200 text-amber-950 font-black">
                                <span>TOTAL PENJUALAN</span>
                                <span class="font-mono text-sm">Rp {{ number_format($dailySummaryData['total_penjualan'], 0, ',', '.') }}</span>
                            </div>

                            <!-- TARIK TUNAI -->
                            <div class="flex items-center justify-between p-3 bg-amber-200 text-amber-950 font-black border-t border-amber-300">
                                <span>TARIK TUNAI KASIR</span>
                                <div class="w-36">
                                    <input type="number" step="1" wire:model.live="tarikTunaiKasir" {{ $isLocked ? 'disabled' : '' }} class="w-full text-right font-mono font-black px-2 py-1 bg-amber-100 border border-amber-300 rounded-lg text-xs text-amber-950 disabled:opacity-85 disabled:bg-amber-100 disabled:cursor-not-allowed" />
                                </div>
                            </div>

                            <!-- SUBTOTAL MERAH -->
                            <div class="flex items-center justify-between p-3 bg-white font-extrabold text-rose-700">
                                <span>SUBTOTAL NETTO</span>
                                <span class="font-mono text-sm">Rp {{ number_format($dailySummaryData['subtotal_netto'], 0, ',', '.') }}</span>
                            </div>

                            <!-- QRIS -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 font-bold text-[#2C3E35]">
                                <span>PEMBAYARAN QRIS</span>
                                <span class="font-mono text-sm font-extrabold text-slate-700">Rp {{ number_format($dailySummaryData['qris_pembayaran_pos'], 0, ',', '.') }}</span>
                            </div>

                            <!-- TUNAI POS -->
                            <div class="flex items-center justify-between p-3 bg-white font-bold text-[#2C3E35]">
                                <span>PEMBAYARAN TUNAI</span>
                                <span class="font-mono text-sm font-extrabold text-slate-700">Rp {{ number_format($dailySummaryData['tunai_pembayaran_pos'], 0, ',', '.') }}</span>
                            </div>

                            <!-- TRANSFER -->
                            <div class="flex items-center justify-between p-3 bg-white font-bold text-[#2C3E35]">
                                <span>PEMBAYARAN TRANSFER</span>
                                <span class="font-mono text-sm font-extrabold text-slate-700">Rp {{ number_format($dailySummaryData['transfer_pembayaran_pos'], 0, ',', '.') }}</span>
                            </div>

                            <!-- SETORAN TUNAI FISIK (BOX HIJAU EMERALD) -->
                            <div class="p-4 bg-emerald-600 text-white space-y-1">
                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-100 flex items-center justify-between">
                                    <span>SETORAN TUNAI LACI KASIR</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-800 text-emerald-200 font-bold">UANG FISIK</span>
                                </div>
                                <div class="text-2xl font-black font-mono tracking-tight">
                                    Rp {{ number_format($dailySummaryData['setoran_tunai'], 0, ',', '.') }}
                                </div>
                                <p class="text-[10px] text-emerald-100 font-medium">Uang fisik tunai yang harus disetorkan dari laci kasir pada akhir hari.</p>
                            </div>

                        </div>
                    </div>

                    <!-- Notes Box -->
                    <div class="bg-white border-2 border-slate-200/90 rounded-2xl p-4 space-y-2 shadow-xs">
                        <label class="text-xs font-extrabold text-[#2C3E35] uppercase tracking-wider block">Catatan Rekap / Penjelasan Selisih</label>
                        <textarea wire:model="notes" {{ $isLocked ? 'disabled' : '' }} rows="3" placeholder="Masukkan catatan tambahan atau penjelasan jika terdapat selisih kas..." class="w-full p-2.5 border border-slate-200 rounded-xl text-xs bg-[#F3F6F4] text-[#2C3E35] font-medium focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] disabled:opacity-75 disabled:bg-slate-100 disabled:cursor-not-allowed"></textarea>
                    </div>

                </div>
            </div>
        </div>

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

    <!-- Tab 2: Cashier Performance -->
    @elseif($type === 'cashier')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#2C3E35] uppercase tracking-wider">Performa &amp; Produktivitas Kasir</h3>
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
                                <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                    {{ $cashier->cashier_name }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#2C3E35]">
                                    {{ number_format($cashier->total_sales, 0, ',', '.') }} Trx
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#2C3E35]">
                                    Rp {{ number_format($cashier->total_omzet, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#3F7A5D]">
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
