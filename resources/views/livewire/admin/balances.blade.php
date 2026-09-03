<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#232E28] tracking-tight">Monitoring Saldo Kas & Rekening</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Kelola saldo kas fisik, rekening bank, mutasi transfer, dan rekonsiliasi keuangan toko.</p>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto py-1">
            <button wire:click="openModal('TRANSFER')" class="px-3.5 py-2 bg-[#E3EEE8] hover:bg-[#d5e5dc] text-[#3F7A5D] border border-[#3F7A5D]/20 font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span>Transfer Saldo</span>
            </button>
            <button wire:click="openModal('DEPOSIT')" class="px-3.5 py-2 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Deposit / Setor</span>
            </button>
            <button wire:click="openModal('WITHDRAWAL')" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                <span>Penarikan Kas</span>
            </button>
            <button wire:click="openModal('ADJUSTMENT')" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                <span>Rekon Saldo</span>
            </button>
        </div>
    </div>

    <!-- Account Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($accounts as $acc)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 relative overflow-hidden hover:border-[#3F7A5D]/50 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between text-xs text-[#718379] font-extrabold uppercase tracking-wider mb-2">
                    <span>{{ $acc->name }}</span>
                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase bg-[#F3F6F4] text-[#3F7A5D] border border-slate-200/80">
                        {{ $acc->account_type }}
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-[#232E28] font-mono tracking-tight mt-1 {{ $acc->balance < 0 ? 'text-rose-600' : '' }}">
                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                </div>
                @if($acc->balance < 0 && $acc->account_type === 'CASH')
                    <div class="text-xs text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200 mt-3 font-semibold">
                        Uang Kasir Minus. Operasional dapat diganti dari rekening.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Mutation Audit Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-3">
        <div class="p-4 sm:p-5 border-b border-slate-200/80 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Audit Log Mutasi Saldo</h2>
            <div class="relative w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari No. Mutasi / Keterangan..."
                    class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4]"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-[#F3F6F4] border-b border-[#E3EEE8] text-[#718379] uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No. Mutasi & Waktu</th>
                        <th class="py-3.5 px-4">Tipe Transaksi</th>
                        <th class="py-3.5 px-4">Akun Asal & Tujuan</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4">Keterangan & Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $trx)
                        @php
                            $refType = strtoupper($trx->reference_type ?? '');
                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            $label = $refType;

                            if (str_contains($refType, 'SALE') || str_contains($refType, 'POS')) {
                                $badgeClass = 'bg-[#E3EEE8] text-[#3F7A5D] border-[#3F7A5D]/20';
                                $label = 'PENJUALAN POS';
                            } elseif (str_contains($refType, 'TRANSFER')) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200/80';
                                $label = 'TRANSFER SALDO';
                            } elseif (str_contains($refType, 'DEPOSIT')) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                                $label = 'DEPOSIT / SETOR';
                            } elseif (str_contains($refType, 'WITHDRAWAL') || str_contains($refType, 'PENARIKAN')) {
                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200/80';
                                $label = 'PENARIKAN KAS';
                            } elseif (str_contains($refType, 'ADJUSTMENT') || str_contains($refType, 'REKON')) {
                                $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200/80';
                                $label = 'REKONSILIASI';
                            }
                        @endphp
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-[#3F7A5D] font-mono text-xs bg-[#F3F6F4] border border-slate-200/80 px-2.5 py-0.5 rounded-md inline-block">{{ $trx->transaction_number }}</div>
                                <div class="text-[10px] text-[#718379] mt-1 font-semibold whitespace-nowrap">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold tracking-wider border uppercase {{ $badgeClass }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#232E28] font-semibold whitespace-nowrap">
                                @if($trx->sourceAccount) <span class="font-extrabold text-rose-600">{{ $trx->sourceAccount->name }}</span> @else - @endif
                                &rarr;
                                @if($trx->destinationAccount) <span class="font-extrabold text-emerald-600">{{ $trx->destinationAccount->name }}</span> @else - @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#232E28] text-sm whitespace-nowrap">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-[#232E28] font-semibold leading-snug">{{ $trx->description }}</div>
                                <div class="text-[10px] text-[#718379] font-medium mt-0.5">Oleh: {{ $trx->user?->name ?? 'System' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-medium">Belum ada catatan mutasi saldo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Action Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-[#232E28]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-[#232E28]">
                        {{ $showModal === 'TRANSFER' ? 'Transfer Saldo Antar Akun' : ($showModal === 'DEPOSIT' ? 'Deposit / Setor Saldo' : ($showModal === 'WITHDRAWAL' ? 'Penarikan Saldo Kas' : 'Penyesuaian (Rekon) Saldo')) }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', null)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="processTransaction" class="space-y-3.5 text-xs font-medium">
                    @if(in_array($showModal, ['TRANSFER', 'WITHDRAWAL']))
                        <div>
                            <label class="block text-[#718379] font-bold uppercase tracking-wider text-[11px] mb-1">Akun Asal *</label>
                            <select wire:model="sourceAccountId" class="w-full p-2.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#232E28] font-semibold focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if(in_array($showModal, ['TRANSFER', 'DEPOSIT', 'ADJUSTMENT']))
                        <div>
                            <label class="block text-[#718379] font-bold uppercase tracking-wider text-[11px] mb-1">Akun Tujuan *</label>
                            <select wire:model="destinationAccountId" class="w-full p-2.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#232E28] font-semibold focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[#718379] font-bold uppercase tracking-wider text-[11px] mb-1">Nominal (Rp) *</label>
                        <input type="number" wire:model="amount" min="1" class="w-full p-2.5 border border-slate-200 rounded-xl font-mono font-extrabold text-base text-[#3F7A5D] bg-[#F3F6F4] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D]" required />
                    </div>

                    <div>
                        <label class="block text-[#718379] font-bold uppercase tracking-wider text-[11px] mb-1">Keterangan / Alasan *</label>
                        <input type="text" wire:model="description" class="w-full p-2.5 border border-slate-200 rounded-xl bg-[#F3F6F4] text-[#232E28] focus:bg-white focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] font-semibold" required />
                    </div>

                    <div class="pt-3 flex gap-2">
                        <button type="submit" class="flex-1 py-3 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-extrabold rounded-2xl transition shadow-sm text-xs uppercase tracking-wider cursor-pointer">
                            Proses Mutasi Saldo
                        </button>
                        <button type="button" wire:click="$set('showModal', null)" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-[#232E28] font-bold rounded-2xl text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
