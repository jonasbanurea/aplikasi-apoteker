<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: 58mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', 'Courier', monospace; 
            font-size: 10px;
            line-height: 1.3;
            margin: 0; 
            padding: 0;
            width: 58mm;
        }
        .receipt { 
            width: 58mm; 
            padding: 2mm;
            margin: 0;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 12px;
            margin-bottom: 2px;
        }
        .info { 
            font-size: 9px; 
            text-align: center; 
            margin-bottom: 4px;
            line-height: 1.2;
        }
        .line { 
            border-top: 1px dashed #000; 
            margin: 3px 0;
            width: 100%;
        }
        .item { 
            margin-bottom: 2px;
            font-size: 9px;
        }
        .item-name {
            margin-bottom: 1px;
        }
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        .summary {
            margin-top: 2px;
            font-size: 9px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }
        .total-row {
            font-weight: bold;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 4px;
        }
        .btn-print { 
            display: block; 
            margin: 10px auto; 
            padding: 8px 16px; 
            border: 1px solid #000; 
            background: #fff; 
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            .btn-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="title">{{ config('app.name') }}</div>
        <div class="info">
            Invoice: {{ $sale->invoice_no }}<br>
            Kasir: {{ $sale->user?->name ?? '-' }}<br>
            {{ $sale->sale_date?->format('d/m/Y H:i') }}
        </div>

        <div class="line"></div>
        
        @foreach($sale->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->product->nama_dagang ?? '-' }}</div>
            <div class="item-detail">
                <span>{{ $item->qty }} x Rp {{ number_format($item->price - $item->discount, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
        
        <div class="line"></div>
        
        <div class="summary">
            <div class="summary-row total-row">
                <span>TOTAL</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Bayar ({{ $sale->payment_method }})</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Kembali</span>
                <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="line"></div>
        <div class="footer">Terima Kasih</div>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak Struk</button>
</body>
</html>
