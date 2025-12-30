<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierReturnStoreRequest;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierReturn;
use App\Models\SupplierReturnItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index', 'show']);
        $this->middleware(['role:owner|admin_gudang'])->only(['create', 'store']);
    }

    public function index()
    {
        $returns = SupplierReturn::with(['supplier', 'user'])
            ->latest('return_date')
            ->paginate(15);

        return view('supplier_returns.index', compact('returns'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $batches = StockBatch::with('product')
            ->orderBy('product_id')
            ->orderBy('batch_no')
            ->get();

        return view('supplier_returns.create', compact('suppliers', 'batches'));
    }

    public function store(SupplierReturnStoreRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        try {
            $supplierReturn = DB::transaction(function () use ($data, $userId) {
                $batchIds = collect($data['items'])->pluck('batch_id');
                if ($batchIds->count() !== $batchIds->unique()->count()) {
                    throw new RuntimeException('Batch tidak boleh duplikat dalam satu retur.');
                }

                $batches = StockBatch::with('product')
                    ->whereIn('id', $batchIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($batches->count() !== $batchIds->count()) {
                    throw new RuntimeException('Batch tidak ditemukan.');
                }

                $return = SupplierReturn::create([
                    'return_no' => $this->generateReturnNo(),
                    'supplier_id' => $data['supplier_id'],
                    'user_id' => $userId,
                    'return_date' => $data['return_date'],
                    'status' => SupplierReturn::STATUS_POSTED,
                    'total_qty' => 0,
                    'total_value' => 0,
                    'notes' => $data['notes'] ?? null,
                ]);

                $totalQty = 0;
                $totalValue = 0.0;

                foreach ($data['items'] as $item) {
                    $batch = $batches[$item['batch_id']] ?? null;
                    if (!$batch) {
                        throw new RuntimeException('Batch tidak ditemukan.');
                    }

                    $qty = (int) $item['qty'];
                    if ($qty <= 0) {
                        throw new RuntimeException('Qty retur harus lebih besar dari 0.');
                    }

                    if ($batch->qty_on_hand < $qty) {
                        throw new RuntimeException('Qty batch ' . $batch->batch_no . ' tidak mencukupi.');
                    }

                    $unitCost = (float) $batch->cost_price;
                    $subtotal = $qty * $unitCost;

                    SupplierReturnItem::create([
                        'supplier_return_id' => $return->id,
                        'product_id' => $batch->product_id,
                        'stock_batch_id' => $batch->id,
                        'qty' => $qty,
                        'unit_cost' => $unitCost,
                        'subtotal' => $subtotal,
                        'reason' => $item['reason'] ?? null,
                    ]);

                    $batch->update(['qty_on_hand' => $batch->qty_on_hand - $qty]);

                    StockMovement::create([
                        'type' => 'OUT',
                        'batch_id' => $batch->id,
                        'product_id' => $batch->product_id,
                        'qty' => -$qty,
                        'ref_type' => 'SUPPLIER_RETURN',
                        'ref_id' => $return->id,
                        'user_id' => $userId,
                        'notes' => 'Retur supplier ' . $return->return_no,
                    ]);

                    $totalQty += $qty;
                    $totalValue += $subtotal;
                }

                $return->update([
                    'total_qty' => $totalQty,
                    'total_value' => $totalValue,
                ]);

                return $return;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('supplier-returns.show', $supplierReturn)->with('success', 'Retur supplier berhasil dicatat.');
    }

    public function show(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load(['supplier', 'user', 'items.product', 'items.batch']);

        return view('supplier_returns.show', compact('supplierReturn'));
    }

    protected function generateReturnNo(): string
    {
        $now = now()->format('YmdHis');
        $rand = random_int(100, 999);

        return 'RT-' . $now . '-' . $rand;
    }
}
