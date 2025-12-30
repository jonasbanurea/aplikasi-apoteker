@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Laporan</h4>
        <small class="text-muted">Penjualan, pembelian, stok, dan kadaluarsa</small>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('reports.pdf', request()->all()) }}">
            <i class="bi bi-file-earmark-pdf"></i> Generate PDF
        </a>
        <form method="POST" action="{{ route('reports.email') }}">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date', $filters['start']->format('Y-m-d')) }}">
            <input type="hidden" name="end_date" value="{{ request('end_date', $filters['end']->format('Y-m-d')) }}">
            <input type="hidden" name="group_by" value="{{ request('group_by', $filters['group_by']) }}">
            <input type="hidden" name="near_days" value="{{ request('near_days', $filters['near_days']) }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope"></i> Email PDF
            </button>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('reports.index') }}">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $filters['start']->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $filters['end']->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Group By</label>
                <select name="group_by" class="form-select">
                    <option value="daily" {{ $filters['group_by'] === 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ $filters['group_by'] === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ $filters['group_by'] === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Near-Expired (hari)</label>
                <select name="near_days" class="form-select">
                    @foreach($filters['near_choices'] as $d)
                        <option value="{{ $d }}" {{ $filters['near_days'] == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 justify-content-end">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Terapkan</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Penjualan</div>
                <div class="h5">Rp {{ number_format($meta['total_sales'], 2, ',', '.') }}</div>
                <div class="small text-muted">Transaksi: {{ $meta['total_transactions'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Near-Expired ({{ $filters['near_days'] }} hari)</div>
                <div class="h5">Rp {{ number_format($nearExpiredLoss, 2, ',', '.') }}</div>
                <div class="small text-muted">Kerugian potensial</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Kadaluarsa</div>
                <div class="h5">Rp {{ number_format($expiredLoss, 2, ',', '.') }}</div>
                <div class="small text-muted">Nilai hilang</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><strong>Ringkasan Penjualan ({{ $filters['group_by'] }})</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Periode</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Transaksi</th>
                    </tr>
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
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Ringkasan 7 Hari (CASH vs NON_CASH)</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Metode</th>
                                <th class="text-end">Total</th>
                            </tr>
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Penjualan per Kasir</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kasir</th>
                                <th class="text-end">Transaksi</th>
                                <th class="text-end">Total</th>
                            </tr>
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Penjualan per Item (Top 25)</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Revenue</th>
                            </tr>
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Penjualan per Golongan</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Revenue</th>
                            </tr>
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><strong>Laba Kotor per Item</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Cost</th>
                        <th class="text-end">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grossProfit as $row)
                        <tr>
                            <td>{{ $row->sku ?? '' }} - {{ $row->nama_dagang ?? '-' }}</td>
                            <td class="text-end">{{ $row->qty }}</td>
                            <td class="text-end">Rp {{ number_format($row->revenue, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($row->cost, 2, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($row->margin, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Pembelian per Supplier</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Supplier</th>
                                <th class="text-end">PO</th>
                                <th class="text-end">Total</th>
                            </tr>
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Hutang Jatuh Tempo (14 hari)</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Supplier</th>
                                <th>Invoice</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total</th>
                            </tr>
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
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada jatuh tempo</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><strong>Stok per Batch (snapshot)</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>ED</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockSnapshot as $row)
                        <tr>
                            <td>{{ $row->product->sku ?? '' }} - {{ $row->product->nama_dagang ?? '-' }}</td>
                            <td>{{ $row->batch_no }}</td>
                            <td>{{ optional($row->expired_date)->format('d M Y') ?? '-' }}</td>
                            <td class="text-end">{{ $row->qty_on_hand }}</td>
                            <td class="text-end">Rp {{ number_format($row->cost_price, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Near-Expired ({{ $filters['near_days'] }} hari)</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Batch</th>
                                <th>ED</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Nilai</th>
                            </tr>
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
                                <tr><td colspan="5" class="text-center text-muted">Tidak ada near-expired</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white"><strong>Sudah Kadaluarsa</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Batch</th>
                                <th>ED</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Nilai</th>
                            </tr>
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
                                <tr><td colspan="5" class="text-center text-muted">Tidak ada kadaluarsa</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><strong>Reorder List (di bawah minimal stok)</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Minimal Stok</th>
                        <th class="text-end">Qty On Hand</th>
                        <th class="text-end">Kebutuhan</th>
                        <th class="text-end">Avg Daily Sold ({{ config('stock.reorder_sales_lookback_days') }}h)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reorder as $row)
                        <tr>
                            <td>{{ $row->sku }} - {{ $row->nama_dagang }}</td>
                            <td class="text-end">{{ $row->minimal_stok }}</td>
                            <td class="text-end">{{ $row->qty_on_hand }}</td>
                            <td class="text-end fw-semibold">{{ $row->reorder_need }}</td>
                            <td class="text-end">{{ $row->avg_daily_sold }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Tidak ada yang perlu reorder</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
