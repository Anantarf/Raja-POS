<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $sale->invoice_number }} - Raja Aksesoris</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: {{ $paperWidth ?? '58mm' }};
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header {
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .meta {
            font-size: 11px;
            margin-bottom: 8px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
        }
        .table-items td {
            padding: 2px 0;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 2px 0;
        }
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 6px 12px; cursor: pointer;">Cetak Struk</button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header text-center">
        <h2>Raja Aksesoris</h2>
        <div>Retail Management System</div>
    </div>

    <div class="meta">
        <div><strong>No:</strong> {{ $sale->invoice_number }}</div>
        <div><strong>Tgl:</strong> {{ $sale->transaction_date->format('d/m/Y H:i') }}</div>
        <div><strong>Kasir:</strong> {{ $sale->cashier->name ?? 'Kasir' }}</div>
    </div>

    <div class="divider"></div>

    <table class="table-items">
        @foreach($sale->items as $item)
            <tr>
                <td colspan="2" class="bold">{{ $item->product_name_snapshot }}</td>
            </tr>
            <tr>
                <td class="text-left">{{ $item->quantity }} x Rp{{ number_format($item->selling_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr class="bold">
            <td class="text-left">TOTAL</td>
            <td class="text-right">Rp{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
        </tr>
        @foreach($sale->payments as $payment)
            <tr>
                <td class="text-left">BAYAR ({{ $payment->paymentMethod->name ?? 'Metode' }})</td>
                <td class="text-right">Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="text-left">KEMBALI</td>
            <td class="text-right">Rp{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        <div class="bold">Terima Kasih Telah Berbelanja!</div>
        <div>Kepuasan Anda Adalah Kebanggaan Kami.</div>
        <div>Sampai Jumpa Kembali di Raja Aksesoris!</div>
    </div>
</body>
</html>
