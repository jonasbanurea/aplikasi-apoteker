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
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </button>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('sales.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari Produk</label>
                    <input type="text" name="product_search" class="form-control" placeholder="Nama/SKU Produk" value="{{ request('product_search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari Invoice</label>
                    <input type="text" name="invoice_search" class="form-control" placeholder="No Invoice" value="{{ request('invoice_search') }}">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            </div>
        </form>
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
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="{{ $sale->is_cancelled ? 'table-danger' : '' }}">
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
                                @if($sale->is_cancelled)
                                    <span class="badge bg-danger">DIBATALKAN</span>
                                @else
                                    <span class="badge bg-success">AKTIF</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!$sale->is_cancelled)
                                    <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada penjualan</td>
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Excel Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('sales.export') }}">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Export akan mengunduh semua data penjualan sesuai filter tanggal yang dipilih.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        <small class="text-muted">Kosongkan untuk export semua data</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        <small class="text-muted">Kosongkan untuk export semua data</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
