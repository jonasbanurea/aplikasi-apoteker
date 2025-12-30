<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOpnameApproveRequest;
use App\Http\Requests\StockOpnameStoreRequest;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index', 'show']);
        $this->middleware(['role:owner|admin_gudang'])->only(['create', 'store']);
        $this->middleware(['role:owner'])->only(['approve']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        $opnames = StockOpname::with(['user', 'approver'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest('opname_date')
            ->paginate(15)
            ->withQueryString();

        return view('stock_opnames.index', [
            'opnames' => $opnames,
            'status' => $status,
        ]);
    }

    public function create()
    {
        $batches = StockBatch::with('product')
            ->orderBy('product_id')
            ->orderBy('batch_no')
            ->get();

        return view('stock_opnames.create', compact('batches'));
    }

    public function store(StockOpnameStoreRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $threshold = (float) config('stock.adjustment_owner_threshold', 0);

        try {
            $opname = DB::transaction(function () use ($data, $user, $threshold) {
                $batchIds = collect($data['items'])->pluck('batch_id');
                if ($batchIds->count() !== $batchIds->unique()->count()) {
                    throw new RuntimeException('Batch tidak boleh duplikat dalam satu opname.');
                }

                $batches = StockBatch::with('product')
                    ->whereIn('id', $batchIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($batches->count() !== $batchIds->count()) {
                    throw new RuntimeException('Batch tidak ditemukan.');
                }

                $itemsPayload = [];
                $totalSystemValue = 0.0;
                $totalPhysicalValue = 0.0;
                $totalDiffValue = 0.0;
                $totalDiffQty = 0;

                foreach ($data['items'] as $item) {
                    $batch = $batches[$item['batch_id']] ?? null;
                    if (!$batch) {
                        throw new RuntimeException('Batch tidak ditemukan.');
                    }

                    $systemQty = (int) $batch->qty_on_hand;
                    $physicalQty = (int) $item['physical_qty'];
                    $diffQty = $physicalQty - $systemQty;
                    $unitCost = (float) $batch->cost_price;
                    $diffValue = $diffQty * $unitCost;

                    $itemsPayload[] = [
                        'batch' => $batch,
                        'batch_id' => $batch->id,
                        'product_id' => $batch->product_id,
                        'system_qty' => $systemQty,
                        'physical_qty' => $physicalQty,
                        'diff_qty' => $diffQty,
                        'reason' => $item['reason'],
                        'unit_cost' => $unitCost,
                        'diff_value' => $diffValue,
                        'notes' => $item['notes'] ?? null,
                    ];

                    $totalSystemValue += $systemQty * $unitCost;
                    $totalPhysicalValue += $physicalQty * $unitCost;
                    $totalDiffValue += $diffValue;
                    $totalDiffQty += $diffQty;
                }

                $requiresApproval = $threshold > 0 && abs($totalDiffValue) >= $threshold;
                $status = $requiresApproval ? StockOpname::STATUS_PENDING : StockOpname::STATUS_APPROVED;

                $opname = StockOpname::create([
                    'user_id' => $user->id,
                    'opname_date' => $data['opname_date'],
                    'status' => $status,
                    'requires_approval' => $requiresApproval,
                    'approval_threshold_value' => $threshold,
                    'total_items' => count($itemsPayload),
                    'total_diff_qty' => $totalDiffQty,
                    'total_system_value' => $totalSystemValue,
                    'total_physical_value' => $totalPhysicalValue,
                    'total_diff_value' => $totalDiffValue,
                    'notes' => $data['notes'] ?? null,
                    'approved_by' => $status === StockOpname::STATUS_APPROVED ? $user->id : null,
                    'approved_at' => $status === StockOpname::STATUS_APPROVED ? now() : null,
                    'approval_notes' => $status === StockOpname::STATUS_APPROVED ? 'Auto approve di bawah threshold' : null,
                ]);

                foreach ($itemsPayload as $payload) {
                    StockOpnameItem::create([
                        'stock_opname_id' => $opname->id,
                        'product_id' => $payload['product_id'],
                        'batch_id' => $payload['batch_id'],
                        'system_qty' => $payload['system_qty'],
                        'physical_qty' => $payload['physical_qty'],
                        'diff_qty' => $payload['diff_qty'],
                        'reason' => $payload['reason'],
                        'unit_cost' => $payload['unit_cost'],
                        'diff_value' => $payload['diff_value'],
                        'notes' => $payload['notes'],
                    ]);
                }

                if ($status === StockOpname::STATUS_APPROVED) {
                    $this->applyAdjustments($opname, $itemsPayload, $user->id);
                }

                return $opname;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $message = $opname->status === StockOpname::STATUS_PENDING
            ? 'Opname disimpan dan menunggu persetujuan owner.'
            : 'Opname disimpan dan stok sudah disesuaikan.';

        return redirect()->route('stock-opnames.show', $opname)->with('success', $message);
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['user', 'approver', 'items.product', 'items.batch']);

        return view('stock_opnames.show', compact('stockOpname'));
    }

    public function approve(StockOpnameApproveRequest $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status !== StockOpname::STATUS_PENDING) {
            return back()->with('error', 'Opname sudah diproses.');
        }

        $data = $request->validated();
        $userId = $request->user()->id;

        try {
            DB::transaction(function () use ($data, $stockOpname, $userId) {
                $stockOpname->load('items');

                if ($data['action'] === 'reject') {
                    $stockOpname->update([
                        'status' => StockOpname::STATUS_REJECTED,
                        'approved_by' => $userId,
                        'approved_at' => now(),
                        'approval_notes' => $data['approval_notes'] ?? null,
                    ]);

                    return;
                }

                $itemsPayload = $stockOpname->items->map(function ($item) {
                    return [
                        'batch_id' => $item->batch_id,
                        'product_id' => $item->product_id,
                        'diff_qty' => $item->diff_qty,
                        'reason' => $item->reason,
                    ];
                })->values()->all();

                $stockOpname->update([
                    'status' => StockOpname::STATUS_APPROVED,
                    'approved_by' => $userId,
                    'approved_at' => now(),
                    'approval_notes' => $data['approval_notes'] ?? null,
                ]);

                $this->applyAdjustments($stockOpname, $itemsPayload, $userId);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('stock-opnames.show', $stockOpname)->with('success', 'Opname sudah disetujui dan stok disesuaikan.');
    }

    /**
     * @param array<int, array{batch?: \App\Models\StockBatch, batch_id:int|null, product_id:int, diff_qty:int, reason?:string}> $itemsPayload
     */
    protected function applyAdjustments(StockOpname $opname, array $itemsPayload, int $actorId): void
    {
        $batchIds = collect($itemsPayload)->pluck('batch_id')->filter()->unique();
        $batches = StockBatch::whereIn('id', $batchIds)->lockForUpdate()->get()->keyBy('id');

        foreach ($itemsPayload as $payload) {
            $diffQty = (int) ($payload['diff_qty'] ?? 0);
            if ($diffQty === 0) {
                continue;
            }

            $batchId = $payload['batch_id'] ?? null;
            $batch = $batchId ? ($batches[$batchId] ?? null) : null;

            if ($batch) {
                $newQty = $batch->qty_on_hand + $diffQty;
                if ($newQty < 0) {
                    throw new RuntimeException('Qty batch ' . $batch->batch_no . ' tidak boleh negatif.');
                }

                $batch->update(['qty_on_hand' => $newQty]);
            }

            StockMovement::create([
                'type' => 'ADJUST',
                'batch_id' => $batchId,
                'product_id' => $payload['product_id'],
                'qty' => $diffQty,
                'ref_type' => 'STOCK_OPNAME',
                'ref_id' => $opname->id,
                'user_id' => $actorId,
                'notes' => 'Opname ' . $opname->id . ($payload['reason'] ? ' (' . $payload['reason'] . ')' : ''),
            ]);
        }
    }
}
