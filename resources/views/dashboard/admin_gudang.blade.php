@extends('layouts.admin')

@section('title', 'Dashboard Admin Gudang')
@section('page-title', 'Dashboard Admin Gudang')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-muted">Dashboard Admin Gudang - Toko Obat Rotua</p>
    </div>
</div>

<div class="row g-3">
    <!-- Total Stok -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Stok</h6>
                        <h3 class="mb-0">{{ number_format($totalStock, 0, ',', '.') }}</h3>
                        <small class="text-muted">Qty on hand</small>
                    </div>
                    <div class="text-primary fs-1">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Stok Menipis</h6>
                        <h3 class="mb-0">{{ number_format($reorderAlerts, 0, ',', '.') }}</h3>
                        <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perlu restock</small>
                    </div>
                    <div class="text-warning fs-1">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Obat Masuk -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Obat Masuk</h6>
                        <h3 class="mb-0">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                        <small class="text-muted">Transaksi penjualan bln ini</small>
                    </div>
                    <div class="text-success fs-1">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Obat Keluar -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Obat Keluar</h6>
                        <h3 class="mb-0">{{ number_format($todayItems, 0, ',', '.') }}</h3>
                        <small class="text-muted">Item terjual hari ini</small>
                    </div>
                    <div class="text-danger fs-1">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Menipis</h5>
            </div>
            <div class="card-body">
                @if($lowStockProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $prod)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $prod->nama_dagang }}</div>
                                    <small class="text-muted">{{ $prod->sku }}</small>
                                </td>
                                <td class="text-end">{{ number_format($prod->stock_on_hand, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Semua stok dalam kondisi baik</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Belum ada aktivitas</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
