<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir']);
    }

    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('reports.index', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->buildReportData($request);
        $pdf = Pdf::loadView('reports.pdf', $data);
        $filename = 'laporan-apotek-' . $data['filters']['start']->format('Ymd') . '-' . $data['filters']['end']->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function email(Request $request)
    {
        $data = $this->buildReportData($request);
        $pdf = Pdf::loadView('reports.pdf', $data);
        $filename = 'laporan-apotek-' . $data['filters']['start']->format('Ymd') . '-' . $data['filters']['end']->format('Ymd') . '.pdf';
        $recipient = config('reports.email_to', 'dentalcarebest@gmail.com');

        Mail::to($recipient)->send(new ReportMail($data, $pdf->output(), $filename));

        return back()->with('success', 'Laporan telah dikirim ke ' . $recipient);
    }

    protected function buildReportData(Request $request): array
    {
        $filters = $this->resolveFilters($request);
        $start = $filters['start'];
        $end = $filters['end'];

        $salesBase = Sale::whereBetween('sale_date', [$start, $end])
            ->where('is_cancelled', false);

        $salesByPeriod = $this->groupSalesByPeriod($filters['group_by'], clone $salesBase);
        $salesPerCashier = (clone $salesBase)
            ->select('user_id', DB::raw('COUNT(*) as total_tx'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        $salesPerItem = SaleItem::select(
                'sale_items.product_id',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.line_total) as revenue')
            )
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.is_cancelled', false)
            ->with('product:id,sku,nama_dagang,golongan')
            ->groupBy('sale_items.product_id')
            ->orderByDesc('revenue')
            ->limit(25)
            ->get();

        $salesPerGolongan = SaleItem::select(
                'products.golongan',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.line_total) as revenue')
            )
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.is_cancelled', false)
            ->groupBy('products.golongan')
            ->orderByDesc('revenue')
            ->get();

        $weeklyPayment = Sale::select('payment_method', DB::raw('SUM(total) as total_amount'))
            ->where('sale_date', '>=', now()->subDays(7))
            ->where('is_cancelled', false)
            ->groupBy('payment_method')
            ->get();

        $grossProfit = $this->grossProfit($start, $end);

        $purchasesBySupplier = Purchase::select(
                'supplier_id',
                DB::raw('COUNT(*) as total_po'),
                DB::raw('SUM(total) as total_amount')
            )
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('supplier:id,name')
            ->groupBy('supplier_id')
            ->orderByDesc('total_amount')
            ->get();

        $dueSoon = Purchase::with('supplier:id,name')
            ->where('status', Purchase::STATUS_POSTED)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(14)->toDateString()])
            ->orderBy('due_date')
            ->get();

        $stockSnapshot = StockBatch::with('product:id,sku,nama_dagang')
            ->orderBy('product_id')
            ->orderBy('expired_date')
            ->limit(50)
            ->get();

        $nearExpired = StockBatch::with('product:id,sku,nama_dagang')
            ->whereNotNull('expired_date')
            ->whereBetween('expired_date', [$filters['today'], $filters['near_until']])
            ->orderBy('expired_date')
            ->get();

        $expired = StockBatch::with('product:id,sku,nama_dagang')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<', $filters['today'])
            ->orderBy('expired_date')
            ->get();

        $nearExpiredLoss = $nearExpired->sum(fn($b) => $b->qty_on_hand * $b->cost_price);
        $expiredLoss = $expired->sum(fn($b) => $b->qty_on_hand * $b->cost_price);

        $reorder = $this->reorderList();

        $meta = [
            'total_sales' => (clone $salesBase)->sum('total'),
            'total_transactions' => (clone $salesBase)->count(),
            'generated_at' => now(),
        ];

        return compact(
            'filters',
            'meta',
            'salesByPeriod',
            'salesPerCashier',
            'salesPerItem',
            'salesPerGolongan',
            'weeklyPayment',
            'grossProfit',
            'purchasesBySupplier',
            'dueSoon',
            'stockSnapshot',
            'nearExpired',
            'expired',
            'nearExpiredLoss',
            'expiredLoss',
            'reorder'
        );
    }

    protected function resolveFilters(Request $request): array
    {
        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        $groupBy = $request->get('group_by', 'daily');

        $choices = config('stock.near_expired_days', [90, 60, 30]);
        $defaultNear = (int) config('stock.default_near_expired_days', $choices[0] ?? 90);
        $nearDays = (int) ($request->get('near_days') ?: $defaultNear);
        if (!in_array($nearDays, $choices, true)) {
            $nearDays = $defaultNear;
        }

        return [
            'start' => $start,
            'end' => $end,
            'group_by' => $groupBy,
            'near_days' => $nearDays,
            'today' => now()->startOfDay(),
            'near_until' => now()->startOfDay()->addDays($nearDays),
            'near_choices' => $choices,
        ];
    }

    protected function groupSalesByPeriod(string $groupBy, $salesBase)
    {
        $format = match ($groupBy) {
            'weekly' => '%x-W%v',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return $salesBase
            ->select(
                DB::raw("DATE_FORMAT(sale_date, '{$format}') as label"),
                DB::raw('SUM(total) as total_amount'),
                DB::raw('COUNT(*) as total_tx')
            )
            ->groupBy('label')
            ->orderBy('label')
            ->get();
    }

    protected function grossProfit(Carbon $start, Carbon $end)
    {
        return DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('sale_item_batches', 'sale_item_batches.sale_item_id', '=', 'sale_items.id')
            ->leftJoin('stock_batches', 'stock_batches.id', '=', 'sale_item_batches.stock_batch_id')
            ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.is_cancelled', false)
            ->groupBy('sale_items.product_id', 'products.nama_dagang', 'products.sku')
            ->select(
                'sale_items.product_id',
                'products.nama_dagang',
                'products.sku',
                DB::raw('SUM(sale_items.qty) as qty'),
                DB::raw('SUM(sale_items.line_total) as revenue'),
                DB::raw('SUM(sale_item_batches.qty * COALESCE(stock_batches.cost_price, products.harga_beli, 0)) as cost')
            )
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($row) {
                $row->cost = (float) $row->cost;
                $row->revenue = (float) $row->revenue;
                $row->margin = $row->revenue - $row->cost;
                return $row;
            });
    }

    protected function reorderList()
    {
        $lookback = (int) config('stock.reorder_sales_lookback_days', 30);

        $sales30 = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.sale_date', '>=', now()->subDays($lookback))
            ->where('sales.is_cancelled', false)
            ->select('sale_items.product_id', DB::raw('SUM(sale_items.qty) as qty30'))
            ->groupBy('sale_items.product_id')
            ->get()
            ->keyBy('product_id');

        return Product::select(
                'products.id',
                'products.sku',
                'products.nama_dagang',
                'products.satuan',
                'products.golongan',
                'products.harga_jual',
                'products.minimal_stok',
                DB::raw('COALESCE(SUM(stock_batches.qty_on_hand), 0) as qty_on_hand')
            )
            ->leftJoin('stock_batches', 'stock_batches.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.sku', 'products.nama_dagang', 'products.satuan', 'products.golongan', 'products.harga_jual', 'products.minimal_stok')
            ->havingRaw('products.minimal_stok > 0')
            ->havingRaw('qty_on_hand < products.minimal_stok')
            ->orderBy('qty_on_hand')
            ->get()
            ->map(function ($product) use ($sales30, $lookback) {
                $qty30 = (int) ($sales30[$product->id]->qty30 ?? 0);
                $product->qty_on_hand = (int) $product->qty_on_hand;
                $product->avg_daily_sold = $lookback > 0 ? round($qty30 / $lookback, 2) : 0;
                $product->reorder_need = max(0, $product->minimal_stok - $product->qty_on_hand);
                return $product;
            });
    }
}
