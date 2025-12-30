@extends('layouts.admin')

@section('title', 'Tambah Batch Stok')
@section('page-title', 'Tambah Batch Stok')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('stock-batches.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Produk</label>
                    <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $batch->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->sku }} - {{ $product->nama_dagang }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch No</label>
                    <input type="text" name="batch_no" class="form-control @error('batch_no') is-invalid @enderror" value="{{ old('batch_no', $batch->batch_no) }}" required>
                    @error('batch_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Expired Date</label>
                    <input type="date" name="expired_date" class="form-control @error('expired_date') is-invalid @enderror" value="{{ old('expired_date', optional($batch->expired_date)->format('Y-m-d')) }}">
                    @error('expired_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Qty on Hand</label>
                    <input type="number" name="qty_on_hand" min="0" class="form-control @error('qty_on_hand') is-invalid @enderror" value="{{ old('qty_on_hand', $batch->qty_on_hand ?? 0) }}" required>
                    @error('qty_on_hand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cost Price</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" step="0.01" min="0" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $batch->cost_price ?? 0) }}" required>
                    </div>
                    @error('cost_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Diterima</label>
                    <input type="date" name="received_at" class="form-control @error('received_at') is-invalid @enderror" value="{{ old('received_at', optional($batch->received_at)->format('Y-m-d')) }}">
                    @error('received_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('stock-batches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
