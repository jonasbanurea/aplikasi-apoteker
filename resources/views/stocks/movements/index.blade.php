@extends('layouts.admin')

@section('title', 'Kartu Stok')
@section('page-title', 'Kartu Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Kartu Stok</h4>
        <small class="text-muted">Mutasi masuk/keluar per produk & batch</small>
    </div>
    <a href="{{ route('stock-movements.export', request()->query()) }}" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('stock-movements.index') }}">
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select name="product_id" class="form-select">
                    <option value="">Semua produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                            {{ $product->sku }} - {{ $product->nama_dagang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">Semua batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ $batchId == $batch->id ? 'selected' : '' }}>
                            {{ $batch->batch_no }} ({{ $batch->product->sku ?? 'P' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-select">
                    <option value="">Semua</option>
                    <option value="IN" {{ $type === 'IN' ? 'selected' : '' }}>IN</option>
                    <option value="OUT" {{ $type === 'OUT' ? 'selected' : '' }}>OUT</option>
                    <option value="ADJUST" {{ $type === 'ADJUST' ? 'selected' : '' }}>ADJUST</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            @if($productId || $batchId || $type)
            <div class="col-md-1 d-grid">
                <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
            @endif
        </form>
    </div>
</div>

@if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
<div class="card mb-4">
    <div class="card-header bg-white">
        <strong>Catat Mutasi</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('stock-movements.store') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="" disabled selected>Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->sku }} - {{ $product->nama_dagang }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Batch (opsional)</label>
                <select name="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
                    <option value="">Tanpa batch</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->batch_no }} - {{ $batch->product->sku ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="" disabled selected>Pilih</option>
                    <option value="IN" {{ old('type') === 'IN' ? 'selected' : '' }}>IN</option>
                    <option value="OUT" {{ old('type') === 'OUT' ? 'selected' : '' }}>OUT</option>
                    <option value="ADJUST" {{ old('type') === 'ADJUST' ? 'selected' : '' }}>ADJUST</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Qty</label>
                <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty') }}" required>
                @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Ref Type</label>
                <input type="text" name="ref_type" class="form-control @error('ref_type') is-invalid @enderror" value="{{ old('ref_type') }}" placeholder="PO / SO / ADJ">
                @error('ref_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Ref ID</label>
                <input type="number" name="ref_id" class="form-control @error('ref_id') is-invalid @enderror" value="{{ old('ref_id') }}" placeholder="Opsional">
                @error('ref_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Catatan singkat">
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Mutasi</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>Tipe</th>
                        <th class="text-end">Qty</th>
                        <th>Ref</th>
                        <th>User</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movements->firstItem() + $loop->index }}</td>
                            <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $movement->product->sku }} - {{ $movement->product->nama_dagang }}</td>
                            <td>{{ $movement->batch?->batch_no ?? '-' }}</td>
                            <td>
                                @if($movement->type === 'IN')
                                    <span class="badge bg-success">IN</span>
                                @elseif($movement->type === 'OUT')
                                    <span class="badge bg-danger">OUT</span>
                                @else
                                    <span class="badge bg-warning text-dark">ADJUST</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">{{ $movement->qty }}</td>
                            <td>{{ $movement->ref_type ?? '-' }} {{ $movement->ref_id ? '#'.$movement->ref_id : '' }}</td>
                            <td>{{ $movement->user->name ?? '-' }}</td>
                            <td>{{ $movement->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada mutasi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} dari {{ $movements->total() }} data
            </div>
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
