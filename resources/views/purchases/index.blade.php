@extends('layouts.admin')

@section('title', 'Penerimaan Barang')
@section('page-title', 'Penerimaan Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Penerimaan / Pembelian</h4>
        <small class="text-muted">Catat penerimaan barang dan status hutang/konsinyasi</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.export') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Penerimaan Baru
        </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Invoice</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Diskon</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Konsinyasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchases->firstItem() + $loop->index }}</td>
                            <td>{{ $purchase->date?->format('d M Y') }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td class="fw-semibold">{{ $purchase->invoice_no }}</td>
                            <td class="text-end">Rp {{ number_format($purchase->total, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($purchase->discount, 2, ',', '.') }}</td>
                            <td>{{ $purchase->due_date?->format('d M Y') ?? '-' }}</td>
                            <td>
                                @if($purchase->status === \App\Models\Purchase::STATUS_CONSIGNMENT)
                                    <span class="badge bg-warning text-dark">CONSIGNMENT</span>
                                @else
                                    <span class="badge bg-success">POSTED</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($purchase->is_consignment)
                                    <i class="bi bi-check-circle text-warning"></i>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada penerimaan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $purchases->firstItem() ?? 0 }} - {{ $purchases->lastItem() ?? 0 }} dari {{ $purchases->total() }} data
            </div>
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
