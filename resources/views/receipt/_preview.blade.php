@php
    $receiptSale = $sale ?? null;
    $fallbackInvoice = $invoiceNumber ?? null;
    $fallbackChange = $changeAmount ?? 0;
    $storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Raja Aksesoris');
    $tagline = $tagline ?? \App\Models\Setting::get('receipt_header_tagline', 'Retail Management System');
    $address = $address ?? \App\Models\Setting::get('receipt_address', '');
    $phone = $phone ?? \App\Models\Setting::get('receipt_phone', '');
    $footerText = $footerText ?? \App\Models\Setting::get('receipt_footer_text', 'Terima Kasih Telah Berbelanja!');
    $paperWidth = $paperWidth ?? \App\Models\Setting::get('receipt_paper_width', '58mm');
    $showCashier = isset($showCashier) ? (bool) $showCashier : (\App\Models\Setting::get('show_cashier_name', '1') === '1');
@endphp

<div
    id="{{ $previewId ?? 'printable-receipt-content' }}"
    class="bg-white p-4 sm:p-5 rounded-xl shadow-md border border-slate-200/80 font-mono text-xs text-black leading-relaxed space-y-3 select-text"
    style="width: {{ $paperWidth === '80mm' ? '280px' : '220px' }}; margin: 0 auto;"
>
    <div class="text-center font-bold text-sm uppercase tracking-wide mb-1.5 leading-snug">{{ filled($storeName) ? $storeName : 'RAJA AKSESORIS' }}</div>
    @if(filled($tagline))
        <div class="text-center text-[10px] text-slate-600 mt-1 leading-snug">{{ $tagline }}</div>
    @endif
    @if(filled($address))
        <div class="text-center text-[9px] text-slate-600 mt-0.5">{{ $address }}</div>
    @endif
    @if(filled($phone))
        <div class="text-center text-[9px] text-slate-600">Telp: {{ $phone }}</div>
    @endif

    <div class="border-t border-dashed border-black my-2.5"></div>

    <div class="text-[11px] space-y-1">
        <div><strong>No:</strong> {{ $receiptSale?->invoice_number ?? $fallbackInvoice }}</div>
        <div><strong>Tgl:</strong> {{ $receiptSale?->transaction_date?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? now('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
        @if($showCashier)
            <div><strong>Kasir:</strong> {{ $receiptSale?->cashier?->name ?? auth()->user()->name }}</div>
        @endif
    </div>

    <div class="border-t border-dashed border-black my-2.5"></div>

    <table class="w-full text-xs text-left border-separate border-spacing-y-1">
        @forelse($receiptSale?->items ?? [] as $item)
            <tr>
                <td colspan="2" class="font-bold pt-1 leading-snug break-words">{{ str_replace(['(Rp ', '(Rp. ', 'Rp '], ["(Rp\u00A0", "(Rp.\u00A0", "Rp\u00A0"], $item->product_name_snapshot) }}</td>
            </tr>
            <tr>
                <td class="text-left text-[11px] text-slate-700 pb-1">{{ $item->quantity }} x Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                <td class="text-right font-bold whitespace-nowrap pb-1">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center text-slate-500 py-2">Item tidak tersedia.</td>
            </tr>
        @endforelse
    </table>

    <div class="border-t border-dashed border-black my-2.5"></div>

    <div class="space-y-1.5 text-xs">
        <div class="flex justify-between gap-3 font-bold text-sm">
            <span>TOTAL</span>
            <span>Rp {{ number_format($receiptSale?->total_amount ?? 0, 0, ',', '.') }}</span>
        </div>
        @foreach($receiptSale?->payments ?? [] as $payment)
            <div class="flex justify-between gap-2 text-[11px] flex-wrap items-baseline">
                <span class="break-words max-w-[65%]">BAYAR ({{ $payment->paymentMethod?->name ?? 'Metode' }})</span>
                <span class="font-bold text-right ml-auto whitespace-nowrap">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        @endforeach
        @if(($receiptSale?->change_amount ?? $fallbackChange ?? 0) > 0)
            <div class="flex justify-between gap-3 text-[11px]">
                <span>KEMBALI</span>
                <span>Rp {{ number_format($receiptSale?->change_amount ?? $fallbackChange, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="border-t border-dashed border-black my-2.5"></div>

    <div class="text-center text-[10px] text-slate-600 pt-1 space-y-1 whitespace-pre-line leading-relaxed">
        {{ filled($footerText) ? $footerText : "Terima Kasih Telah Berbelanja!
Sampai Jumpa Kembali." }}
    </div>
</div>
