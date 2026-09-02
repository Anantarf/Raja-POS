<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-[#232E28] tracking-tight">Keuangan & Akun Saldo Kas/Bank</h1>
            <p class="text-xs text-[#718379] font-medium mt-0.5">Kelola saldo kas fisik, rekening bank, mutasi transfer, dan rekonsiliasi keuangan.</p>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto py-1">
            <button wire:click="openModal('TRANSFER')" class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200/80 font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0">
                <span>Transfer Saldo</span>
            </button>
            <button wire:click="openModal('DEPOSIT')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0">
                <span>Deposit / Setor</span>
            </button>
            <button wire:click="openModal('WITHDRAWAL')" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0">
                <span>Penarikan Kas</span>
            </button>
            <button wire:click="openModal('ADJUSTMENT')" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold rounded-2xl text-xs transition active:scale-95 flex items-center gap-1.5 shrink-0">
                <span>Rekon Saldo</span>
            </button>
        </div>
    </div>

    <!-- Account Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($accounts as $acc)
            <div class="bg-white rounded-3xl p-5 shadow-emco border border-[#E3EEE8] relative overflow-hidden hover:border-[#3F7A5D]/40 transition-all duration-200">
                <div class="flex items-center justify-between text-xs text-[#718379] font-extrabold uppercase tracking-wider mb-2">
                    <span>{{ $acc->name }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-50 text-indigo-600 border border-indigo-200/60">
                        {{ $acc->account_type }}
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-[#3F7A5D] font-mono tracking-tight mt-1 {{ $acc->balance < 0 ? 'text-rose-600' : '' }}">
                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                </div>
                @if($acc->balance < 0 && $acc->account_type === 'CASH')
                    <div class="text-[11px] text-amber-800 bg-amber-50 p-2.5 rounded-2xl border border-amber-200 mt-3 font-bold">
                        Uang Kasir Minus. Operasional dapat diganti dari rekening.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Mutation Audit Table -->
    <div class="bg-white rounded-3xl border border-[#E3EEE8] shadow-emco overflow-hidden space-y-3">
        <div class="p-5 border-b border-[#E3EEE8] flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-[#232E28] uppercase tracking-wider">Audit Log Mutasi Saldo</h2>
            <div class="relative w-64">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari No. Mutasi / Keterangan..."
                    class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#3F7A5D]/20 focus:border-[#3F7A5D] bg-[#F3F6F4]"
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
                        <tr class="hover:bg-[#F3F6F4]/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-indigo-600 font-mono text-xs bg-indigo-50 border border-indigo-200/70 px-2 py-0.5 rounded-md inline-block">{{ $trx->transaction_number }}</div>
                                <div class="text-[10px] text-[#718379] mt-1 font-semibold">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase {{ in_array($trx->reference_type, ['SALE_PAYMENT', 'DEPOSIT']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-600 border border-indigo-200/70' }}">
                                    {{ $trx->reference_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#232E28] font-semibold">
                                @if($trx->sourceAccount) <span class="font-extrabold text-rose-600">{{ $trx->sourceAccount->name }}</span> @else - @endif
                                &rarr;
                                @if($trx->destinationAccount) <span class="font-extrabold text-emerald-600">{{ $trx->destinationAccount->name }}</span> @else - @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-[#3F7A5D] text-sm">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-[#232E28] font-semibold leading-snug">{{ $trx->description }}</div>
                                <div class="text-[10px] text-[#718379] font-medium mt-0.5">Oleh: {{ $trx->user?->name ?? 'System' }}</div>
                            </td>
                        </tr>            </tr>
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
        <div class="fixed inset-0 bg-[#232333]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-extrabold text-[#566A7F]">
                        {{ $showModal === 'TRANSFER' ? 'Transfer Saldo Antar Akun' : ($showModal === 'DEPOSIT' ? 'Deposit / Setor Saldo' : ($showModal === 'WITHDRAWAL' ? 'Penarikan Saldo Kas' : 'Penyesuaian (Rekon) Saldo')) }}
                    </h3>
                    <button wire:click="$set('showModal', null)" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="processTransaction" class="space-y-3.5 text-xs font-medium">
                    @if(in_array($showModal, ['TRANSFER', 'WITHDRAWAL']))
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Akun Asal *</label>
                            <select wire:model="sourceAccountId" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF]">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if(in_array($showModal, ['TRANSFER', 'DEPOSIT', 'ADJUSTMENT']))
                        <div>
                            <label class="block text-[#566A7F] font-semibold mb-1">Akun Tujuan *</label>
                            <select wire:model="destinationAccountId" class="w-full p-2.5 border border-slate-300 rounded-xl bg-white font-semibold focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF]">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Nominal (Rp) *</label>
                        <input type="number" wire:model="amount" min="1" class="w-full p-2.5 border border-slate-300 rounded-xl font-mono font-extrabold text-base text-[#696CFF] focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF]" required />
                    </div>

                    <div>
                        <label class="block text-[#566A7F] font-semibold mb-1">Keterangan / Alasan *</label>
                        <input type="text" wire:model="description" class="w-full p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#696CFF]/20 focus:border-[#696CFF] font-semibold" required />
                    </div>

                    <div class="pt-3 flex gap-2">
                        <button type="submit" class="flex-1 py-3.5 bg-[#696CFF] hover:bg-[#5F61E6] text-white font-bold rounded-xl transition shadow-sneat-primary text-xs uppercase tracking-wider">
                            Proses Mutasi Saldo
                        </button>
                        <button type="button" wire:click="$set('showModal', null)" class="py-3.5 px-4 bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
