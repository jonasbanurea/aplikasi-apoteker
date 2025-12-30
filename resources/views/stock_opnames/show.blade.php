@extends('layouts.admin')

@section('title', 'Detail Stock Opname')
@section('page-title', 'Detail Stock Opname')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Stock Opname {{ $stockOpname->id }}</h4>
        <small class="text-muted">Tanggal {{ $stockOpname->opname_date->format('d M Y') }}</small>
    </div>
    <a href="{{ route('stock-opnames.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Status</div>
                @if($stockOpname->status === \App\Models\StockOpname::STATUS_APPROVED)
                    <span class="badge bg-success">APPROVED</span>
                @elseif($stockOpname->status === \App\Models\StockOpname::STATUS_REJECTED)
                    <span class="badge bg-secondary">REJECTED</span>
                @else
                    <span class="badge bg-warning text-dark">PENDING APPROVAL</span>
                @endif
                <div class="text-muted mt-3">Creator</div>
                <div class="fw-semibold">{{ $stockOpname->user->name ?? '-' }}</div>
                <div class="text-muted mt-3">Catatan</div>
                <div>{{ $stockOpname->notes ?? '-' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Diff Qty</div>
                <div class="fw-semibold">{{ $stockOpname->total_diff_qty }}</div>
                <div class="text-muted mt-2">Selisih Nilai</div>
                <div class="fw-semibold">Rp {{ number_format($stockOpname->total_diff_value, 2, ',', '.') }}</div>
                <div class="text-muted mt-2">Butuh Approval Owner</div>
                <div class="fw-semibold">{{ $stockOpname->requires_approval ? 'Ya' : 'Tidak' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Approval</div>
                <div class="fw-semibold">{{ $stockOpname->approver?->name ?? '-' }}</div>
                <div class="text-muted mt-2">Waktu</div>
                <div>{{ optional($stockOpname->approved_at)->format('d M Y H:i') ?? '-' }}</div>
                <div class="text-muted mt-2">Catatan Approval</div>
                <div>{{ $stockOpname->approval_notes ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

@if($stockOpname->status === \App\Models\StockOpname::STATUS_PENDING && auth()->user()->hasRole('owner'))
<div class="card mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('stock-opnames.approve', $stockOpname) }}" id="approvalForm">
            @csrf
            <input type="hidden" name="action" id="approvalAction" value="approve">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Catatan Approval</label>
                    <input type="text" name="approval_notes" class="form-control" placeholder="Opsional">
                </div>
                <div class="col-md-4 d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-danger" data-action="reject"><i class="bi bi-x-circle"></i> Tolak</button>
                    <button type="button" class="btn btn-success" data-action="approve"><i class="bi bi-check-circle"></i> Setujui & Sesuaikan</button>
                </div>
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
                        <th>Produk</th>
                        <th>Batch</th>
                        <th class="text-end">Qty Sistem</th>
                        <th class="text-end">Qty Fisik</th>
                        <th class="text-end">Selisih</th>
                        <th>Reason</th>
                        <th class="text-end">Nilai Selisih</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->sku ?? '' }} - {{ $item->product->nama_dagang ?? '-' }}</td>
                            <td>{{ $item->batch?->batch_no ?? '-' }}</td>
                            <td class="text-end">{{ $item->system_qty }}</td>
                            <td class="text-end">{{ $item->physical_qty }}</td>
                            <td class="text-end fw-semibold">{{ $item->diff_qty }}</td>
                            <td>{{ $item->reason }}</td>
                            <td class="text-end">Rp {{ number_format($item->diff_value, 2, ',', '.') }}</td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($stockOpname->status === \App\Models\StockOpname::STATUS_PENDING && auth()->user()->hasRole('owner'))
<script>
(function() {
    const form = document.getElementById('approvalForm');
    const actionInput = document.getElementById('approvalAction');
    form.querySelectorAll('button[data-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            actionInput.value = btn.getAttribute('data-action');
            form.submit();
        });
    });
})();
</script>
@endif
@endpush
