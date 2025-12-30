<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .text-end { text-align: right; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>
<h3>Laporan Apotek</h3>
<p class="mb-1">Periode: {{ $filters['start']->format('d M Y') }} s/d {{ $filters['end']->format('d M Y') }}</p>
<p class="mb-2">Dibuat: {{ $meta['generated_at']->format('d M Y H:i') }}</p>

<table>
    <tr>
        <th>Total Penjualan</th>
        <th>Transaksi</th>
        <th>Near-Expired</th>
        <th>Kadaluarsa</th>
    </tr>
    <tr>
        <td class="text-end">Rp {{ number_format($meta['total_sales'], 2, ',', '.') }}</td>
        <td class="text-end">{{ $meta['total_transactions'] }}</td>
        <td class="text-end">Rp {{ number_format($nearExpiredLoss, 2, ',', '.') }}</td>
        <td class="text-end">Rp {{ number_format($expiredLoss, 2, ',', '.') }}</td>
    </tr>
</table>

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
</body>
</html>
