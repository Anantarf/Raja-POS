<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Monitoring Saldo</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Kelola saldo toko, rekening bank, mutasi transfer, dan penyesuaian saldo.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-2.5 w-full sm:w-auto overflow-x-auto no-scrollbar py-1">
            <button type="button" wire:click="openModal('TRANSFER')" class="h-10 sm:h-11 px-3 sm:px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 font-extrabold rounded-xl text-xs sm:text-sm transition active:scale-95 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span>Transfer Saldo</span>
            </button>
            <button type="button" wire:click="openModal('DEPOSIT')" class="h-10 sm:h-11 px-3 sm:px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl text-xs sm:text-sm transition active:scale-95 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Deposit / Setor</span>
            </button>
            <button type="button" wire:click="openModal('WITHDRAWAL')" class="h-10 sm:h-11 px-3 sm:px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 font-extrabold rounded-xl text-xs sm:text-sm transition active:scale-95 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                <span>Penarikan Saldo</span>
            </button>
            <button type="button" wire:click="openModal('ADJUSTMENT')" class="h-10 sm:h-11 px-3 sm:px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 font-extrabold rounded-xl text-xs sm:text-sm transition active:scale-95 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                <span>Koreksi Saldo</span>
            </button>
        </div>
    </div>

    <!-- Summary KPI Banner -->
    <div class="bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="space-y-1">
            <div class="text-xs text-slate-500 font-extrabold uppercase tracking-wider">Total Saldo Operasional Toko</div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-mono tracking-tight">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 font-medium">Gabungan saldo uang cash, rekening bank, QRIS, dan e-wallet toko.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="bg-emerald-50/80 px-4 py-2.5 rounded-2xl border border-emerald-200/80 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-emerald-800 uppercase font-extrabold tracking-wider">Uang Cash</div>
                    <div class="text-sm font-mono font-extrabold text-slate-900">Rp {{ number_format($totalCash, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-200/80 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 4h4m-4-8h4m-4-4h4"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase font-extrabold tracking-wider">Bank &amp; QRIS</div>
                    <div class="text-sm font-mono font-extrabold text-slate-900">Rp {{ number_format($totalBank, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-200/80 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase font-extrabold tracking-wider">E-Wallet</div>
                    <div class="text-sm font-mono font-extrabold text-slate-900">Rp {{ number_format($totalEwallet, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        @foreach($accounts as $acc)
            @php
                $hasBalance = $acc->balance > 0;
                $isMinus = $acc->balance < 0;
            @endphp
            <div class="bg-white rounded-2xl p-4 shadow-2xs border transition {{ $hasBalance ? 'border-emerald-500/40 bg-gradient-to-b from-emerald-50/30 to-white' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="text-[11px] text-slate-500 font-extrabold uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>{{ $acc->name }}</span>
                    <span class="w-2 h-2 rounded-full {{ $hasBalance ? 'bg-emerald-700' : 'bg-slate-300' }} inline-block"></span>
                </div>
                <div class="text-xl font-extrabold font-mono tracking-tight {{ $isMinus ? 'text-rose-600' : ($hasBalance ? 'text-emerald-700' : 'text-slate-400') }}">
                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                </div>
                @if($isMinus && $acc->account_type === 'CASH')
                    <div class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-xl border border-amber-200 mt-2 font-semibold leading-tight">
                        Saldo Minus.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Mutation Audit Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden space-y-3">
        <!-- Table Header & Quick Filters -->
        <div class="p-4 sm:p-5 border-b border-slate-200/90 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Riwayat Mutasi Saldo</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Audit lengkap aliran uang masuk, keluar, transfer antar rekening, dan sisa saldo setelah mutasi.</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Filter Tabs -->
                <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-[11px] font-extrabold text-slate-500">
                    <button wire:click="setFilterType('ALL')" class="px-3 py-1.5 rounded-lg transition {{ $filterType === 'ALL' ? 'bg-white text-emerald-700 shadow-2xs' : 'hover:text-slate-900' }}">
                        Semua Mutasi
                    </button>
                    <button wire:click="setFilterType('IN')" class="px-3 py-1.5 rounded-lg transition {{ $filterType === 'IN' ? 'bg-emerald-700 text-white shadow-2xs' : 'hover:text-emerald-700' }}">
                        Uang Masuk (+)
                    </button>
                    <button wire:click="setFilterType('OUT')" class="px-3 py-1.5 rounded-lg transition {{ $filterType === 'OUT' ? 'bg-rose-600 text-white shadow-2xs' : 'hover:text-rose-700' }}">
                        Uang Keluar (-)
                    </button>
                    <button wire:click="setFilterType('TRANSFER')" class="px-3 py-1.5 rounded-lg transition {{ $filterType === 'TRANSFER' ? 'bg-indigo-600 text-white shadow-2xs' : 'hover:text-indigo-700' }}">
                        Transfer
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-56">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari No. Mutasi / Ref..."
                        class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 bg-slate-50 text-slate-800 placeholder:text-slate-400"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200/90 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th wire:click="sortBy('transaction_number')" class="py-3.5 px-4 cursor-pointer hover:text-emerald-700 transition select-none">
                            <div class="flex items-center gap-1">
                                <span>No. Mutasi &amp; Waktu</span>
                                @if($sortField === 'transaction_number' || $sortField === 'created_at')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Tipe Transaksi</th>
                        <th class="py-3.5 px-4">Aliran Akun</th>
                        <th wire:click="sortBy('amount')" class="py-3.5 px-4 text-right cursor-pointer hover:text-emerald-700 transition select-none">
                            <div class="flex items-center justify-end gap-1">
                                <span>Nominal</span>
                                @if($sortField === 'amount')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4 text-right">Saldo Akhir</th>
                        <th class="py-3.5 px-4">Keterangan &amp; Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $trx)
                        @php
                            $isIncoming = !is_null($trx->destination_account_id) && is_null($trx->source_account_id);
                            $isOutgoing = !is_null($trx->source_account_id) && is_null($trx->destination_account_id);
                            $isTransfer = !is_null($trx->source_account_id) && !is_null($trx->destination_account_id);

                            $refType = strtoupper($trx->reference_type ?? '');
                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            $label = $refType;

                            if (str_contains($refType, 'SALE') || str_contains($refType, 'POS')) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                                $label = 'PENJUALAN';
                            } elseif (str_contains($refType, 'DEPOSIT')) {
                                $badgeClass = 'bg-emerald-100/90 text-emerald-800 border-emerald-300/60';
                                $label = 'DEPOSIT / SETOR';
                            } elseif (str_contains($refType, 'WITHDRAWAL') || str_contains($refType, 'PENARIKAN')) {
                                $badgeClass = 'bg-rose-50 text-rose-800 border-rose-200/80';
                                $label = 'PENARIKAN SALDO';
                            } elseif (str_contains($refType, 'TRANSFER')) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200/80';
                                $label = 'TRANSFER SALDO';
                            } elseif (str_contains($refType, 'ADJUSTMENT') || str_contains($refType, 'REKON')) {
                                $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200/80';
                                $label = 'KOREKSI SALDO';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- No Mutasi & Waktu -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $trxNum = (string) ($trx->formatted_transaction_number ?? $trx->transaction_number ?? '');
                                    $trxNum = preg_replace_callback('/(TRX)-([a-zA-Z0-9]{8})-[a-zA-Z0-9-]{10,}/i', function ($m) {
                                        return strtoupper($m[1] . '-' . substr($m[2], 0, 8));
                                    }, $trxNum);
                                    if (strlen($trxNum) > 20 && str_contains($trxNum, '-')) {
                                        $p = array_values(array_filter(explode('-', $trxNum)));
                                        $trxNum = strtoupper(($p[0] ?? 'TRX') . '-' . substr($p[1] ?? '', 0, 8));
                                    }
                                @endphp
                                <div class="font-bold text-emerald-700 font-mono text-xs bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md inline-block">{{ $trxNum }}</div>
                                <div class="text-[10px] text-slate-500 mt-1 font-semibold whitespace-nowrap">{{ $trx->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</div>
                            </td>

                            <!-- Tipe Transaksi -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider border uppercase {{ $badgeClass }}">
                                    {{ $label }}
                                </span>
                            </td>

                            <!-- Aliran Akun -->
                            <td class="py-3.5 px-4 text-slate-800 font-semibold whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs">
                                    @if($trx->sourceAccount)
                                        <span class="font-extrabold text-rose-600">{{ $trx->sourceAccount->name }}</span>
                                    @else
                                        <span class="text-slate-500 font-medium">Pelanggan</span>
                                    @endif

                                    <span class="text-slate-400 font-bold">&rarr;</span>

                                    @if($trx->destinationAccount)
                                        <span class="font-extrabold text-emerald-700">{{ $trx->destinationAccount->name }}</span>
                                    @else
                                        <span class="text-slate-500 font-medium">Pengeluaran</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Nominal Mutasi -->
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-sm whitespace-nowrap">
                                @if($isIncoming)
                                    <span class="text-emerald-700">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                @elseif($isOutgoing)
                                    <span class="text-rose-600">- Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-indigo-600">Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            <!-- Posisi Saldo Akhir -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="font-mono text-xs text-slate-900 font-bold">
                                    Rp {{ number_format($trx->balance_after, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                    (Awal: Rp {{ number_format($trx->balance_before, 0, ',', '.') }})
                                </div>
                            </td>

                            <!-- Keterangan & Petugas -->
                            <td class="py-3.5 px-4">
                                <div class="text-slate-800 font-semibold leading-snug">
                                    @php
                                        $desc = e($trx->description);
                                        $desc = preg_replace_callback('/(TRX)-([a-zA-Z0-9]{8})-[a-zA-Z0-9-]{10,}/i', function ($m) {
                                            return strtoupper($m[1] . '-' . substr($m[2], 0, 8));
                                        }, $desc);
                                        $desc = preg_replace('/(#(?:INV|TRX|POS)-[\w-]+)/i', '<span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-700 font-mono font-bold rounded border border-indigo-200/80 text-[11px] inline-block mb-0.5">$1</span>', $desc);
                                    @endphp
                                    {!! $desc !!}
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium mt-0.5">Oleh: <span class="font-bold text-slate-900">{{ $trx->user?->name ?? 'System' }}</span></div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">Belum ada catatan mutasi saldo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 border-t border-slate-100">
            {{ $transactions->links('components.emco-pagination') }}
        </div>
    </div>

    <!-- Action Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $showModal === 'TRANSFER' ? 'Transfer Saldo Antar Akun' : ($showModal === 'DEPOSIT' ? 'Deposit / Setor Saldo' : ($showModal === 'WITHDRAWAL' ? 'Penarikan Saldo' : 'Koreksi / Penyesuaian Saldo')) }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', null)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="processTransaction" class="space-y-3.5 text-xs font-medium">
                    @if(in_array($showModal, ['TRANSFER', 'WITHDRAWAL']))
                        <div>
                            <label class="block text-slate-500 font-bold uppercase tracking-wider text-[11px] mb-1">Akun Asal *</label>
                            <select wire:model="sourceAccountId" class="w-full p-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 cursor-pointer">
                                <option value="">Pilih Akun Asal</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if(in_array($showModal, ['TRANSFER', 'DEPOSIT', 'ADJUSTMENT']))
                        <div>
                            <label class="block text-slate-500 font-bold uppercase tracking-wider text-[11px] mb-1">Akun Tujuan *</label>
                            <select wire:model="destinationAccountId" class="w-full p-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 cursor-pointer">
                                <option value="">Pilih Akun Tujuan</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[11px] mb-1">Nominal (Rp) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input
                                type="text"
                                x-data
                                x-on:input="
                                    let val = $el.value.replace(/\D/g, '');
                                    if (val && parseInt(val) > 1000000000) val = '1000000000';
                                    $el.value = val ? parseInt(val).toLocaleString('id-ID') : '';
                                    $wire.set('amount', val ? parseInt(val) : 0);
                                "
                                value="{{ $amount ? number_format($amount, 0, ',', '.') : '' }}"
                                placeholder="0"
                                class="w-full pl-9 pr-3 py-2.5 border border-slate-200 rounded-xl font-mono font-extrabold text-right text-sm text-emerald-700 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-500 font-bold uppercase tracking-wider text-[11px] mb-1">Keterangan / Alasan *</label>
                        <input type="text" wire:model="description" class="w-full p-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 font-semibold" required />
                    </div>

                    <div class="pt-3 flex gap-2.5">
                        <button type="submit" class="flex-1 h-11 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl transition shadow-2xs text-sm cursor-pointer flex items-center justify-center">
                            Proses Mutasi Saldo
                        </button>
                        <button type="button" wire:click="$set('showModal', null)" class="h-11 px-5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-sm transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
