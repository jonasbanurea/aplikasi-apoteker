@extends('layouts.admin')

@section('title', 'Produk / Obat')
@section('page-title', 'Produk / Obat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Daftar Produk / Obat</h4>
        <small class="text-muted">Kelola master produk dan harga jual</small>
    </div>
    @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Produk
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('products.index') }}">
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
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>SKU</th>
                        <th>Nama Dagang</th>
                        <th>Nama Generik</th>
                        <th>Golongan</th>
                        <th class="text-center">Wajib Resep</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Minimal Stok</th>
                        <th class="text-center">Konsinyasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $product->sku }}</td>
                            <td>{{ $product->nama_dagang }}</td>
                            <td>{{ $product->nama_generik ?? '-' }}</td>
                            <td><span class="badge bg-info text-dark">{{ $product->golongan }}</span></td>
                            <td class="text-center">
                                @if($product->wajib_resep)
                                    <span class="badge bg-danger">Ya</span>
                                @else
                                    <span class="badge bg-success">Tidak</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($product->harga_jual, 2, ',', '.') }}</td>
                            <td class="text-end">{{ $product->minimal_stok }}</td>
                            <td class="text-center">
                                @if($product->konsinyasi)
                                    <span class="badge bg-warning text-dark">Konsinyasi</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">Read-only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} data
            </div>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
