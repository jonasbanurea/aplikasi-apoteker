@extends('layouts.admin')

@section('title', 'Penjualan (POS)')
@section('page-title', 'Penjualan (POS)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Daftar Penjualan</h4>
        <small class="text-muted">Riwayat transaksi POS</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.export') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Transaksi Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Invoice</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Bayar</th>
                        <th class="text-end">Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sales->firstItem() + $loop->index }}</td>
                            <td>{{ $sale->sale_date?->format('d M Y H:i') }}</td>
                            <td class="fw-semibold">{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->user?->name ?? '-' }}</td>
                            <td>
                                @if($sale->payment_method === \App\Models\Sale::METHOD_CASH)
                                    <span class="badge bg-success">CASH</span>
                                @else
                                    <span class="badge bg-info text-dark">NON CASH</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($sale->total, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sale->paid_amount, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($sale->change_amount, 2, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada penjualan</td>
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
