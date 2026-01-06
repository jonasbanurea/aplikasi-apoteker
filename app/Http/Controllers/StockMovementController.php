<?php

namespace App\Http\Controllers;

use App\Exports\StockMovementsExport;
use App\Http\Requests\StockMovementRequest;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index']);
        $this->middleware(['role:owner|admin_gudang'])->only(['store']);
    }

    public function index(Request $request)
    {
        $productId = $request->get('product_id');
        $batchId = $request->get('batch_id');
        $type = $request->get('type');

        $movements = StockMovement::with(['product', 'batch', 'user'])
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->when($batchId, fn($q) => $q->where('batch_id', $batchId))
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $products = Product::orderBy('nama_dagang')->get(['id', 'nama_dagang', 'sku']);
        $batches = StockBatch::orderBy('batch_no')->get(['id', 'batch_no', 'product_id']);

        return view('stocks.movements.index', compact('movements', 'products', 'batches', 'productId', 'batchId', 'type'));
    }

    public function store(StockMovementRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $batch = null;
            if (!empty($data['batch_id'])) {
                $batch = StockBatch::lockForUpdate()->findOrFail($data['batch_id']);
            }

            $productId = $data['product_id'];
            $qty = (int) $data['qty'];

            if ($data['type'] === 'OUT') {
                $qty = abs($qty) * -1;
            }

            if ($data['type'] === 'IN') {
                $qty = abs($qty);
            }

            // ADJUST keeps sign as provided

            $movement = StockMovement::create([
                'type' => $data['type'],
                'batch_id' => $data['batch_id'] ?? null,
                'product_id' => $productId,
                'qty' => $qty,
                'ref_type' => $data['ref_type'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'user_id' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($batch) {
                $newQty = $batch->qty_on_hand + $qty;
                if ($newQty < 0) {
                    throw new \RuntimeException('Qty batch tidak boleh negatif.');
                }
                $batch->update(['qty_on_hand' => $newQty]);
            }

            // If no batch specified, we do not alter batch quantities
            return $movement;
        });

        return redirect()->route('stock-movements.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $productId = $request->input('product_id');

        $filename = 'stock-movements-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new StockMovementsExport($startDate, $endDate, $productId), $filename);
    }
}
