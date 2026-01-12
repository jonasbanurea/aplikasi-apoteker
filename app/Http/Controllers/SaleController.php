<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Http\Requests\SaleStoreRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemBatch;
use App\Models\StockMovement;
use App\Services\FefoAllocatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;

class SaleController extends Controller
{
    public function __construct(protected FefoAllocatorService $fefoAllocator)
    {
        $this->middleware(['role:owner|admin_gudang|kasir']);
        $this->middleware('shift.open')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = Sale::with('user')->latest('sale_date');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $sales = $query->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filename = 'sales-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new SalesExport($startDate, $endDate), $filename);
    }

    public function create()
    {
        $products = Product::orderBy('nama_dagang')->get();

        return view('sales.create', compact('products'));
    }

    public function store(SaleStoreRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;
        $openShift = $request->attributes->get('open_shift');
        if (!$openShift) {
            return redirect()->route('shifts.create')->with('error', 'Buka shift terlebih dahulu.');
        }

        try {
            $sale = DB::transaction(function () use ($data, $userId, $openShift) {
                $productMap = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                    ->get()
                    ->keyBy('id');

                $itemsPayload = collect($data['items'])->map(function ($item) use ($productMap) {
                    $product = $productMap[$item['product_id']] ?? null;
                    $qty = (int) $item['qty'];
                    $price = (float) $item['price'];
                    $discount = isset($item['discount']) ? (float) $item['discount'] : 0;
                    $netPrice = max(0, $price - $discount);

                    return [
                        'product' => $product,
                        'product_id' => $item['product_id'],
                        'product_name' => $product?->nama_dagang ?? '',
                        'qty' => $qty,
                        'price' => $price,
                        'discount' => $discount,
                        'net_price' => $netPrice,
                        'line_total' => $netPrice * $qty,
                    ];
                })->values();

                $discountTotal = isset($data['discount_total']) ? (float) $data['discount_total'] : 0;
                $subtotal = $itemsPayload->sum('line_total');
                $total = max(0, $subtotal - $discountTotal);

                $paymentMethod = $data['payment_method'];
                $paidAmount = $paymentMethod === Sale::METHOD_NON_CASH
                    ? $total
                    : (isset($data['paid_amount']) ? (float) $data['paid_amount'] : $total);
                $changeAmount = max(0, $paidAmount - $total);

                $allocations = $this->fefoAllocator->allocate(
                    $itemsPayload->map(fn($item) => [
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'product_name' => $item['product_name'],
                    ])->values()->all()
                );

                $sale = Sale::create([
                    'invoice_no' => $this->generateInvoiceNo(),
                    'user_id' => $userId,
                    'shift_id' => $openShift?->id,
                    'sale_date' => Carbon::now(),
                    'payment_method' => $paymentMethod,
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'no_resep' => $data['no_resep'] ?? null,
                    'dokter' => $data['dokter'] ?? null,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                foreach ($itemsPayload as $index => $item) {
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'line_total' => $item['line_total'],
                    ]);

                    foreach ($allocations[$index] as $alloc) {
                        $batch = $alloc['batch'];
                        $allocQty = $alloc['qty'];

                        SaleItemBatch::create([
                            'sale_item_id' => $saleItem->id,
                            'stock_batch_id' => $batch->id,
                            'qty' => $allocQty,
                        ]);

                        $batch->decrement('qty_on_hand', $allocQty);

                        StockMovement::create([
                            'type' => 'OUT',
                            'batch_id' => $batch->id,
                            'product_id' => $saleItem->product_id,
                            'qty' => -$allocQty,
                            'ref_type' => 'SALE',
                            'ref_id' => $sale->id,
                            'user_id' => $userId,
                            'notes' => 'Penjualan ' . $sale->invoice_no,
                        ]);
                    }
                }

                return $sale;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product', 'items.batches.stockBatch.product']);

        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['user', 'items.product', 'items.batches.stockBatch']);

        return view('sales.print', compact('sale'));
    }

    public function printThermal(Sale $sale)
    {
        $sale->load(['user', 'items.product', 'items.batches.stockBatch']);

        // Generate ESC/POS raw data
        $escpos = $this->generateEscPosReceipt($sale);

        // Return as downloadable text file yang bisa dikirim langsung ke printer
        return response($escpos)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="struk-' . $sale->invoice_no . '.txt"');
    }

    protected function generateEscPosReceipt(Sale $sale): string
    {
        $output = '';
        
        // ESC/POS Commands
        $ESC = "\x1B";
        $GS = "\x1D";
        
        // Initialize printer
        $output .= $ESC . "@"; // Initialize
        
        // Center align + Bold + Store name
        $output .= $ESC . "a" . chr(1); // Center
        $output .= $ESC . "E" . chr(1); // Bold ON
        $output .= config('app.name') . "\n";
        $output .= $ESC . "E" . chr(0); // Bold OFF
        
        // Invoice info
        $output .= "Invoice: " . $sale->invoice_no . "\n";
        $output .= "Kasir: " . ($sale->user?->name ?? '-') . "\n";
        $output .= $sale->sale_date?->format('d/m/Y H:i') . "\n";
        
        // Separator line
        $output .= $ESC . "a" . chr(0); // Left align
        $output .= str_repeat("-", 32) . "\n";
        
        // Items
        foreach ($sale->items as $item) {
            $productName = $item->product->nama_dagang ?? '-';
            // Truncate if too long
            if (strlen($productName) > 32) {
                $productName = substr($productName, 0, 29) . '...';
            }
            $output .= $productName . "\n";
            
            $qty = $item->qty;
            $price = number_format($item->price - $item->discount, 0, ',', '.');
            $lineTotal = number_format($item->line_total, 0, ',', '.');
            
            $qtyPrice = $qty . "x" . $price;
            $spaces = 32 - strlen($qtyPrice) - strlen($lineTotal);
            $output .= $qtyPrice . str_repeat(' ', $spaces) . $lineTotal . "\n";
        }
        
        // Separator
        $output .= str_repeat("-", 32) . "\n";
        
        // Totals
        $total = "TOTAL";
        $totalAmount = number_format($sale->total, 0, ',', '.');
        $spaces = 32 - strlen($total) - strlen($totalAmount);
        $output .= $ESC . "E" . chr(1); // Bold ON
        $output .= $total . str_repeat(' ', $spaces) . $totalAmount . "\n";
        $output .= $ESC . "E" . chr(0); // Bold OFF
        
        $paid = "Bayar(" . $sale->payment_method . ")";
        $paidAmount = number_format($sale->paid_amount, 0, ',', '.');
        $spaces = 32 - strlen($paid) - strlen($paidAmount);
        $output .= $paid . str_repeat(' ', $spaces) . $paidAmount . "\n";
        
        $change = "Kembali";
        $changeAmount = number_format($sale->change_amount, 0, ',', '.');
        $spaces = 32 - strlen($change) - strlen($changeAmount);
        $output .= $change . str_repeat(' ', $spaces) . $changeAmount . "\n";
        
        // Footer
        $output .= str_repeat("-", 32) . "\n";
        $output .= $ESC . "a" . chr(1); // Center
        $output .= "Terima Kasih\n";
        
        // Feed and cut
        $output .= "\n\n\n";
        $output .= $GS . "V" . chr(1); // Partial cut
        
        return $output;
    }

    protected function generateInvoiceNo(): string
    {
        $now = Carbon::now()->format('YmdHis');
        $rand = random_int(100, 999);

        return 'POS-' . $now . '-' . $rand;
    }

    public function cancel(Request $request, Sale $sale)
    {
        // Validasi hanya owner yang bisa cancel
        if (!$request->user()->hasRole('owner')) {
            return back()->with('error', 'Hanya owner yang dapat membatalkan transaksi.');
        }

        // Cek apakah sudah dibatalkan
        if ($sale->is_cancelled) {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        try {
            DB::transaction(function () use ($sale, $request) {
                // Load items dengan batches
                $sale->load(['items.batches.stockBatch']);

                foreach ($sale->items as $item) {
                    foreach ($item->batches as $saleItemBatch) {
                        $batch = $saleItemBatch->stockBatch;
                        $qty = $saleItemBatch->qty;

                        // Kembalikan stok ke batch
                        $batch->increment('qty_on_hand', $qty);

                        // Buat stock movement reversal
                        StockMovement::create([
                            'type' => 'IN',
                            'batch_id' => $batch->id,
                            'product_id' => $item->product_id,
                            'qty' => $qty,
                            'ref_type' => 'SALE_CANCEL',
                            'ref_id' => $sale->id,
                            'user_id' => $request->user()->id,
                            'notes' => 'Pembatalan penjualan ' . $sale->invoice_no,
                        ]);
                    }
                }

                // Update status sale menjadi cancelled
                $sale->update([
                    'is_cancelled' => true,
                    'cancelled_at' => Carbon::now(),
                    'cancelled_by' => $request->user()->id,
                    'cancel_reason' => $request->input('cancel_reason', 'Transaksi dibatalkan oleh owner'),
                ]);
            });

            return back()->with('success', 'Transaksi berhasil dibatalkan. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
