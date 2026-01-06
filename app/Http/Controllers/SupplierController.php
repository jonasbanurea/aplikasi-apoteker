<?php

namespace App\Http\Controllers;

use App\Exports\SuppliersExport;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir'])->only(['index']);
        $this->middleware(['role:owner|admin_gudang'])->except(['index']);
    }

    public function index(Request $request)
    {
        $search = $request->get('q');

        $suppliers = Supplier::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('contact', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        $supplier = new Supplier();

        return view('suppliers.create', compact('supplier'));
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil disimpan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function export()
    {
        $filename = 'suppliers-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new SuppliersExport(), $filename);
    }
}
