@extends('layouts.admin')

@section('title', 'Supplier')
@section('page-title', 'Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Daftar Supplier</h4>
        <small class="text-muted">Kelola data supplier untuk pembelian</small>
    </div>
    @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Supplier
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('suppliers.index') }}">
            <div class="col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama / kontak" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3 col-lg-2 d-grid d-md-block">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Cari</button>
            </div>
            @if($search)
            <div class="col-md-3 col-lg-2 d-grid d-md-block">
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Term Pembayaran (hari)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact ?? '-' }}</td>
                            <td>{{ $supplier->payment_term_days }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus supplier ini?');">
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
                            <td colspan="5" class="text-center text-muted">Belum ada supplier</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} data
            </div>
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
