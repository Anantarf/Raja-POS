<div class="space-y-6">
    <!-- Page Header Banner -->
    <div class="bg-gradient-to-r from-[#3F7A5D]/10 via-[#3F7A5D]/5 to-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-0.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#2C3E35] tracking-tight">Pengaturan Toko &amp; Sistem</h1>
            <p class="text-xs sm:text-sm text-[#52645B] font-medium leading-relaxed">Kelola profil toko, pengguna sistem, role &amp; hak akses, metode pembayaran, dan lokasi cabang.</p>
        </div>

        <div class="flex items-center gap-2 text-xs font-extrabold text-[#3F7A5D] bg-[#E3EEE8] px-3 py-2 rounded-xl border border-[#3F7A5D]/30 shrink-0">
            <svg class="w-4 h-4 text-[#3F7A5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <span>Akses Hak Khusus Owner</span>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex items-center gap-1.5 sm:gap-2 border-b border-slate-200/80 pb-3 text-xs sm:text-sm font-extrabold overflow-x-auto no-scrollbar whitespace-nowrap">
        @foreach([
            'STORE_SETTINGS' => ['Profil Toko', '/admin/settings/store-settings'],
            'PRINTER_SETTINGS' => ['Printer & Struk', '/admin/settings/printer-settings'],
            'USERS' => ['Pengguna & Akses', '/admin/settings/users'],
            'ROLES' => ['Role & Hak Akses', '/admin/settings/roles'],
            'PAYMENT_METHODS' => ['Metode Pembayaran', '/admin/settings/payment-methods'],
            'LOCATIONS' => ['Lokasi Cabang', '/admin/settings/locations'],
        ] as $tab => [$label, $href])
            <a href="{{ $href }}" class="px-3 py-2 rounded-xl transition-all duration-200 shrink-0 {{ $activeTab === $tab ? 'bg-[#3F7A5D] text-white shadow-xs' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#2C3E35]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <!-- Tab 1: Store Settings -->
    @if($activeTab === 'STORE_SETTINGS')
        @php
            $settingMap = $settings->pluck('value', 'key');
            $storeSettings = [
                ['label' => 'Nama Toko', 'value' => $settingMap->get('store_name', 'Raja Aksesoris'), 'hint' => 'Nama resmi toko yang tampil di sistem dan struk belanja.'],
                ['label' => 'Mata Uang', 'value' => $settingMap->get('currency', 'Rupiah (Rp)'), 'hint' => 'Format simbol mata uang untuk transaksi & laporan.'],
                ['label' => 'Zona Waktu', 'value' => $settingMap->get('timezone', 'Asia/Jakarta (WIB)'), 'hint' => 'Acuan waktu tanggal transaksi, laporan harian, dan audit.'],
                ['label' => 'Mode Transaksi', 'value' => 'Multi-Payment & Split Account', 'hint' => 'Mendukung Tunai, Bank, QRIS, & E-Wallet dalam 1 transaksi.'],
                ['label' => 'Validasi Stok', 'value' => 'Server-Side Strict Guard', 'hint' => 'Mencegah transaksi jika stok barang fisik di toko kosong.'],
            ];
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Main Panel -->
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-5">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3.5">
                    <div>
                        <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Profil &amp; Preferensi Toko</h3>
                        <p class="text-sm text-[#718379] font-medium mt-0.5">Konfigurasi dasar operasional kasir, cetak struk, dan pencatatan laporan.</p>
                    </div>
                    <span class="px-3 py-1 rounded-lg bg-[#E3EEE8] text-[#3F7A5D] text-xs font-extrabold uppercase tracking-wider border border-[#3F7A5D]/20">Aktif &amp; Berjalan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach($storeSettings as $item)
                        <div class="rounded-xl border border-slate-200/80 bg-[#F3F6F4]/60 hover:border-[#3F7A5D]/30 p-4 space-y-1.5 transition-all">
                            <div class="text-xs font-extrabold uppercase tracking-wider text-[#3F7A5D]">{{ $item['label'] }}</div>
                            <div class="text-base font-extrabold text-[#2C3E35] font-mono tracking-tight">{{ filled($item['value']) ? $item['value'] : 'Belum diisi' }}</div>
                            <div class="text-sm text-[#718379] leading-relaxed font-medium">{{ $item['hint'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Info Panel -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3.5">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Panduan &amp; Spesifikasi Sistem</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Ringkasan status integrasi operasional toko.</p>
                </div>

                <div class="space-y-3.5 text-sm font-medium">
                    <div class="p-3.5 rounded-xl bg-[#E3EEE8]/60 border border-[#3F7A5D]/30 text-[#3F7A5D] leading-relaxed font-semibold">
                        Perubahan preferensi toko ini berlaku otomatis di seluruh terminal kasir dan laporan finansial.
                    </div>
                    <div class="space-y-2.5 pt-1">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-[#718379] font-bold">Kertas Printer Struk:</span>
                            <span class="font-extrabold text-[#2C3E35] font-mono">{{ $settingMap->get('receipt_paper_width', '58mm') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                            <span class="text-[#718379] font-bold">Acuan Zona Waktu:</span>
                            <span class="font-extrabold text-[#2C3E35] font-mono">Asia/Jakarta (WIB)</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-[#718379] font-bold">Status Server Toko:</span>
                            <span class="font-extrabold text-[#3F7A5D] font-mono uppercase">ONLINE &amp; SYNCED</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Tab 2: PRINTER & STRUK SETTINGS -->
    @elseif($activeTab === 'PRINTER_SETTINGS')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Form Panel -->
            <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Konfigurasi Printer &amp; Struk Thermal</h3>
                        <p class="text-xs text-[#718379] font-medium mt-0.5">Atur ukuran kertas, metode cetak, dan tampilan teks header/footer struk.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-[#E3EEE8] text-[#3F7A5D] text-[11px] font-extrabold uppercase tracking-wider border border-[#3F7A5D]/20 shrink-0">Thermal POS</span>
                </div>

                <form wire:submit.prevent="savePrinterSettings" class="space-y-4">
                    <!-- 1. Selection Card: Ukuran Kertas Thermal -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-2">1. Pilih Ukuran Kertas Thermal *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div @click="$wire.set('receiptPaperWidth', '58mm')" class="relative flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $receiptPaperWidth === '58mm' ? 'border-[#3F7A5D] bg-[#E3EEE8]/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <input type="radio" wire:model.live="receiptPaperWidth" value="58mm" class="hidden">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-9 h-9 rounded-lg {{ $receiptPaperWidth === '58mm' ? 'bg-[#3F7A5D] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-black text-xs shrink-0 transition-colors">
                                        58mm
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs sm:text-sm text-[#2C3E35]">Kertas Kecil (58mm)</div>
                                        <div class="text-[11px] text-[#718379] font-medium leading-snug truncate">Bluetooth portable / mini kasir</div>
                                    </div>
                                </div>
                                @if($receiptPaperWidth === '58mm')
                                    <div class="w-5 h-5 rounded-full bg-[#3F7A5D] text-white flex items-center justify-center shrink-0 ml-1 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0 ml-1"></div>
                                @endif
                            </div>

                            <div @click="$wire.set('receiptPaperWidth', '80mm')" class="relative flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $receiptPaperWidth === '80mm' ? 'border-[#3F7A5D] bg-[#E3EEE8]/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <input type="radio" wire:model.live="receiptPaperWidth" value="80mm" class="hidden">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-9 h-9 rounded-lg {{ $receiptPaperWidth === '80mm' ? 'bg-[#3F7A5D] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-black text-xs shrink-0 transition-colors">
                                        80mm
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs sm:text-sm text-[#2C3E35]">Kertas Lebar (80mm)</div>
                                        <div class="text-[11px] text-[#718379] font-medium leading-snug truncate">Printer meja / auto-cutter</div>
                                    </div>
                                </div>
                                @if($receiptPaperWidth === '80mm')
                                    <div class="w-5 h-5 rounded-full bg-[#3F7A5D] text-white flex items-center justify-center shrink-0 ml-1 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0 ml-1"></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. Selection Card: Mode Cetak Utama -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-2">2. Pilih Metode Cetak Utama *</label>
                        <div class="grid grid-cols-1 gap-2.5">
                            <!-- Option A: Browser -->
                            <div @click="$wire.set('printMode', 'BROWSER')" class="relative flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $printMode === 'BROWSER' ? 'border-[#3F7A5D] bg-[#E3EEE8]/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <input type="radio" wire:model.live="printMode" value="BROWSER" class="hidden">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-9 h-9 rounded-lg {{ $printMode === 'BROWSER' ? 'bg-[#3F7A5D] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs sm:text-sm text-[#2C3E35] flex items-center gap-2 flex-wrap">
                                            <span>Browser Print Dialog</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-100 text-slate-700 font-extrabold border border-slate-200/80 uppercase tracking-wider">PC / Laptop</span>
                                        </div>
                                        <div class="text-[11px] text-[#718379] font-medium leading-snug mt-0.5">Dialog cetak bawaan browser Chrome/Edge/Firefox.</div>
                                    </div>
                                </div>
                                @if($printMode === 'BROWSER')
                                    <div class="w-5 h-5 rounded-full bg-[#3F7A5D] text-white flex items-center justify-center shrink-0 ml-1 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0 ml-1"></div>
                                @endif
                            </div>

                            <!-- Option B: Direct Web Bluetooth -->
                            <div @click="$wire.set('printMode', 'WEB_BLUETOOTH')" class="relative flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $printMode === 'WEB_BLUETOOTH' ? 'border-[#3F7A5D] bg-[#E3EEE8]/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <input type="radio" wire:model.live="printMode" value="WEB_BLUETOOTH" class="hidden">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-9 h-9 rounded-lg {{ $printMode === 'WEB_BLUETOOTH' ? 'bg-[#3F7A5D] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs sm:text-sm text-[#2C3E35] flex items-center gap-2 flex-wrap">
                                            <span>Direct Web Bluetooth</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-100 text-emerald-800 font-extrabold border border-emerald-200/80 uppercase tracking-wider">1-Click Direct Print</span>
                                        </div>
                                        <div class="text-[11px] text-[#718379] font-medium leading-snug mt-0.5">Cetak langsung ke printer Bluetooth tanpa jendela pop-up print.</div>
                                    </div>
                                </div>
                                @if($printMode === 'WEB_BLUETOOTH')
                                    <div class="w-5 h-5 rounded-full bg-[#3F7A5D] text-white flex items-center justify-center shrink-0 ml-1 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0 ml-1"></div>
                                @endif
                            </div>

                            <!-- Option C: RawBT Android -->
                            <div @click="$wire.set('printMode', 'RAWBT')" class="relative flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 {{ $printMode === 'RAWBT' ? 'border-[#3F7A5D] bg-[#E3EEE8]/40 shadow-2xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <input type="radio" wire:model.live="printMode" value="RAWBT" class="hidden">
                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                    <div class="w-9 h-9 rounded-lg {{ $printMode === 'RAWBT' ? 'bg-[#3F7A5D] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-xs sm:text-sm text-[#2C3E35] flex items-center gap-2 flex-wrap">
                                            <span>RawBT App Intent</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-100 text-amber-800 font-extrabold border border-amber-200/80 uppercase tracking-wider">HP / Tablet Android</span>
                                        </div>
                                        <div class="text-[11px] text-[#718379] font-medium leading-snug mt-0.5">Instruksi cetak ke aplikasi RawBT di Android.</div>
                                    </div>
                                </div>
                                @if($printMode === 'RAWBT')
                                    <div class="w-5 h-5 rounded-full bg-[#3F7A5D] text-white flex items-center justify-center shrink-0 ml-1 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 shrink-0 ml-1"></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contextual Help / Setup Box -->
                    @if($printMode === 'WEB_BLUETOOTH')
                        <div x-data="{
                            printerName: window.webBluetoothThermalPrinter?.getSavedDeviceName() || '',
                            isConnecting: false,
                            async pair() {
                                this.isConnecting = true;
                                const name = await window.webBluetoothThermalPrinter?.pairDevice();
                                if (name) {
                                    this.printerName = name;
                                }
                                this.isConnecting = false;
                            },
                            async testPrint() {
                                const success = await window.webBluetoothThermalPrinter?.printReceipt({
                                    storeName: '{{ $storeName }}',
                                    tagline: '{{ $receiptHeaderTagline }}',
                                    address: '{{ $receiptAddress }}',
                                    phone: '{{ $receiptPhone }}',
                                    invoiceNumber: 'INV-TEST-001',
                                    date: '{{ now()->format('d/m/Y H:i') }}',
                                    cashier: '{{ auth()->user()->name }}',
                                    items: [
                                        { name: 'TEST PRINTER ITEM 1', qty: 1, price: 'Rp 10.000', subtotal: 'Rp 10.000' }
                                    ],
                                    total: 'Rp 10.000',
                                    payments: [{ method: 'TUNAI', amount: 'Rp 10.000' }],
                                    footer: '{{ $receiptFooterText }}'
                                });
                                if (success) {
                                    alert('Struk percobaan berhasil dikirim ke printer Bluetooth!');
                                }
                            }
                        }" class="p-3 bg-emerald-50/80 rounded-xl border border-emerald-200/80 space-y-2 text-xs">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-1.5 text-emerald-900 font-extrabold uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span>Status Web Bluetooth</span>
                                </div>
                                <span x-text="printerName ? 'TERHUBUNG: ' + printerName : 'BELUM TERHUBUNG'" :class="printerName ? 'bg-emerald-200/80 text-emerald-900' : 'bg-amber-100 text-amber-900'" class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase font-mono"></span>
                            </div>

                            <p class="text-[11px] text-emerald-800 font-medium leading-relaxed">
                                Pastikan Bluetooth aktif, lalu pasangkan (pair) printer Bluetooth 1 kali di bawah.
                            </p>

                            <div class="flex items-center gap-2 pt-0.5 flex-wrap">
                                <button type="button" @click="pair()" :disabled="isConnecting" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95">
                                    <span x-text="isConnecting ? 'Menghubungkan...' : 'Sambungkan Bluetooth Printer'"></span>
                                </button>
                                <button type="button" @click="testPrint()" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-emerald-800 border border-emerald-300 font-bold text-xs rounded-lg transition cursor-pointer shadow-2xs">
                                    Cetak Struk Percobaan
                                </button>
                            </div>
                        </div>
                    @elseif($printMode === 'BROWSER')
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1">
                            <div class="font-extrabold text-[#2C3E35] flex items-center gap-1.5">
                                <span>💡 Tips Silent Print Google Chrome (Tanpa Pop-up):</span>
                            </div>
                            <p class="text-[11px] text-[#5F7167] leading-relaxed">
                                Buka shortcut Chrome di Windows -> tambahkan <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 font-mono font-bold text-[#2C3E35]">--kiosk-printing</code> di ujung kolom Target.
                            </p>
                        </div>
                    @elseif($printMode === 'RAWBT')
                        <div class="p-3 bg-amber-50/80 rounded-xl border border-amber-200/80 text-xs space-y-1 text-amber-900">
                            <div class="font-extrabold flex items-center gap-1.5">
                                <span>📱 Kebutuhan Aplikasi RawBT:</span>
                            </div>
                            <p class="text-[11px] leading-relaxed">
                                Pastikan aplikasi <strong>RawBT Thermal Printer Driver</strong> sudah terinstall dari Play Store pada Android Anda.
                            </p>
                        </div>
                    @endif

                    <!-- Options Toggle -->
                    <div class="p-3 bg-[#F3F6F4]/70 rounded-xl border border-slate-200/70 space-y-2.5">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" wire:model="autoPrint" class="w-4 h-4 text-[#3F7A5D] rounded border-slate-300 focus:ring-[#3F7A5D]">
                            <div>
                                <div class="text-xs font-extrabold text-[#2C3E35] uppercase tracking-wider">Otomatis Cetak (Auto-Print)</div>
                                <div class="text-[11px] text-[#718379] font-medium">Cetak otomatis begitu checkout transaksi selesai.</div>
                            </div>
                        </label>

                        <div class="border-t border-slate-200/60 pt-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" wire:model.live="showCashierName" class="w-4 h-4 text-[#3F7A5D] rounded border-slate-300 focus:ring-[#3F7A5D]">
                                <div>
                                    <div class="text-xs font-extrabold text-[#2C3E35] uppercase tracking-wider">Tampilkan Nama Kasir di Struk</div>
                                    <div class="text-[11px] text-[#718379] font-medium">Mencantumkan "Kasir: [Nama User]" di metadata struk.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Header Inputs -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-1">Nama Toko *</label>
                            <input type="text" wire:model.live="storeName" class="w-full h-10 px-3 py-1.5 border border-slate-200 rounded-xl font-bold text-sm text-[#2C3E35] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-1">Tagline / Sub-Header Struk</label>
                            <input type="text" wire:model.live="receiptHeaderTagline" placeholder="Retail Management System" class="w-full h-10 px-3 py-1.5 border border-slate-200 rounded-xl text-sm text-[#2C3E35] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-1">Alamat Toko</label>
                                <input type="text" wire:model.live="receiptAddress" placeholder="Jl. Aksesoris No. 88, Jakarta" class="w-full h-10 px-3 py-1.5 border border-slate-200 rounded-xl text-sm text-[#2C3E35] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" />
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-1">No. Telp / WhatsApp</label>
                                <input type="text" wire:model.live="receiptPhone" placeholder="0812-3456-7890" class="w-full h-10 px-3 py-1.5 border border-slate-200 rounded-xl text-sm text-[#2C3E35] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Input -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-[#2C3E35] mb-1">Pesan Footer Struk</label>
                        <textarea wire:model.live="receiptFooterText" rows="2" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm text-[#2C3E35] focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" placeholder="Terima kasih telah berbelanja!"></textarea>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl text-xs uppercase tracking-wider transition shadow-sm cursor-pointer active:scale-95">
                            Simpan Pengaturan Printer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Preview Panel -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm sticky top-6">
                    <div class="border-b border-slate-100 pb-3 mb-3 flex items-center justify-between gap-2">
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Live Preview Struk</h3>
                            <p class="text-xs text-[#718379] font-medium mt-0.5">Gambaran real-time hasil cetak struk thermal</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/80 text-xs font-mono font-extrabold shrink-0 shadow-2xs">{{ $receiptPaperWidth }}</span>
                    </div>

                    <!-- Simulated Paper Container -->
                    <div class="bg-slate-100/90 p-3 sm:p-4 rounded-xl flex justify-center items-start overflow-x-auto min-h-[380px] border border-slate-200/60">
                        @include('receipt._preview', [
                            'storeName' => $storeName,
                            'tagline' => $receiptHeaderTagline,
                            'address' => $receiptAddress,
                            'phone' => $receiptPhone,
                            'footerText' => $receiptFooterText,
                            'paperWidth' => $receiptPaperWidth,
                            'showCashier' => $showCashierName,
                            'invoiceNumber' => 'INV-20260913-001',
                            'changeAmount' => 15000,
                            'previewId' => 'settings-live-receipt-preview',
                            'sale' => (object) [
                                'invoice_number' => 'INV-20260913-001',
                                'transaction_date' => now('Asia/Jakarta'),
                                'total_amount' => 85000,
                                'change_amount' => 15000,
                                'cashier' => auth()->user(),
                                'user' => auth()->user(),
                                'items' => collect([
                                    (object) ['product_name_snapshot' => 'Kabel Data Type-C Fast', 'quantity' => 2, 'selling_price' => 25000, 'subtotal' => 50000],
                                    (object) ['product_name_snapshot' => 'Tempered Glass Bening', 'quantity' => 1, 'selling_price' => 35000, 'subtotal' => 35000],
                                ]),
                                'payments' => collect([
                                    (object) ['paymentMethod' => (object) ['name' => 'Tunai'], 'amount' => 100000],
                                ]),
                            ],
                        ])
                    </div>
                </div>
            </div>
        </div>

    <!-- Tab 3: USERS -->
    @elseif($activeTab === 'USERS')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Table Panel (60% Golden Width) -->
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Daftar Pengguna Sistem</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Petugas kasir dan pengelola toko yang memiliki hak akses login ke sistem.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Username Login</th>
                                <th class="py-3.5 px-4">Nama Lengkap</th>
                                <th class="py-3.5 px-4">Role / Jabatan</th>
                                <th class="py-3.5 px-4 text-center">Status Akun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($users as $user)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $user->username }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $user->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-[#52645B]">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $user->role?->name ?? 'Kasir' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Pengguna (40% Golden Width) -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Pengguna Baru</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan petugas kasir atau admin baru.</p>
                </div>
                <form wire:submit.prevent="addUser" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="userName" placeholder="Budi Santoso" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Username Login *</label>
                        <input type="text" wire:model="userUsername" placeholder="kasir_budi" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-mono font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Password *</label>
                        <input type="password" wire:model="userPassword" placeholder="••••••••" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Role / Wewenang *</label>
                        <select wire:model="userRoleId" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer" required>
                            <option value="">Pilih Role / Wewenang</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Lokasi Kerja *</label>
                        <select wire:model="userLocationId" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer" required>
                            <option value="">Pilih Lokasi Kerja</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Pengguna Sistem
                    </button>
                </form>
            </div>
        </div>

    <!-- Tab 3: ROLES -->
    @elseif($activeTab === 'ROLES')
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Role &amp; Wewenang Akses</h3>
                <p class="text-sm text-[#718379] mt-0.5 font-medium">Tingkatan jabatan dan batas wewenang operasional pengguna dalam sistem POS.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Role / Jabatan</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Pengguna Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($roles as $role)
                            <tr class="hover:bg-[#F3F6F4]/60 transition">
                                <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                    {{ $role->name }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-extrabold text-[#3F7A5D] text-sm">
                                    {{ number_format($role->users_count, 0, ',', '.') }} Pengguna
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Tab 4: Locations -->
    @elseif($activeTab === 'LOCATIONS')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Daftar Lokasi Cabang Toko</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Cabang dan lokasi tempat operasional transaksi POS berlangsung.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Kode Cabang</th>
                                <th class="py-3.5 px-4">Nama Cabang Toko</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($locations as $loc)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $loc->code }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $loc->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $loc->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Lokasi -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Cabang Toko</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan lokasi outlet baru.</p>
                </div>
                <form wire:submit.prevent="addLocation" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Kode Cabang *</label>
                        <input type="text" wire:model="locationCode" placeholder="RAJA-BANGO" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl font-mono uppercase bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Cabang Toko *</label>
                        <input type="text" wire:model="locationName" placeholder="Raja Aksesoris Bango" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Cabang Toko
                    </button>
                </form>
            </div>
        </div>

    <!-- Tab 5: Payment Methods -->
    @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <div class="lg:col-span-3 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Metode Pembayaran Toko</h3>
                    <p class="text-sm text-[#718379] mt-0.5 font-medium">Opsi cara pembayaran yang tersedia bagi pelanggan di kasir.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#F3F6F4] border-b border-slate-200/80 text-[#718379] uppercase text-xs font-extrabold tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Nama Metode</th>
                                <th class="py-3.5 px-4">Kategori Tipe</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($paymentMethods as $pm)
                                <tr class="hover:bg-[#F3F6F4]/60 transition">
                                    <td class="py-3.5 px-4 font-bold text-[#2C3E35] text-sm">
                                        {{ $pm->name }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-[#3F7A5D] text-sm">
                                        {{ $pm->type }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#E3EEE8] text-[#3F7A5D] border border-[#3F7A5D]/20 uppercase">
                                            {{ $pm->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Tambah Metode Pembayaran -->
            <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#2C3E35] uppercase tracking-wider">Tambah Metode Pembayaran</h3>
                    <p class="text-sm text-[#718379] font-medium mt-0.5">Daftarkan opsi pembayaran baru.</p>
                </div>
                <form wire:submit.prevent="addPaymentMethod" class="space-y-3.5 text-sm">
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Nama Metode *</label>
                        <input type="text" wire:model="pmName" placeholder="Transfer Bank BCA / QRIS" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-semibold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>
                    <div>
                        <label class="block text-[#718379] font-extrabold uppercase tracking-wider text-xs mb-1">Kategori Tipe *</label>
                        <select wire:model="pmType" class="w-full h-11 px-3.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#2C3E35] font-bold text-sm focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] cursor-pointer">
                            <option value="CASH">Tunai (Cash)</option>
                            <option value="QRIS">QRIS (Nontunai)</option>
                            <option value="TRANSFER">Transfer Bank</option>
                            <option value="E_WALLET">E-Wallet (Dompet Digital)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full h-11 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-xl transition text-sm cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Metode Pembayaran
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
