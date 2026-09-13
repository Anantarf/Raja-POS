@php
    $receiptSale = $sale ?? null;
    $fallbackInvoice = $invoiceNumber ?? null;
    $fallbackChange = $changeAmount ?? 0;
    $storeName = \App\Models\Setting::get('store_name', 'Raja Aksesoris');
    $tagline = \App\Models\Setting::get('receipt_header_tagline', 'Retail Management System');
    $address = \App\Models\Setting::get('receipt_address', '');
    $phone = \App\Models\Setting::get('receipt_phone', '');
    $footerText = \App\Models\Setting::get('receipt_footer_text', 'Terima Kasih Telah Berbelanja!');
    $paperWidth = \App\Models\Setting::get('receipt_paper_width', '58mm');
    $showCashier = \App\Models\Setting::get('show_cashier_name', '1') === '1';
@endphp

<div
    id="{{ $previewId ?? 'printable-receipt-content' }}"
    class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 font-mono text-xs text-black leading-relaxed space-y-2 select-text"
    style="width: {{ $paperWidth === '80mm' ? '280px' : '200px' }}; margin: 0 auto;"
>
    <div class="text-center font-bold text-sm uppercase tracking-wide">{{ filled($storeName) ? $storeName : 'RAJA AKSESORIS' }}</div>
    @if(filled($tagline))
        <div class="text-center text-[10px] text-slate-600">{{ $tagline }}</div>
    @endif
    @if(filled($address))
        <div class="text-center text-[9px] text-slate-600 mt-0.5">{{ $address }}</div>
    @endif
    @if(filled($phone))
        <div class="text-center text-[9px] text-slate-600">Telp: {{ $phone }}</div>
    @endif

    <div class="border-t border-dashed border-black my-2"></div>

    <div class="text-[11px] space-y-0.5">
        <div><strong>No:</strong> {{ $receiptSale?->invoice_number ?? $fallbackInvoice }}</div>
        <div><strong>Tgl:</strong> {{ $receiptSale?->transaction_date?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? now('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
        @if($showCashier)
            <div><strong>Kasir:</strong> {{ $receiptSale?->cashier?->name ?? auth()->user()->name }}</div>
        @endif
    </div>

    <div class="border-t border-dashed border-black my-2"></div>

    <table class="w-full text-xs text-left">
        @forelse($receiptSale?->items ?? [] as $item)
            <tr>
                <td colspan="2" class="font-bold pt-1">{{ $item->product_name_snapshot }}</td>
            </tr>
            <tr>
                <td class="text-left text-[11px] text-slate-700">{{ $item->quantity }} x Rp{{ number_format($item->selling_price, 0, ',', '.') }}</td>
                <td class="text-right font-bold whitespace-nowrap">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center text-slate-500 py-2">Item tidak tersedia.</td>
            </tr>
        @endforelse
    </table>

    <div class="border-t border-dashed border-black my-2"></div>

    <div class="space-y-1 text-xs">
        <div class="flex justify-between font-bold text-sm">
            <span>TOTAL</span>
            <span>Rp{{ number_format($receiptSale?->total_amount ?? 0, 0, ',', '.') }}</span>
        </div>
        @foreach($receiptSale?->payments ?? [] as $payment)
            <div class="flex justify-between text-[11px]">
                <span>BAYAR ({{ $payment->paymentMethod?->name ?? 'Metode' }})</span>
                <span>Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        @endforeach
        <div class="flex justify-between text-[11px]">
            <span>KEMBALI</span>
            <span>Rp{{ number_format($receiptSale?->change_amount ?? $fallbackChange, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="border-t border-dashed border-black my-2"></div>

    <div class="text-center text-[10px] text-slate-600 pt-1 space-y-0.5 whitespace-pre-line">
        {{ filled($footerText) ? $footerText : "Terima Kasih Telah Berbelanja!
Sampai Jumpa Kembali." }}
    </div>
</div>
