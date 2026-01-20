<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockBatch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Common data
        $data = $this->getDashboardData();
        
        // Redirect based on role
        if ($user->hasRole('owner')) {
            return view('dashboard.owner', $data);
        } elseif ($user->hasRole('kasir')) {
            return view('dashboard.kasir', $data);
        } elseif ($user->hasRole('admin_gudang')) {
            return view('dashboard.admin_gudang', $data);
        }

        return view('dashboard.index', $data);
    }

    protected function getDashboardData(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        $startOfLastMonth = $lastMonth->copy()->startOfMonth();
        $endOfLastMonth = $lastMonth->copy()->endOfMonth();

        // Sales data (exclude cancelled transactions)
        $totalRevenue = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->where('is_cancelled', false)
            ->sum('total');
        $totalRevenueLastMonth = Sale::whereBetween('sale_date', [$startOfLastMonth, $endOfLastMonth])
            ->where('is_cancelled', false)
            ->sum('total');
        $revenueGrowth = $totalRevenueLastMonth > 0 
            ? (($totalRevenue - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100 
            : 0;

        $todayTransactions = Sale::whereDate('sale_date', $today)
            ->where('is_cancelled', false)
            ->count();
        $todayRevenue = Sale::whereDate('sale_date', $today)
            ->where('is_cancelled', false)
            ->sum('total');
        $todayItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereDate('sales.sale_date', $today)
            ->where('sales.is_cancelled', false)
            ->sum('sale_items.qty');
        $totalTransactions = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->where('is_cancelled', false)
            ->count();

        // Stock data
        $totalProducts = Product::count();
        $totalStock = StockBatch::sum('qty_on_hand');
        $reorderAlerts = Product::whereRaw('(SELECT COALESCE(SUM(qty_on_hand), 0) FROM stock_batches WHERE stock_batches.product_id = products.id) <= products.minimal_stok')
            ->count();

        // Near expired (30 days)
        $nearExpired = StockBatch::whereNotNull('expired_date')
            ->whereBetween('expired_date', [$today, $today->copy()->addDays(30)])
            ->count();

        // Expired
        $expired = StockBatch::whereNotNull('expired_date')
            ->where('expired_date', '<', $today)
            ->count();

        // Users
        $totalUsers = User::where('is_active', true)->count();

        // Due payables (14 days)
        $duePayables = Purchase::where('status', Purchase::STATUS_POSTED)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today, $today->copy()->addDays(14)])
            ->count();

        // Low stock products (top 5)
        $lowStockProducts = Product::select('products.id', 'products.nama_dagang', 'products.sku')
            ->selectRaw('(SELECT COALESCE(SUM(qty_on_hand), 0) FROM stock_batches WHERE stock_batches.product_id = products.id) as stock_on_hand')
            ->whereRaw('(SELECT COALESCE(SUM(qty_on_hand), 0) FROM stock_batches WHERE stock_batches.product_id = products.id) <= products.minimal_stok')
            ->orderBy('stock_on_hand')
            ->limit(5)
            ->get();

        // Recent sales (latest 5, exclude cancelled)
        $recentSales = Sale::with('user:id,name')
            ->where('is_cancelled', false)
            ->latest('sale_date')
            ->limit(5)
            ->get();

        // Sales chart (last 7 days, exclude cancelled)
        $salesChart = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
            ->where('sale_date', '>=', $today->copy()->subDays(6))
            ->where('is_cancelled', false)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->toDateString();
            $chartData[] = [
                'date' => Carbon::parse($date)->format('d M'),
                'count' => $salesChart[$date]->count ?? 0,
                'total' => $salesChart[$date]->total ?? 0,
            ];
        }

        // Top products (this month, exclude cancelled)
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.sale_date', [$startOfMonth, $endOfMonth])
            ->where('sales.is_cancelled', false)
            ->select(
                'products.nama_dagang',
                'products.sku',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.line_total) as total_revenue')
            )
            ->groupBy('products.id', 'products.nama_dagang', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return compact(
            'totalRevenue',
            'revenueGrowth',
            'todayTransactions',
            'totalTransactions',
            'totalProducts',
            'totalStock',
            'reorderAlerts',
            'nearExpired',
            'expired',
            'totalUsers',
            'duePayables',
            'chartData',
            'topProducts',
            'lowStockProducts',
            'todayRevenue',
            'todayItems',
            'recentSales'
        );
    }
}
