@extends('layouts.admin')

@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard Owner')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-muted">Dashboard Owner - Toko Obat Ro Tua</p>
    </div>
</div>

<div class="row g-3">
    <!-- Total Pendapatan -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <h3 class="mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <small class="{{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i> 
                            {{ number_format(abs($revenueGrowth), 1) }}% dari bulan lalu
                        </small>
                    </div>
                    <div class="text-primary fs-1">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transaksi -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Transaksi</h6>
                        <h3 class="mb-0">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                        <small class="text-muted">{{ $todayTransactions }} hari ini</small>
                    </div>
                    <div class="text-success fs-1">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Obat -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Stok Obat</h6>
                        <h3 class="mb-0">{{ number_format($totalStock, 0, ',', '.') }}</h3>
                        <small class="{{ $reorderAlerts > 0 ? 'text-warning' : 'text-muted' }}">
                            <i class="bi bi-exclamation-triangle"></i> {{ $reorderAlerts }} obat hampir habis
                        </small>
                    </div>
                    <div class="text-warning fs-1">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total User -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total User</h6>
                        <h3 class="mb-0">{{ $totalUsers }}</h3>
                        <small class="text-muted">Pengguna aktif</small>
                    </div>
                    <div class="text-danger fs-1">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Grafik Penjualan (7 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                @if(count($chartData) > 0)
                <canvas id="salesChart" height="80"></canvas>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Belum ada data penjualan</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-star"></i> Top Produk</h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($topProducts as $product)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold">{{ $product->nama_dagang }}</div>
                                <small class="text-muted">{{ $product->sku }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">{{ number_format($product->total_qty) }} pcs</div>
                                <small class="text-muted">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-star text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Belum ada data penjualan</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($nearExpired > 0 || $expired > 0 || $reorderAlerts > 0 || $duePayables > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Peringatan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($expired > 0)
                    <div class="col-md-3">
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle"></i> <strong>{{ $expired }}</strong> batch kadaluarsa
                        </div>
                    </div>
                    @endif
                    @if($nearExpired > 0)
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-clock"></i> <strong>{{ $nearExpired }}</strong> batch hampir kadaluarsa
                        </div>
                    </div>
                    @endif
                    @if($reorderAlerts > 0)
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-box"></i> <strong>{{ $reorderAlerts }}</strong> obat perlu restock
                        </div>
                    </div>
                    @endif
                    @if($duePayables > 0)
                    <div class="col-md-3">
                        <div class="alert alert-primary mb-0">
                            <i class="bi bi-cash"></i> <strong>{{ $duePayables }}</strong> hutang jatuh tempo
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(count($chartData) > 0)
const ctx = document.getElementById('salesChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column($chartData, 'date')),
            datasets: [{
                label: 'Transaksi',
                data: @json(array_column($chartData, 'count')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Total (Rp)',
                data: @json(array_column($chartData, 'total')),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total (Rp)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}
@endif
</script>
@endpush
