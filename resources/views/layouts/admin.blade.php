<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom inline styles AFTER Bootstrap to override -->
    <style>
        body {
            font-size: 0.95rem;
        }
        
        /* Sidebar Styles */
        nav#sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
            padding-bottom: 24px;
            overscroll-behavior: contain;
        }

        /* Collapsed state - MUST override */
        nav#sidebar.collapsed {
            width: 72px !important;
            overflow: visible !important;
        }
        
        nav#sidebar.collapsed .sidebar-header h4,
        nav#sidebar.collapsed .sidebar-header small,
        nav#sidebar.collapsed .nav-item small {
            display: none !important;
        }
        
        nav#sidebar.collapsed .nav-link {
            justify-content: center !important;
            padding: 12px 10px !important;
            font-size: 0 !important;
        }
        
        nav#sidebar.collapsed .nav-link i {
            margin-right: 0 !important;
            font-size: 1.2rem !important;
            width: auto !important;
        }
        
        nav#sidebar.collapsed .nav-item form button.nav-link {
            justify-content: center !important;
            padding: 12px 10px !important;
            font-size: 0 !important;
        }
        
        nav#sidebar.collapsed .nav-item form button.nav-link i {
            margin-right: 0 !important;
            font-size: 1.2rem !important;
        }

        /* Chrome scrollbar */
        nav#sidebar::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.35);
            border-radius: 10px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
        }
        
        #sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        #sidebar .text-muted,
        #sidebar small {
            color: rgba(255,255,255,0.75) !important;
        }
        
        #sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main Content */
        #content {
            margin-left: 250px;
            width: calc(100% - 250px);
            transition: all 0.3s;
        }

        /* Content shift when sidebar collapsed */
        #content.sidebar-collapsed {
            margin-left: 72px !important;
            width: calc(100% - 72px) !important;
        }

            /* Sidebar toggle button */
            #sidebarToggle {
                color: #2c3e50;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.25rem;
                cursor: pointer;
            }
            #sidebarToggle:focus {
                box-shadow: none;
            }
            #sidebarToggle i {
                display: inline-block;
                font-size: 1.5rem;
                pointer-events: none;
            }
        
        /* Topbar */
        .topbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 30px;
        }
        
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .topbar .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* Main Content Area */
        .main-content {
            padding: 30px;
            min-height: calc(100vh - 70px);
            background: #f8f9fa;
        }
        
        /* Cards */
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card.primary {
            border-left-color: #667eea;
        }
        
        .stat-card.success {
            border-left-color: #28a745;
        }
        
        .stat-card.warning {
            border-left-color: #ffc107;
        }
        
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        
        /* Sidebar Toggle for Mobile */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">
                <i class="bi bi-capsule"></i> {{ config('app.name') }}
            </h4>
            <small class="text-muted">Sistem Apoteker</small>
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @hasanyrole('owner|admin_gudang|kasir')
            <li class="nav-item mt-3">
                <small class="text-muted px-3">MASTER DATA</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="bi bi-capsule"></i> Produk/Obat
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('owner|admin_gudang|kasir')
            @php $hasSales = Route::has('sales.index'); @endphp
            @if($hasSales)
            <li class="nav-item mt-3">
                <small class="text-muted px-3">PENJUALAN</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                    <i class="bi bi-cart"></i> POS Penjualan
                </a>
            </li>
            @endif
            @endhasanyrole

            @hasanyrole('owner|admin_gudang|kasir')
            @php $hasShifts = Route::has('shifts.index'); @endphp
            @if($hasShifts)
            <li class="nav-item mt-3">
                <small class="text-muted px-3">SHIFT & ABSENSI</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}" href="{{ route('shifts.index') }}">
                    <i class="bi bi-clock-history"></i> Absensi Kasir
                </a>
            </li>
            @endif
            @endhasanyrole

            @hasanyrole('owner|admin_gudang|kasir')
            @php $hasPurchases = Route::has('purchases.index'); @endphp
            @if($hasPurchases)
            <li class="nav-item mt-3">
                <small class="text-muted px-3">PEMBELIAN</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                    <i class="bi bi-receipt"></i> Penerimaan Barang
                </a>
            </li>
            @endif
            @endhasanyrole

            @hasanyrole('owner|admin_gudang|kasir')
            @php 
                $hasStockBatch = Route::has('stock-batches.index');
                $hasStockMovement = Route::has('stock-movements.index');
                $hasStockOpname = Route::has('stock-opnames.index');
                $hasSupplierReturn = Route::has('supplier-returns.index');
            @endphp
            @if($hasStockBatch || $hasStockMovement || $hasStockOpname || $hasSupplierReturn)
            <li class="nav-item mt-3">
                <small class="text-muted px-3">STOK</small>
            </li>
            @if($hasStockBatch)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock-batches.*') ? 'active' : '' }}" href="{{ route('stock-batches.index') }}">
                    <i class="bi bi-box"></i> Stok per Batch
                </a>
            </li>
            @endif
            @if($hasStockMovement)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}">
                    <i class="bi bi-arrow-left-right"></i> Kartu Stok
                </a>
            </li>
            @endif
            @if($hasStockOpname)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}" href="{{ route('stock-opnames.index') }}">
                    <i class="bi bi-clipboard-check"></i> Stock Opname
                </a>
            </li>
            @endif
            @if($hasSupplierReturn)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('supplier-returns.*') ? 'active' : '' }}" href="{{ route('supplier-returns.index') }}">
                    <i class="bi bi-box-arrow-left"></i> Retur Supplier
                </a>
            </li>
            @endif
            @endif
            @endhasanyrole

            @hasanyrole('owner|admin_gudang|kasir')
            @php $hasReports = Route::has('reports.index'); @endphp
            @if($hasReports)
            <li class="nav-item mt-3">
                <small class="text-muted px-3">LAPORAN</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="bi bi-graph-up"></i> Laporan
                </a>
            </li>
            @endif
            @endhasanyrole
            
            @role('owner')
            <li class="nav-item mt-3">
                <small class="text-muted px-3">OWNER MENU</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i> Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}" href="{{ route('backup.index') }}">
                    <i class="bi bi-cloud-download"></i> Backup
                </a>
            </li>
            @endrole
            
            @role('kasir')
            <li class="nav-item mt-3">
                <small class="text-muted px-3">KASIR MENU</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}">
                    <i class="bi bi-cart"></i> Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                    <i class="bi bi-receipt"></i> Riwayat Transaksi
                </a>
            </li>
            @endrole
            
            @role('admin_gudang')
            <li class="nav-item mt-3">
                <small class="text-muted px-3">GUDANG MENU</small>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-box-seam"></i> Stok Obat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-arrow-down-circle"></i> Obat Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-arrow-up-circle"></i> Obat Keluar
                </a>
            </li>
            @endrole
            
            <li class="nav-item mt-3">
                <small class="text-muted px-3">AKUN</small>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.password.*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                    <i class="bi bi-person"></i> Profil / Password
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-link" id="sidebarToggle" aria-label="Toggle sidebar" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 d-none d-md-inline">@yield('page-title', 'Dashboard')</h5>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="d-none d-md-block">
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <small class="text-muted">
                        @if(auth()->user()->hasRole('owner'))
                            <span class="badge bg-primary">Owner</span>
                        @elseif(auth()->user()->hasRole('kasir'))
                            <span class="badge bg-success">Kasir</span>
                        @elseif(auth()->user()->hasRole('admin_gudang'))
                            <span class="badge bg-info">Admin Gudang</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            if (!sidebar || !content) return;

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('active');
                document.body.classList.remove('sidebar-collapsed');
                content.classList.remove('sidebar-collapsed');
            } else {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                content.classList.toggle('sidebar-collapsed');
                document.body.classList.toggle('sidebar-collapsed');
                
                if (isCollapsed) {
                    sidebar.setAttribute('style', 'width: 72px !important; transition: all 0.3s ease;');
                    content.setAttribute('style', 'margin-left: 72px !important; width: calc(100% - 72px) !important; transition: all 0.3s;');
                } else {
                    sidebar.setAttribute('style', 'width: 250px !important; transition: all 0.3s ease;');
                    content.setAttribute('style', 'margin-left: 250px !important; width: calc(100% - 250px) !important; transition: all 0.3s;');
                }
            }
        }

        window.toggleSidebar = toggleSidebar;
    </script>
    
    @stack('scripts')
</body>
</html>
