@extends('layouts.admin')

@section('title', 'Buka Shift')
@section('page-title', 'Buka Shift')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shifts.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Modal Awal (Cash)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" min="0" name="opening_cash" class="form-control @error('opening_cash') is-invalid @enderror" value="{{ old('opening_cash', 0) }}" required>
                    @error('opening_cash')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-door-open"></i> Buka Shift</button>
            </div>
        </form>
    </div>
</div>
@endsection
