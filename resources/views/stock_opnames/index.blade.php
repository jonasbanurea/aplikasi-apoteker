@extends('layouts.admin')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Stock Opname</h4>
        <small class="text-muted">Hitung stok fisik, selisih, dan approval</small>
    </div>
    @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
    <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Opname
    </a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('stock-opnames.index') }}">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="PENDING_APPROVAL" {{ $status === 'PENDING_APPROVAL' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="APPROVED" {{ $status === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                    <option value="REJECTED" {{ $status === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            @if($status)
            <div class="col-md-2 d-grid">
                <a href="{{ route('stock-opnames.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
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
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-end">Diff Qty</th>
                        <th class="text-end">Selisih Nilai</th>
                        <th>Dibuat oleh</th>
                        <th>Approval</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $row)
                        <tr>
                            <td>{{ $opnames->firstItem() + $loop->index }}</td>
                            <td>{{ $row->opname_date->format('d M Y') }}</td>
                            <td>
                                @if($row->status === \App\Models\StockOpname::STATUS_APPROVED)
                                    <span class="badge bg-success">APPROVED</span>
                                @elseif($row->status === \App\Models\StockOpname::STATUS_REJECTED)
                                    <span class="badge bg-secondary">REJECTED</span>
                                @else
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $row->total_diff_qty }}</td>
                            <td class="text-end">Rp {{ number_format($row->total_diff_value, 2, ',', '.') }}</td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td>
                                @if($row->status === \App\Models\StockOpname::STATUS_APPROVED)
                                    <div class="small text-muted">{{ $row->approver->name ?? '-' }}</div>
                                    <div class="small">{{ optional($row->approved_at)->format('d M Y H:i') }}</div>
                                @elseif($row->status === \App\Models\StockOpname::STATUS_PENDING)
                                    <span class="text-muted small">Menunggu owner</span>
                                @else
                                    <div class="small text-muted">Ditolak</div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('stock-opnames.show', $row) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada opname</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $opnames->firstItem() ?? 0 }} - {{ $opnames->lastItem() ?? 0 }} dari {{ $opnames->total() }} data
            </div>
            {{ $opnames->links() }}
        </div>
    </div>
</div>
@endsection
