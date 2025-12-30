<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

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

    private function mapData(ProductRequest $request): array
    {
        $data = $request->validated();
        $data['wajib_resep'] = $request->boolean('wajib_resep');
        $data['konsinyasi'] = $request->boolean('konsinyasi');

        return $data;
    }
}
