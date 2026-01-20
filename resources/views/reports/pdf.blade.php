<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Toko Obat Ro Tua</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            margin: 20mm 10mm 15mm 10mm;
        }
        html, body {
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            color: #333;
            line-height: 1.5;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #2c3e50;
            padding: 15px 0 8px 0;
            margin-bottom: 6px;
            position: relative;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .logo-section {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }
        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .company-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .company-address {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        .title-table {
            width: 100%;
            margin: 4px 0 4px 0;
        }
        .report-title {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 8px 15px;
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            border-radius: 3px;
            text-align: center !important;
            letter-spacing: 1px;
            display: inline-block;
            min-width: 300px;
        }
        .report-meta {
            background: #f8f9fa;
            padding: 6px 15px;
            margin-bottom: 6px;
            border-left: 4px solid #3498db;
            font-size: 10px;
        }
        .report-meta strong {
            color: #2c3e50;
        }
        
        /* Table Styles */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th { 
            background: linear-gradient(180deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #2c3e50;
        }
        td { 
            border: 1px solid #ddd; 
            padding: 7px 8px;
            background: white;
        }
        tr:nth-child(even) td {
            background: #f8f9fa;
        }
        tr:hover td {
            background: #e8f4f8;
        }
        
        /* Section Headers */
        h4 {
            color: #2c3e50;
            font-size: 13px;
            margin: 20px 0 10px 0;
            padding-left: 10px;
            border-left: 4px solid #3498db;
            font-weight: bold;
        }
        
        /* Summary Box */
        .summary-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #3498db;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .summary-box table td {
            border: none;
            background: transparent;
            font-weight: bold;
            font-size: 11px;
        }
        .summary-box table th {
            background: #3498db;
            color: white;
            border: 1px solid #2980b9;
        }
        
        /* Utility Classes */
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding: 15px 0 20px 0;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header dengan Logo -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                @if(file_exists('C:\Aplikasi-apoteker\logo.png'))
                    <img src="C:\Aplikasi-apoteker\logo.png" alt="Logo" class="logo">
                @endif
            </div>
            <div class="company-info">
                <div class="company-name">TOKO OBAT RO TUA</div>
                <div class="company-address">Jl. Saribu Dolok, Pematang Pane, Kec. Panombean Panei</div>
                <div class="company-address">Kabupaten Simalungun, Sumatera Utara</div>
            </div>
        </div>
    </div>

    <table class="title-table" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <div class="report-title">LAPORAN APOTEK</div>
            </td>
        </tr>
    </table>
    
    <div class="report-meta">
        <strong>Periode:</strong> {{ $filters['start']->format('d M Y') }} s/d {{ $filters['end']->format('d M Y') }}<br>
        <strong>Tanggal Cetak:</strong> {{ $meta['generated_at']->format('d M Y H:i') }} WIB
    </div>

<div class="summary-box">

<div class="summary-box">
<table>
    <tr>
        <th>Total Penjualan</th>
        <th>Total Transaksi</th>
        <th>Near-Expired</th>
        <th>Kadaluarsa</th>
    </tr>
    <tr>
        <td class="text-end">Rp {{ number_format($meta['total_sales'], 2, ',', '.') }}</td>
        <td class="text-center">{{ $meta['total_transactions'] }}</td>
        <td class="text-end">Rp {{ number_format($nearExpiredLoss, 2, ',', '.') }}</td>
        <td class="text-end">Rp {{ number_format($expiredLoss, 2, ',', '.') }}</td>
    </tr>
</table>
</div>

<h4>Ringkasan Penjualan ({{ $filters['group_by'] }})</h4>
<table>
    <thead>
        <tr><th>Periode</th><th class="text-end">Total</th><th class="text-end">Transaksi</th></tr>
    </thead>
    <tbody>
        @foreach($salesByPeriod as $row)
            <tr>
                <td>{{ $row->label }}</td>
                <td class="text-end">Rp {{ number_format($row->total_amount, 2, ',', '.') }}</td>
                <td class="text-end">{{ $row->total_tx }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Ringkasan 7 Hari (CASH vs NON_CASH)</h4>
<table>
    <thead>
        <tr><th>Metode</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @foreach($weeklyPayment as $row)
            <tr>
                <td>{{ $row->payment_method }}</td>
                <td class="text-end">Rp {{ number_format($row->total_amount, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Penjualan per Kasir</h4>
<table>
    <thead>
        <tr><th>Kasir</th><th class="text-end">Transaksi</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @foreach($salesPerCashier as $row)
            <tr>
                <td>{{ $row->user->name ?? '-' }}</td>
                <td class="text-end">{{ $row->total_tx }}</td>
                <td class="text-end">Rp {{ number_format($row->total_amount, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Penjualan per Item (Top 25)</h4>
<table>
    <thead>
        <tr><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Revenue</th></tr>
    </thead>
    <tbody>
        @foreach($salesPerItem as $row)
            <tr>
                <td>{{ $row->product->sku ?? '' }} - {{ $row->product->nama_dagang ?? '-' }}</td>
                <td class="text-end">{{ $row->total_qty }}</td>
                <td class="text-end">Rp {{ number_format($row->revenue, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Penjualan per Golongan</h4>
<table>
    <thead>
        <tr><th>Golongan</th><th class="text-end">Qty</th><th class="text-end">Revenue</th></tr>
    </thead>
    <tbody>
        @foreach($salesPerGolongan as $row)
            <tr>
                <td>{{ $row->golongan ?? '-' }}</td>
                <td class="text-end">{{ $row->total_qty }}</td>
                <td class="text-end">Rp {{ number_format($row->revenue, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Laba Kotor per Item</h4>
<table>
    <thead>
        <tr><th>Produk</th><th class="text-end">Qty</th><th class="text-end">Revenue</th><th class="text-end">Cost</th><th class="text-end">Margin</th></tr>
    </thead>
    <tbody>
        @foreach($grossProfit as $row)
            <tr>
                <td>{{ $row->sku ?? '' }} - {{ $row->nama_dagang ?? '-' }}</td>
                <td class="text-end">{{ $row->qty }}</td>
                <td class="text-end">Rp {{ number_format($row->revenue, 2, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($row->cost, 2, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($row->margin, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Pembelian per Supplier</h4>
<table>
    <thead>
        <tr><th>Supplier</th><th class="text-end">PO</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @foreach($purchasesBySupplier as $row)
            <tr>
                <td>{{ $row->supplier->name ?? '-' }}</td>
                <td class="text-end">{{ $row->total_po }}</td>
                <td class="text-end">Rp {{ number_format($row->total_amount, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Hutang Jatuh Tempo (14 hari)</h4>
<table>
    <thead>
        <tr><th>Supplier</th><th>Invoice</th><th>Jatuh Tempo</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @forelse($dueSoon as $row)
            <tr>
                <td>{{ $row->supplier->name ?? '-' }}</td>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ optional($row->due_date)->format('d M Y') }}</td>
                <td class="text-end">Rp {{ number_format($row->total, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Tidak ada jatuh tempo</td></tr>
        @endforelse
    </tbody>
</table>

<h4>Near-Expired ({{ $filters['near_days'] }} hari)</h4>
<table>
    <thead>
        <tr><th>Produk</th><th>Batch</th><th>ED</th><th class="text-end">Qty</th><th class="text-end">Nilai</th></tr>
    </thead>
    <tbody>
        @forelse($nearExpired as $row)
            <tr>
                <td>{{ $row->product->sku ?? '' }} - {{ $row->product->nama_dagang ?? '-' }}</td>
                <td>{{ $row->batch_no }}</td>
                <td>{{ optional($row->expired_date)->format('d M Y') }}</td>
                <td class="text-end">{{ $row->qty_on_hand }}</td>
                <td class="text-end">Rp {{ number_format($row->qty_on_hand * $row->cost_price, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Tidak ada near-expired</td></tr>
        @endforelse
    </tbody>
</table>

<h4>Kadaluarsa</h4>
<table>
    <thead>
        <tr><th>Produk</th><th>Batch</th><th>ED</th><th class="text-end">Qty</th><th class="text-end">Nilai</th></tr>
    </thead>
    <tbody>
        @forelse($expired as $row)
            <tr>
                <td>{{ $row->product->sku ?? '' }} - {{ $row->product->nama_dagang ?? '-' }}</td>
                <td>{{ $row->batch_no }}</td>
                <td>{{ optional($row->expired_date)->format('d M Y') }}</td>
                <td class="text-end">{{ $row->qty_on_hand }}</td>
                <td class="text-end">Rp {{ number_format($row->qty_on_hand * $row->cost_price, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Tidak ada kadaluarsa</td></tr>
        @endforelse
    </tbody>
</table>

<h4>Reorder List</h4>
<table>
    <thead>
        <tr><th>Produk</th><th class="text-end">Minimal</th><th class="text-end">Qty On Hand</th><th class="text-end">Kebutuhan</th><th class="text-end">Avg Daily Sold</th></tr>
    </thead>
    <tbody>
        @forelse($reorder as $row)
            <tr>
                <td>{{ $row->sku }} - {{ $row->nama_dagang }}</td>
                <td class="text-end">{{ $row->minimal_stok }}</td>
                <td class="text-end">{{ $row->qty_on_hand }}</td>
                <td class="text-end">{{ $row->reorder_need }}</td>
                <td class="text-end">{{ $row->avg_daily_sold }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Tidak ada yang perlu reorder</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <strong>TOKO OBAT RO TUA</strong><br>
    Jl. Saribu Dolok, Pematang Pane, Kec. Panombean Panei, Kabupaten Simalungun, Sumatera Utara<br>
    Laporan ini dicetak secara otomatis pada {{ $meta['generated_at']->format('d M Y H:i') }} WIB
</div>
</body>
</html>
