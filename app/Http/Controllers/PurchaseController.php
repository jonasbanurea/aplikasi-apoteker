<?php

namespace App\Http\Controllers;

use App\Exports\PurchasesExport;
use App\Exports\PurchaseItemsExport;
use App\Http\Requests\PurchaseStoreRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index', 'show']);
        $this->middleware(['role:owner|admin_gudang'])->only(['create', 'store', 'edit', 'update']);
    }

    public function index()
    {
        $purchases = Purchase::with('supplier')
            ->latest('date')
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('nama_dagang')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(PurchaseStoreRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $supplier = Supplier::findOrFail($data['supplier_id']);
            $productMap = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $date = Carbon::parse($data['date']);
            $discount = isset($data['discount']) ? (float) $data['discount'] : 0;

            $itemsPayload = collect($data['items'])->map(function ($item) use ($productMap) {
                $bonus = isset($item['bonus_qty']) ? (int) $item['bonus_qty'] : 0;
                $qty = (int) $item['qty'];
                $costPrice = (float) $item['cost_price'];

                return [
                    'product' => $productMap[$item['product_id']] ?? null,
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'] ?? null,
                    'qty' => $qty,
                    'bonus_qty' => $bonus,
                    'cost_price' => $costPrice,
                    'line_total' => ($qty + $bonus) * $costPrice,
                ];
            });

            $isConsignment = (bool) ($data['is_consignment'] ?? false);
            if (!$isConsignment) {
                $isConsignment = $itemsPayload->contains(fn($item) => $item['product']?->konsinyasi);
            }

            $subtotal = $itemsPayload->sum('line_total');
            $total = max(0, $subtotal - $discount);

            $dueDate = $isConsignment
                ? null
                : ($supplier->payment_term_days ? $date->copy()->addDays($supplier->payment_term_days) : $date);

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'invoice_no' => $data['invoice_no'],
                'date' => $date,
                'discount' => $discount,
                'total' => $total,
                'due_date' => $dueDate,
                'status' => $isConsignment ? Purchase::STATUS_CONSIGNMENT : Purchase::STATUS_POSTED,
                'is_consignment' => $isConsignment,
            ]);

            foreach ($itemsPayload as $item) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'],
                    'qty' => $item['qty'],
                    'bonus_qty' => $item['bonus_qty'],
                    'cost_price' => $item['cost_price'],
                ]);

                $totalQty = $purchaseItem->qty + $purchaseItem->bonus_qty;

                $batch = StockBatch::where('product_id', $purchaseItem->product_id)
                    ->where('batch_no', $purchaseItem->batch_no)
                    ->lockForUpdate()
                    ->first();

                if (!$batch) {
                    $batch = StockBatch::create([
                        'product_id' => $purchaseItem->product_id,
                        'batch_no' => $purchaseItem->batch_no,
                        'expired_date' => $purchaseItem->expired_date,
                        'qty_on_hand' => 0,
                        'cost_price' => $purchaseItem->cost_price,
                        'received_at' => $date,
                    ]);
                } else {
                    $batch->fill([
                        'expired_date' => $batch->expired_date ?? $purchaseItem->expired_date,
                        'cost_price' => $purchaseItem->cost_price,
                        'received_at' => $batch->received_at ?? $date,
                    ])->save();
                }

                $batch->increment('qty_on_hand', $totalQty);

                StockMovement::create([
                    'type' => 'IN',
                    'batch_id' => $batch->id,
                    'product_id' => $purchaseItem->product_id,
                    'qty' => $totalQty,
                    'ref_type' => 'PURCHASE',
                    'ref_id' => $purchase->id,
                    'user_id' => auth()->id(),
                    'notes' => 'Penerimaan ' . $purchase->invoice_no,
                ]);

                // Auto-update harga produk jika harga beli berbeda
                $this->updateProductPrice($item['product'], $purchaseItem->cost_price);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Penerimaan berhasil disimpan.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('nama_dagang')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(PurchaseStoreRequest $request, Purchase $purchase)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $purchase) {
            // Kembalikan stok dari items lama
            foreach ($purchase->items as $oldItem) {
                $batch = StockBatch::where('product_id', $oldItem->product_id)
                    ->where('batch_no', $oldItem->batch_no)
                    ->lockForUpdate()
                    ->first();

                if ($batch) {
                    $totalQty = $oldItem->qty + $oldItem->bonus_qty;
                    $batch->decrement('qty_on_hand', $totalQty);

                    // Catat movement reversal
                    StockMovement::create([
                        'type' => 'OUT',
                        'batch_id' => $batch->id,
                        'product_id' => $oldItem->product_id,
                        'qty' => $totalQty,
                        'ref_type' => 'PURCHASE_EDIT',
                        'ref_id' => $purchase->id,
                        'user_id' => auth()->id(),
                        'notes' => 'Koreksi penerimaan ' . $purchase->invoice_no . ' (edit)',
                    ]);
                }
            }

            // Hapus items lama
            $purchase->items()->delete();

            // Update header purchase
            $supplier = Supplier::findOrFail($data['supplier_id']);
            $productMap = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $date = Carbon::parse($data['date']);
            $discount = isset($data['discount']) ? (float) $data['discount'] : 0;

            $itemsPayload = collect($data['items'])->map(function ($item) use ($productMap) {
                $bonus = isset($item['bonus_qty']) ? (int) $item['bonus_qty'] : 0;
                $qty = (int) $item['qty'];
                $costPrice = (float) $item['cost_price'];

                return [
                    'product' => $productMap[$item['product_id']] ?? null,
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'] ?? null,
                    'qty' => $qty,
                    'bonus_qty' => $bonus,
                    'cost_price' => $costPrice,
                    'line_total' => ($qty + $bonus) * $costPrice,
                ];
            });

            $isConsignment = (bool) ($data['is_consignment'] ?? false);
            if (!$isConsignment) {
                $isConsignment = $itemsPayload->contains(fn($item) => $item['product']?->konsinyasi);
            }

            $subtotal = $itemsPayload->sum('line_total');
            $total = max(0, $subtotal - $discount);

            $dueDate = $isConsignment
                ? null
                : ($supplier->payment_term_days ? $date->copy()->addDays($supplier->payment_term_days) : $date);

            $purchase->update([
                'supplier_id' => $supplier->id,
                'invoice_no' => $data['invoice_no'],
                'date' => $date,
                'discount' => $discount,
                'total' => $total,
                'due_date' => $dueDate,
                'status' => $isConsignment ? Purchase::STATUS_CONSIGNMENT : Purchase::STATUS_POSTED,
                'is_consignment' => $isConsignment,
            ]);

            // Tambahkan items baru
            foreach ($itemsPayload as $item) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'batch_no' => $item['batch_no'],
                    'expired_date' => $item['expired_date'],
                    'qty' => $item['qty'],
                    'bonus_qty' => $item['bonus_qty'],
                    'cost_price' => $item['cost_price'],
                ]);

                $totalQty = $purchaseItem->qty + $purchaseItem->bonus_qty;

                $batch = StockBatch::where('product_id', $purchaseItem->product_id)
                    ->where('batch_no', $purchaseItem->batch_no)
                    ->lockForUpdate()
                    ->first();

                if (!$batch) {
                    $batch = StockBatch::create([
                        'product_id' => $purchaseItem->product_id,
                        'batch_no' => $purchaseItem->batch_no,
                        'expired_date' => $purchaseItem->expired_date,
                        'qty_on_hand' => 0,
                        'cost_price' => $purchaseItem->cost_price,
                        'received_at' => $date,
                    ]);
                } else {
                    $batch->fill([
                        'expired_date' => $batch->expired_date ?? $purchaseItem->expired_date,
                        'cost_price' => $purchaseItem->cost_price,
                        'received_at' => $batch->received_at ?? $date,
                    ])->save();
                }

                $batch->increment('qty_on_hand', $totalQty);

                StockMovement::create([
                    'type' => 'IN',
                    'batch_id' => $batch->id,
                    'product_id' => $purchaseItem->product_id,
                    'qty' => $totalQty,
                    'ref_type' => 'PURCHASE',
                    'ref_id' => $purchase->id,
                    'user_id' => auth()->id(),
                    'notes' => 'Penerimaan ' . $purchase->invoice_no . ' (updated)',
                ]);

                // Auto-update harga produk jika harga beli berbeda
                $this->updateProductPrice($item['product'], $purchaseItem->cost_price);
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Penerimaan berhasil diupdate.');
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'summary'); // summary atau detail

        if ($type === 'detail') {
            $filename = 'purchase-items-detail-' . date('Y-m-d') . '.xlsx';
            return Excel::download(new PurchaseItemsExport($startDate, $endDate), $filename);
        }

        $filename = 'purchases-summary-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new PurchasesExport($startDate, $endDate), $filename);
    }

    /**
     * Auto-update harga produk berdasarkan harga beli batch terbaru
     * 
     * @param Product $product
     * @param float $newCostPrice
     * @return void
     */
    private function updateProductPrice(Product $product, float $newCostPrice)
    {
        // Cek apakah fitur auto-update aktif
        if (!config('pricing.auto_update_price_on_purchase', true)) {
            return;
        }

        $oldCostPrice = (float) $product->harga_beli;
        
        // Cek threshold: hanya update jika selisih signifikan
        $threshold = config('pricing.price_update_threshold_percentage', 0.02); // 2%
        if ($oldCostPrice > 0) {
            $diffPercentage = abs($newCostPrice - $oldCostPrice) / $oldCostPrice;
            if ($diffPercentage < $threshold) {
                // Selisih terlalu kecil, skip update
                return;
            }
        }

        // Ambil margin sesuai golongan produk
        $marginByGolongan = config('pricing.margin_by_golongan', []);
        $margin = $marginByGolongan[$product->golongan] ?? config('pricing.default_margin_percentage', 0.20);

        // Hitung harga jual baru
        $newSellingPrice = $newCostPrice * (1 + $margin);

        // Pembulatan harga jual
        $roundTo = config('pricing.round_selling_price_to', 100);
        if ($roundTo > 1) {
            $newSellingPrice = ceil($newSellingPrice / $roundTo) * $roundTo;
        }

        // Update produk
        $product->update([
            'harga_beli' => $newCostPrice,
            'harga_jual' => $newSellingPrice,
        ]);

        // Log perubahan (opsional, bisa dihapus jika tidak perlu)
        \Log::info('Auto-update harga produk', [
            'product_id' => $product->id,
            'sku' => $product->sku,
            'nama' => $product->nama_dagang,
            'old_cost' => $oldCostPrice,
            'new_cost' => $newCostPrice,
            'new_selling' => $newSellingPrice,
            'margin' => ($margin * 100) . '%',
        ]);
    }
}
