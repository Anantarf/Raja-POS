<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->invoice_number }} - {{ $storeName ?? 'Raja Aksesoris' }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ ($paperWidth ?? '58mm') === '80mm' ? '13px' : '11px' }};
            color: #000;
            background: #fff;
            margin: 0 auto;
            padding: 2px 2px;
            width: {{ ($paperWidth ?? '58mm') === '80mm' ? '72mm' : '54mm' }};
            box-sizing: border-box;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .header {
            margin-bottom: 6px;
        }
        .header h2 {
            margin: 0 0 3px 0;
            font-size: {{ ($paperWidth ?? '58mm') === '80mm' ? '18px' : '15px' }};
            text-transform: uppercase;
            line-height: 1.2;
        }
        .header .subtitle {
            font-size: 11px;
            margin-top: 3px;
            line-height: 1.25;
        }
        .header .address {
            font-size: 10px;
            margin-top: 2px;
        }
        .meta {
            font-size: 11px;
            margin-bottom: 6px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
        }
        .table-items td {
            padding: 1px 0;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            margin-top: 3px;
        }
        .totals-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        .totals-table .label {
            width: 48%;
            line-height: 1.15;
            word-break: normal;
            overflow-wrap: anywhere;
        }
        .totals-table .amount {
            width: 52%;
            white-space: nowrap;
        }
        .footer {
            margin-top: 8px;
            font-size: 11px;
            white-space: pre-line;
            line-height: 1.2;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="{{ ($printMode ?? 'BROWSER') === 'RAWBT' ? "window.location.href='intent:' + encodeURIComponent(window.location.href) + '#Intent;scheme=http;package=ru.a256.rawbtprinter;end;'" : 'window.print()' }}">
    <div class="no-print" style="margin-bottom: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
        <button onclick="window.print()" style="padding: 6px 12px; cursor: pointer; background: #0284c7; color: #ffffff; border: none; border-radius: 4px; font-weight: 600; font-family: sans-serif;">Cetak Struk (Browser)</button>
        <button onclick="window.location.href='intent:' + encodeURIComponent(window.location.href) + '#Intent;scheme=http;package=ru.a256.rawbtprinter;end;'" style="padding: 6px 12px; cursor: pointer; background: #16a34a; color: #ffffff; border: none; border-radius: 4px; font-weight: 600; font-family: sans-serif;">Cetak Direct (RawBT POS)</button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer; background: #64748b; color: #ffffff; border: none; border-radius: 4px; font-family: sans-serif;">Tutup</button>
    </div>

    <div class="header text-center">
        <h2>{{ $storeName ?? 'Raja Aksesoris' }}</h2>
        @if(filled($receiptHeaderTagline ?? null))
            <div class="subtitle">{{ $receiptHeaderTagline }}</div>
        @endif
        @if(filled($receiptAddress ?? null))
            <div class="address">{{ $receiptAddress }}</div>
        @endif
        @if(filled($receiptPhone ?? null))
            <div class="address">Telp: {{ $receiptPhone }}</div>
        @endif
    </div>

    <div class="meta">
        <div><strong>No:</strong> {{ $sale->invoice_number }}</div>
        <div><strong>Tgl:</strong> {{ ($sale->transaction_date ?? $sale->created_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
        @if($showCashierName ?? true)
            <div><strong>Kasir:</strong> {{ $sale->cashier?->name ?? $sale->user?->name ?? 'Kasir' }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <table class="table-items">
        @foreach($sale->items as $item)
            <tr>
                <td colspan="2" class="bold">{{ str_replace(['(Rp ', '(Rp. ', 'Rp '], ["(Rp\u00A0", "(Rp.\u00A0", "Rp\u00A0"], $item->product_name_snapshot) }}</td>
            </tr>
            <tr>
                <td class="text-left">
                    {{ $item->quantity }} x Rp{{ number_format($item->original_unit_price ?? $item->selling_price, 0, ',', '.') }}
                    @if(($item->unit_discount ?? 0) > 0)
                        <br><span style="font-size: 10px;">(Disc: -Rp{{ number_format($item->unit_discount, 0, ',', '.') }}/unit)</span>
                    @endif
                </td>
                <td class="text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        @if(($sale->total_discount_amount ?? 0) > 0)
            <tr>
                <td class="text-left label">SUBTOTAL</td>
                <td class="text-right amount">Rp{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left label">TOTAL DISKON</td>
                <td class="text-right amount">-Rp{{ number_format($sale->total_discount_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="bold">
            <td class="text-left">TOTAL</td>
            <td class="text-right">Rp{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
        </tr>
        @foreach($sale->payments as $payment)
            <tr>
                <td class="text-left label">{{ $payment->paymentMethod?->receipt_display_name ?? 'BAYAR' }}</td>
                <td class="text-right amount">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        @if(($sale->change_amount ?? 0) > 0)
            <tr>
                <td class="text-left">KEMBALI</td>
                <td class="text-right">Rp{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        @if(filled($receiptFooterText ?? null))
            {{ $receiptFooterText }}
        @else
            <div class="bold">Terima Kasih Telah Berbelanja!</div>
            <div>Kepuasan Anda Adalah Kebanggaan Kami.</div>
            <div>Sampai Jumpa Kembali di {{ $storeName ?? 'Raja Aksesoris' }}!</div>
        @endif
    </div>
</body>
</html>
