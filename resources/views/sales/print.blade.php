<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_no }}</title>
    <style>
        @page { size: 58mm auto; margin: 4px; }
        body { font-family: 'Courier New', monospace; margin: 0; padding: 0; }
        .receipt { width: 58mm; padding: 6px; margin: 0 auto; }
        .title { text-align: center; font-weight: bold; margin-bottom: 4px; }
        .muted { color: #555; font-size: 12px; text-align: center; margin-bottom: 8px; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        td { vertical-align: top; }
        .right { text-align: right; }
        .btn-print { display: block; margin: 10px auto; padding: 6px 10px; border: 1px solid #000; background: #fff; cursor: pointer; }
        @media print {
            .btn-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="title">{{ config('app.name') }}</div>
        <div class="muted">
            Invoice: {{ $sale->invoice_no }}<br>
            Kasir: {{ $sale->user?->name ?? '-' }}<br>
            {{ $sale->sale_date?->format('d M Y H:i') }}
        </div>

        <div class="line"></div>
        <table>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td colspan="2">{{ $item->product->nama_dagang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>{{ $item->qty }} x {{ number_format($item->price - $item->discount, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($item->line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="line"></div>
        <table>
            <tr>
                <td>Total</td>
                <td class="right">{{ number_format($sale->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar ({{ $sale->payment_method }})</td>
                <td class="right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="line"></div>
        <div class="muted">Terima kasih</div>
    </div>

    <button class="btn-print" onclick="window.print()">Print</button>
</body>
</html>
