@extends('layouts.admin')

@section('title', 'Batch Hampir/Sudah Kadaluarsa')
@section('page-title', 'Batch Hampir/Sudah Kadaluarsa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Batch Hampir/Sudah Kadaluarsa</h4>
        <small class="text-muted">Daftar batch dengan expired date dalam {{ $thresholdDays }} hari atau sudah kadaluarsa</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <a href="{{ route('stock-batches.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-box-seam"></i> Semua Batch
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Sudah Kadaluarsa</h6>
                        <h3 class="mb-0">{{ number_format($expiredCount, 0, ',', '.') }}</h3>
                        <small>Batch yang sudah expired</small>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Hampir Kadaluarsa</h6>
                        <h3 class="mb-0">{{ number_format($nearExpiredCount, 0, ',', '.') }}</h3>
                        <small>Dalam {{ $thresholdDays }} hari ke depan</small>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter Form -->
        <form class="row g-2 mb-3" method="GET" action="{{ route('stock-batches.near-expiry') }}">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari produk / batch" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Sudah Kadaluarsa</option>
                    <option value="near_expired" {{ $status === 'near_expired' ? 'selected' : '' }}>Hampir Kadaluarsa ({{ $thresholdDays }} hari)</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            @if($search || $status !== 'all')
            <div class="col-md-2 d-grid">
                <a href="{{ route('stock-batches.near-expiry') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
            @endif
        </form>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> 
            Menampilkan <strong>{{ $batches->total() }}</strong> batch yang perlu perhatian
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Batch No</th>
                        <th>Expired Date</th>
                        <th class="text-end">Stok Tersisa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Sisa Hari</th>
                        @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                        <th class="text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($batches as $batch)
                        @php
                            $today = now();
                            $expiredDate = \Carbon\Carbon::parse($batch->expired_date);
                            $daysRemaining = $today->diffInDays($expiredDate, false);
                            $isExpired = $expiredDate->lt($today);
                            
                            if ($isExpired) {
                                $rowClass = 'table-danger';
                                $statusBadge = '<span class="badge bg-danger">Sudah Expired</span>';
                                $daysText = abs($daysRemaining) . ' hari lalu';
                            } elseif ($daysRemaining <= 7) {
                                $rowClass = 'table-danger';
                                $statusBadge = '<span class="badge bg-danger">Kritis</span>';
                                $daysText = $daysRemaining . ' hari lagi';
                            } elseif ($daysRemaining <= 14) {
                                $rowClass = 'table-warning';
                                $statusBadge = '<span class="badge bg-warning text-dark">Peringatan</span>';
                                $daysText = $daysRemaining . ' hari lagi';
                            } else {
                                $rowClass = '';
                                $statusBadge = '<span class="badge bg-info">Perhatian</span>';
                                $daysText = $daysRemaining . ' hari lagi';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $batches->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $batch->product->nama_dagang }}</strong>
                                @if($batch->product->nama_generik)
                                <br><small class="text-muted">{{ $batch->product->nama_generik }}</small>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $batch->product->sku }}</td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>
                                <span class="{{ $isExpired ? 'text-danger fw-bold' : '' }}">
                                    {{ $expiredDate->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $batch->qty_on_hand > 0 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                    {{ number_format($batch->qty_on_hand, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">{!! $statusBadge !!}</td>
                            <td class="text-center">
                                <span class="{{ $isExpired ? 'text-danger' : 'text-warning' }}">
                                    {{ $daysText }}
                                </span>
                            </td>
                            @if(auth()->user()->hasAnyRole('owner', 'admin_gudang'))
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock-batches.edit', $batch) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada batch yang hampir/sudah kadaluarsa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
        <div class="mt-3">
            {{ $batches->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Info Panel -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h6>
        <ul class="mb-0">
            <li><strong>Sudah Expired:</strong> Batch yang expired date-nya sudah lewat dari hari ini</li>
            <li><strong>Kritis (≤7 hari):</strong> Batch akan expired dalam 7 hari atau kurang</li>
            <li><strong>Peringatan (8-14 hari):</strong> Batch akan expired dalam 8-14 hari</li>
            <li><strong>Perhatian (15-{{ $thresholdDays }} hari):</strong> Batch akan expired dalam 15-{{ $thresholdDays }} hari</li>
            <li>Hanya menampilkan batch yang masih memiliki stok (qty_on_hand > 0)</li>
        </ul>
    </div>
</div>
@endsection
