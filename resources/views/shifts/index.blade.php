@extends('layouts.admin')

@section('title', 'Shift Kasir')
@section('page-title', 'Shift Kasir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Shift Kasir</h4>
        <small class="text-muted">Kontrol buka/tutup shift dan ringkasan kas</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('shifts.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
        <a href="{{ route('shifts.create') }}" class="btn btn-primary">
            <i class="bi bi-door-open"></i> Buka Shift
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('shifts.index') }}">
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            @if($date || $status)
            <div class="col-md-2 d-grid">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
        </form>
    </div>
</div>

@if($openShift)
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Shift aktif: ID #{{ $openShift->id }} (dibuka {{ $openShift->opened_at?->format('d M Y H:i') }}) dengan kas awal Rp {{ number_format($openShift->opening_cash, 2, ',', '.') }}.
    <a href="{{ route('shifts.closeForm', $openShift) }}" class="ms-2">Tutup shift</a>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kasir</th>
                        <th>Dibuka</th>
                        <th>Ditutup</th>
                        <th class="text-end">Opening</th>
                        <th class="text-end">Expected Cash</th>
                        <th class="text-end">Actual Cash</th>
                        <th class="text-end">Selisih</th>
                        <th class="text-end">Cash</th>
                        <th class="text-end">Non-Cash</th>
                        <th class="text-end">Total Sales</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr class="{{ $shift->closed_at ? '' : 'table-warning' }}">
                            <td>{{ $shift->id }}</td>
                            <td>{{ $shift->user?->name ?? '-' }}</td>
                            <td>{{ $shift->opened_at?->format('d M Y H:i') }}</td>
                            <td>{{ $shift->closed_at?->format('d M Y H:i') ?? 'Open' }}</td>
                            <td class="text-end">Rp {{ number_format($shift->opening_cash, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($shift->cash_expected, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($shift->closing_cash_actual, 2, ',', '.') }}</td>
                            <td class="text-end fw-semibold {{ $shift->discrepancy == 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($shift->discrepancy, 2, ',', '.') }}
                            </td>
                            <td class="text-end">Rp {{ number_format($shift->total_cash ?? 0, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($shift->total_non_cash ?? 0, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($shift->total_sales ?? 0, 2, ',', '.') }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('shifts.show', $shift) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                @if(!$shift->closed_at && auth()->id() === $shift->user_id)
                                    <a href="{{ route('shifts.closeForm', $shift) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-door-closed"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">Belum ada shift</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted">
                Menampilkan {{ $shifts->firstItem() ?? 0 }} - {{ $shifts->lastItem() ?? 0 }} dari {{ $shifts->total() }} data
            </div>
            {{ $shifts->links() }}
        </div>
    </div>
</div>
@endsection
