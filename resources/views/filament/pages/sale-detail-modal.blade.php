<div class="space-y-4 text-xs">
    <!-- Meta Info -->
    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
        <div>
            <div class="text-slate-500">Nomor Invoice</div>
            <div class="font-bold text-slate-800 font-mono text-sm">{{ $sale->invoice_number }}</div>
        </div>
        <div>
            <div class="text-slate-500">Tanggal & Waktu</div>
            <div class="font-semibold text-slate-800">{{ $sale->transaction_date->format('d/m/Y H:i:s') }}</div>
        </div>
        <div>
            <div class="text-slate-500">Kasir</div>
            <div class="font-semibold text-slate-800">{{ $sale->cashier->name ?? '-' }}</div>
        </div>
        <div>
            <div class="text-slate-500">Status Transaksi</div>
            <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $sale->status === 'COMPLETED' ? 'bg-emerald-100 text-emerald-700' : ($sale->status === 'TRASHED' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700') }}">
                {{ $sale->status }}
            </span>
        </div>
    </div>

    @if($sale->status === 'TRASHED')
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-2.5 rounded-lg space-y-1">
            <div class="font-bold">Alasan Pembatalan:</div>
            <div>{{ $sale->trash_reason }}</div>
            <div class="text-[10px] text-amber-700 pt-1">
                Dibatalkan oleh: {{ $sale->trashed_by ? \App\Models\User::find($sale->trashed_by)?->name : '-' }} Pada {{ $sale->trashed_at?->format('d/m/Y H:i') }}
            </div>
        </div>
    @endif

    <!-- Items Table -->
    <div>
        <div class="font-bold text-slate-700 mb-1">Daftar Item Snapshot</div>
        <table class="w-full text-left border-collapse border border-slate-200">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-200">
                    <th class="p-2">Kode</th>
                    <th class="p-2">Nama Produk</th>
                    <th class="p-2 text-center">Tipe</th>
                    <th class="p-2 text-center">Qty</th>
                    <th class="p-2 text-right">Harga Jual</th>
                    <th class="p-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($sale->items as $item)
                    <tr>
                        <td class="p-2 font-mono text-[11px]">{{ $item->product_code_snapshot }}</td>
                        <td class="p-2 font-medium">{{ $item->product_name_snapshot }}</td>
                        <td class="p-2 text-center">
                            <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold {{ $item->product_type_snapshot === 'PHYSICAL' ? 'bg-blue-100 text-blue-700' : ($item->product_type_snapshot === 'DIGITAL' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $item->product_type_snapshot }}
                            </span>
                        </td>
                        <td class="p-2 text-center font-bold">{{ $item->quantity }}</td>
                        <td class="p-2 text-right">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        <td class="p-2 text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Payments Table -->
    <div>
        <div class="font-bold text-slate-700 mb-1">Rincian Pembayaran & Saldo</div>
        <table class="w-full text-left border-collapse border border-slate-200">
            <thead>
                <tr class="bg-slate-100 text-slate-700 border-b border-slate-200">
                    <th class="p-2">Metode Pembayaran</th>
                    <th class="p-2">Akun Saldo Tujuan</th>
                    <th class="p-2 text-right">Jumlah Dibayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($sale->payments as $payment)
                    <tr>
                        <td class="p-2 font-semibold">{{ $payment->paymentMethod->name ?? 'Kas' }}</td>
                        <td class="p-2">{{ $payment->balanceAccount->name ?? 'CASH' }}</td>
                        <td class="p-2 text-right font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-1 text-right">
        <div class="flex justify-between">
            <span class="text-slate-600">Total Belanja:</span>
            <span class="font-bold text-slate-900">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600">Total Dibayar:</span>
            <span>Rp {{ number_format($sale->amount_paid, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-emerald-600 font-bold">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
