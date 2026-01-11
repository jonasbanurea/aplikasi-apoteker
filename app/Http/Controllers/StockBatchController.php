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
}
