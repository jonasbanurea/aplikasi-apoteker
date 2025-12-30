@extends('layouts.admin')

@section('title', 'Tutup Shift')
@section('page-title', 'Tutup Shift')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Shift</div>
                <div class="fw-semibold">#{{ $shift->id }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Kasir</div>
                <div class="fw-semibold">{{ $shift->user?->name ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Dibuka</div>
                <div>{{ $shift->opened_at?->format('d M Y H:i') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Opening Cash</div>
                <div>Rp {{ number_format($shift->opening_cash, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shifts.close', $shift) }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Expected Cash</label>
                    <input type="text" class="form-control" value="Rp {{ number_format($totals['expected_cash'], 2, ',', '.') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cash Penjualan (CASH)</label>
                    <input type="text" class="form-control" value="Rp {{ number_format($totals['total_cash'], 2, ',', '.') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cash Aktual</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" step="0.01" min="0" name="closing_cash_actual" class="form-control @error('closing_cash_actual') is-invalid @enderror" value="{{ old('closing_cash_actual', $totals['expected_cash']) }}" required>
                        @error('closing_cash_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-door-closed"></i> Tutup Shift</button>
            </div>
        </form>
    </div>
</div>
@endsection
