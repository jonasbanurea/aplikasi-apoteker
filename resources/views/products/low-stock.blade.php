@extends('layouts.admin')

@section('title', 'Obat Perlu Restock')
@section('page-title', 'Obat Perlu Restock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Obat Perlu Restock</h4>
        <small class="text-muted">Daftar obat yang stoknya di bawah minimal stok</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-box-seam"></i> Semua Produk
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('products.low-stock') }}">
            <div class="col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari SKU / nama dagang / generik" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3 col-lg-2 d-grid d-md-block">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Cari</button>
            </div>
            @if($search)
            <div class="col-md-3 col-lg-2 d-grid d-md-block">
                <a href="{{ route('products.low-stock') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
        </form>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> 
            Ditemukan <strong>{{ $products->total() }}</strong> obat yang perlu restock
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>SKU</th>
                        <th>Nama Dagang</th>
                        <th>Nama Generik</th>
                        <th>Lokasi Rak</th>
                        <th class="text-end">Stok Saat Ini</th>
                        <th class="text-end">Minimal Stok</th>
                        <th class="text-end">Perlu Ditambah</th>
                        <th class="text-center">Status</th>
                        @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                        <th class="text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $kekurangan = $product->minimal_stok - $product->current_stock;
                            $persentase = $product->minimal_stok > 0 ? ($product->current_stock / $product->minimal_stok) * 100 : 0;
                        @endphp
                        <tr class="{{ $product->current_stock == 0 ? 'table-danger' : '' }}">
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $product->sku }}</td>
                            <td>{{ $product->nama_dagang }}</td>
                            <td>{{ $product->nama_generik ?? '-' }}</td>
                            <td>
                                @if($product->lokasi_rak)
                                    <span class="badge bg-secondary">{{ $product->lokasi_rak }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($product->current_stock == 0)
                                    <span class="badge bg-danger">0</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ number_format($product->current_stock, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($product->minimal_stok, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <span class="badge bg-info">{{ number_format($kekurangan, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                @if($product->current_stock == 0)
                                    <span class="badge bg-danger">Habis</span>
                                @elseif($persentase <= 25)
                                    <span class="badge bg-danger">Kritis</span>
                                @elseif($persentase <= 50)
                                    <span class="badge bg-warning text-dark">Rendah</span>
                                @else
                                    <span class="badge bg-info">Menipis</span>
                                @endif
                            </td>
                            @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                            <td class="text-end">
                                <a href="{{ route('purchases.create', ['product_id' => $product->id]) }}" 
                                   class="btn btn-sm btn-primary" 
                                   title="Buat Pembelian">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data obat yang perlu restock.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="mt-3">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
