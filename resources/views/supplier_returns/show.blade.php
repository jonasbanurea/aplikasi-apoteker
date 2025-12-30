@extends('layouts.admin')

@section('title', 'Detail Retur Supplier')
@section('page-title', 'Detail Retur Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">{{ $supplierReturn->return_no }}</h4>
        <small class="text-muted">Retur ke {{ $supplierReturn->supplier->name ?? '-' }}</small>
    </div>
    <a href="{{ route('supplier-returns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Tanggal</div>
                <div class="fw-semibold">{{ $supplierReturn->return_date->format('d M Y') }}</div>
                <div class="text-muted mt-2">Status</div>
                @if($supplierReturn->status === \App\Models\SupplierReturn::STATUS_POSTED)
                    <span class="badge bg-success">POSTED</span>
                @else
                    <span class="badge bg-secondary">VOID</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Qty</div>
                <div class="fw-semibold">{{ $supplierReturn->total_qty }}</div>
                <div class="text-muted mt-2">Total Nilai</div>
                <div class="fw-semibold">Rp {{ number_format($supplierReturn->total_value, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Dibuat oleh</div>
                <div class="fw-semibold">{{ $supplierReturn->user->name ?? '-' }}</div>
                <div class="text-muted mt-2">Catatan</div>
                <div>{{ $supplierReturn->notes ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>ED</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supplierReturn->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->sku ?? '' }} - {{ $item->product->nama_dagang ?? '-' }}</td>
                            <td>{{ $item->batch?->batch_no ?? '-' }}</td>
                            <td>{{ optional($item->batch?->expired_date)->format('d M Y') ?? '-' }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_cost, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            <td>{{ $item->reason ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
