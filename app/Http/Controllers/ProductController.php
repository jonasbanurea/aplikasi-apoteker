<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index']);
        $this->middleware(['role:owner|admin_gudang'])->except(['index']);
    }

    public function index(Request $request)
    {
        $search = $request->get('q');

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('sku', 'like', "%{$search}%")
                        ->orWhere('nama_dagang', 'like', "%{$search}%")
                        ->orWhere('nama_generik', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_dagang')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        $product = new Product();

        return view('products.create', compact('product'));
    }

    public function store(ProductRequest $request)
    {
        $data = $this->mapData($request);

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil disimpan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $this->mapData($request);

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(
            new ProductsExport,
            'daftar-obat-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    public function lowStock(Request $request)
    {
        $search = $request->get('q');

        $products = Product::query()
            ->selectRaw('products.*, (SELECT COALESCE(SUM(qty_on_hand), 0) FROM stock_batches WHERE stock_batches.product_id = products.id) as current_stock')
            ->whereRaw('(SELECT COALESCE(SUM(qty_on_hand), 0) FROM stock_batches WHERE stock_batches.product_id = products.id) <= products.minimal_stok')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('sku', 'like', "%{$search}%")
                        ->orWhere('nama_dagang', 'like', "%{$search}%")
                        ->orWhere('nama_generik', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_dagang')
            ->paginate(20)
            ->withQueryString();

        return view('products.low-stock', compact('products', 'search'));
    }

    private function mapData(ProductRequest $request): array
    {
        $data = $request->validated();
        $data['wajib_resep'] = $request->boolean('wajib_resep');
        $data['konsinyasi'] = $request->boolean('konsinyasi');
        $data['jual_eceran'] = $request->boolean('jual_eceran');

        // Jika jual_eceran tidak dicentang, set field terkait ke null
        if (!$data['jual_eceran']) {
            $data['unit_kemasan'] = null;
            $data['unit_terkecil'] = null;
            $data['isi_per_kemasan'] = null;
        }

        return $data;
    }
}
