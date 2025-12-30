@extends('layouts.admin')

@section('title', 'Detail Penerimaan')
@section('page-title', 'Detail Penerimaan')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Supplier</div>
                <div class="fw-semibold">{{ $purchase->supplier->name }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">No. Invoice</div>
                <div class="fw-semibold">{{ $purchase->invoice_no }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Tanggal</div>
                <div>{{ $purchase->date?->format('d M Y') }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Jatuh Tempo</div>
                <div>{{ $purchase->due_date?->format('d M Y') ?? '-' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Status</div>
                @if($purchase->status === \App\Models\Purchase::STATUS_CONSIGNMENT)
                    <span class="badge bg-warning text-dark">CONSIGNMENT</span>
                @else
                    <span class="badge bg-success">POSTED</span>
                @endif
            </div>
            <div class="col-md-1">
                <div class="text-muted small">Konsinyasi</div>
                @if($purchase->is_consignment)
                    <i class="bi bi-check-circle text-warning"></i>
                @else
                    <span class="text-muted">-</span>
                @endif
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
                        <th>Expired</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Bonus</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($purchase->items as $item)
                        @php $lineTotal = ($item->qty + $item->bonus_qty) * $item->cost_price; $subtotal += $lineTotal; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->product->nama_dagang ?? '-' }}</div>
                                <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                            </td>
                            <td>{{ $item->batch_no }}</td>
                            <td>{{ $item->expired_date?->format('d M Y') ?? '-' }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">{{ $item->bonus_qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->cost_price, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($lineTotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end">Subtotal</th>
                        <th class="text-end">Rp {{ number_format($subtotal, 2, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" class="text-end">Diskon</th>
                        <th class="text-end">Rp {{ number_format($purchase->discount, 2, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="7" class="text-end">Total</th>
                        <th class="text-end">Rp {{ number_format($purchase->total, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
