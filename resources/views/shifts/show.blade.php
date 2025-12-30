@extends('layouts.admin')

@section('title', 'Detail Shift')
@section('page-title', 'Detail Shift')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Shift #{{ $shift->id }}</h4>
        <small class="text-muted">{{ $shift->opened_at?->format('d M Y H:i') }} @if($shift->closed_at) - {{ $shift->closed_at?->format('d M Y H:i') }} @endif</small>
    </div>
    <div class="d-flex gap-2">
        @if(!$shift->closed_at && auth()->id() === $shift->user_id)
            <a href="{{ route('shifts.closeForm', $shift) }}" class="btn btn-outline-primary"><i class="bi bi-door-closed"></i> Tutup Shift</a>
        @endif
        <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Kasir</div>
                <div class="fw-semibold">{{ $shift->user?->name ?? '-' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Opening Cash</div>
                <div>Rp {{ number_format($shift->opening_cash, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Expected Cash</div>
                <div>Rp {{ number_format($shift->cash_expected, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Actual Cash</div>
                <div>Rp {{ number_format($shift->closing_cash_actual, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Selisih</div>
                <div class="fw-semibold {{ $shift->discrepancy == 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($shift->discrepancy, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                @if($shift->closed_at)
                    <span class="badge bg-secondary">Closed</span>
                @else
                    <span class="badge bg-warning text-dark">Open</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Total Penjualan</div>
                <div class="h5 mb-0">Rp {{ number_format($totals['total_sales'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Cash</div>
                <div class="h5 mb-0">Rp {{ number_format($totals['total_cash'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Non-Cash</div>
                <div class="h5 mb-0">Rp {{ number_format($totals['total_non_cash'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">Transaksi (Shift)</h6>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Invoice</th>
                        <th>Metode</th>
                        <th class="text-end">Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_date?->format('d M Y H:i') }}</td>
                            <td class="fw-semibold">{{ $sale->invoice_no }}</td>
                            <td>
                                @if($sale->payment_method === \App\Models\Sale::METHOD_CASH)
                                    <span class="badge bg-success">CASH</span>
                                @else
                                    <span class="badge bg-info text-dark">NON CASH</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($sale->total, 2, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada transaksi pada shift ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $sales->firstItem() ?? 0 }} - {{ $sales->lastItem() ?? 0 }} dari {{ $sales->total() }} data
            </div>
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
