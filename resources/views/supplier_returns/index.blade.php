@extends('layouts.admin')

@section('title', 'Retur Supplier')
@section('page-title', 'Retur Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Retur ke Supplier</h4>
        <small class="text-muted">Catat pengembalian ke supplier dan pengurangan stok</small>
    </div>
    @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
    <a href="{{ route('supplier-returns.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Retur
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>No Retur</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th class="text-end">Total Qty</th>
                        <th class="text-end">Total Nilai</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $row)
                        <tr>
                            <td>{{ $returns->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $row->return_no }}</td>
                            <td>{{ $row->return_date->format('d M Y') }}</td>
                            <td>{{ $row->supplier->name ?? '-' }}</td>
                            <td class="text-end">{{ $row->total_qty }}</td>
                            <td class="text-end">Rp {{ number_format($row->total_value, 2, ',', '.') }}</td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td>
                                @if($row->status === \App\Models\SupplierReturn::STATUS_POSTED)
                                    <span class="badge bg-success">POSTED</span>
                                @else
                                    <span class="badge bg-secondary">VOID</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('supplier-returns.show', $row) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada retur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $returns->firstItem() ?? 0 }} - {{ $returns->lastItem() ?? 0 }} dari {{ $returns->total() }} data
            </div>
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
