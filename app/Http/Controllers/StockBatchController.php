<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockBatchRequest;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class StockBatchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index']);
        $this->middleware(['role:owner|admin_gudang'])->except(['index']);
    }

    public function index(Request $request)
    {
        $searchProduct = $request->get('product_id');
        $nearExpired = $request->boolean('near_expired');
        $thresholdDays = 30;

        $batches = StockBatch::with('product')
            ->when($searchProduct, fn($q) => $q->where('product_id', $searchProduct))
            ->when($nearExpired, function ($q) use ($thresholdDays) {
                $q->whereNotNull('expired_date')
                  ->whereBetween('expired_date', [now(), now()->addDays($thresholdDays)]);
            })
            ->orderBy('expired_date')
            ->orderBy('batch_no')
            ->paginate(10)
            ->withQueryString();

        $products = Product::orderBy('nama_dagang')->get(['id', 'nama_dagang', 'sku']);

        return view('stocks.batches.index', compact('batches', 'products', 'searchProduct', 'nearExpired', 'thresholdDays'));
    }

    public function create()
    {
        $products = Product::orderBy('nama_dagang')->get(['id', 'nama_dagang', 'sku']);
        $batch = new StockBatch();

        return view('stocks.batches.create', compact('products', 'batch'));
    }

    public function store(StockBatchRequest $request)
    {
        StockBatch::create($request->validated());

        return redirect()->route('stock-batches.index')->with('success', 'Batch stok berhasil ditambahkan.');
    }

    public function edit(StockBatch $stockBatch)
    {
        $products = Product::orderBy('nama_dagang')->get(['id', 'nama_dagang', 'sku']);
        $batch = $stockBatch;

        return view('stocks.batches.edit', compact('products', 'batch'));
    }

    public function update(StockBatchRequest $request, StockBatch $stockBatch)
    {
        $stockBatch->update($request->validated());

        return redirect()->route('stock-batches.index')->with('success', 'Batch stok berhasil diupdate.');
    }

    public function nearExpiry(Request $request)
    {
        $search = $request->get('q');
        $status = $request->get('status', 'all'); // all, expired, near_expired
        $today = now();
        $thresholdDays = 30;

        $batches = StockBatch::with('product')
            ->whereNotNull('expired_date')
            ->where('qty_on_hand', '>', 0) // Only show batches with stock
            ->when($status === 'expired', function ($q) use ($today) {
                $q->where('expired_date', '<', $today);
            })
            ->when($status === 'near_expired', function ($q) use ($today, $thresholdDays) {
                $q->whereBetween('expired_date', [$today, $today->copy()->addDays($thresholdDays)]);
            })
            ->when($status === 'all', function ($q) use ($today, $thresholdDays) {
                $q->where(function ($sub) use ($today, $thresholdDays) {
                    $sub->where('expired_date', '<', $today)
                        ->orWhereBetween('expired_date', [$today, $today->copy()->addDays($thresholdDays)]);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('product', function ($sub) use ($search) {
                    $sub->where('sku', 'like', "%{$search}%")
                        ->orWhere('nama_dagang', 'like', "%{$search}%")
                        ->orWhere('nama_generik', 'like', "%{$search}%");
                })
                ->orWhere('batch_no', 'like', "%{$search}%");
            })
            ->orderByRaw('CASE WHEN expired_date < NOW() THEN 0 ELSE 1 END')
            ->orderBy('expired_date')
            ->paginate(20)
            ->withQueryString();

        // Count statistics
        $expiredCount = StockBatch::whereNotNull('expired_date')
            ->where('expired_date', '<', $today)
            ->where('qty_on_hand', '>', 0)
            ->count();

        $nearExpiredCount = StockBatch::whereNotNull('expired_date')
            ->whereBetween('expired_date', [$today, $today->copy()->addDays($thresholdDays)])
            ->where('qty_on_hand', '>', 0)
            ->count();

        return view('stocks.batches.near-expiry', compact(
            'batches',
            'search',
            'status',
            'thresholdDays',
            'expiredCount',
            'nearExpiredCount'
        ));
    }
}
