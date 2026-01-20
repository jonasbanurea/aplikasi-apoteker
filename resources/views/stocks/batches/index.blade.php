@extends('layouts.admin')

@section('title', 'Stok per Batch')
@section('page-title', 'Stok per Batch')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Stok per Batch</h4>
        <small class="text-muted">Pantau batch, ED, dan jumlah per produk (FEFO)</small>
    </div>
    @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
    <a href="{{ route('stock-batches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Batch
    </a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('stock-batches.index') }}">
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select name="product_id" id="productSelect" class="form-select" data-placeholder="Ketik untuk mencari produk...">
                    <option value="">Semua produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $searchProduct == $product->id ? 'selected' : '' }}>
                            {{ $product->sku }} - {{ $product->nama_dagang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="near_expired" value="1" id="nearExpired" {{ $nearExpired ? 'checked' : '' }}>
                    <label class="form-check-label" for="nearExpired">Tampilkan ED <= {{ $thresholdDays }} hari</label>
                </div>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            @if($searchProduct || $nearExpired)
            <div class="col-md-2 d-grid">
                <a href="{{ route('stock-batches.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
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
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>Expired</th>
                        <th>Qty on Hand</th>
                        <th>Cost</th>
                        <th>Diterima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr class="{{ $batch->expired_date && $batch->expired_date->isPast() ? 'table-danger' : '' }}">
                            <td>{{ $batches->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $batch->product->nama_dagang }}</div>
                                <small class="text-muted">{{ $batch->product->sku }}</small>
                            </td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>
                                @if($batch->expired_date)
                                    {{ $batch->expired_date->format('d M Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $batch->qty_on_hand }}</td>
                            <td>Rp {{ number_format($batch->cost_price, 2, ',', '.') }}</td>
                            <td>{{ $batch->received_at ? $batch->received_at->format('d M Y') : '-' }}</td>
                            <td>
                                @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                                <a href="{{ route('stock-batches.edit', $batch) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Batch">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                <a href="{{ route('stock-movements.index', ['product_id' => $batch->product_id, 'batch_id' => $batch->id]) }}" class="btn btn-sm btn-outline-secondary" title="Kartu Stok">
                                    <i class="bi bi-arrow-left-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada batch</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $batches->firstItem() ?? 0 }} - {{ $batches->lastItem() ?? 0 }} dari {{ $batches->total() }} data
            </div>
            {{ $batches->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#productSelect').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik untuk mencari produk...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "Produk tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
    });
</script>
@endpush
