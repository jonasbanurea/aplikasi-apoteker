@extends('layouts.admin')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-muted">Dashboard Kasir - Toko Obat Rotua</p>
    </div>
</div>

<div class="row g-3">
    <!-- Transaksi Hari Ini -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Transaksi Hari Ini</h6>
                        <h3 class="mb-0">{{ number_format($todayTransactions, 0, ',', '.') }}</h3>
                        <small class="text-muted">Total transaksi</small>
                    </div>
                    <div class="text-primary fs-1">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Hari Ini -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Pendapatan Hari Ini</h6>
                        <h3 class="mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                        <small class="text-muted">Total penjualan</small>
                    </div>
                    <div class="text-success fs-1">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Terjual -->
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Item Terjual</h6>
                        <h3 class="mb-0">{{ number_format($todayItems, 0, ',', '.') }}</h3>
                        <small class="text-muted">Hari ini</small>
                    </div>
                    <div class="text-warning fs-1">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Transaksi Terakhir</h5>
                <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Transaksi Baru
                </a>
            </div>
            <div class="card-body">
                @if($recentSales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>{{ optional($sale->sale_date)->format('d M H:i') }}</td>
                                <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Belum ada transaksi</p>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Mulai Transaksi
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
