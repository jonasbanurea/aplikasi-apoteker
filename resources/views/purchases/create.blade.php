@extends('layouts.admin')

@section('title', 'Penerimaan Baru')
@section('page-title', 'Penerimaan Baru')

@section('content')
@php
    $oldItems = old('items', [[
        'product_id' => '',
        'batch_no' => '',
        'expired_date' => '',
        'qty' => '',
        'bonus_qty' => 0,
        'cost_price' => '',
    ]]);
@endphp
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('purchases.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('supplier_id') ? '' : 'selected' }}>Pilih supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror" value="{{ old('invoice_no') }}" required>
                    @error('invoice_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Diskon (Rp)</label>
                    <input type="number" step="0.01" min="0" name="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', 0) }}">
                    @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="isConsignment" name="is_consignment" {{ old('is_consignment') ? 'checked' : '' }}>
                        <label class="form-check-label" for="isConsignment">
                            Tandai sebagai penerimaan konsinyasi (akan otomatis aktif jika ada produk konsinyasi)
                        </label>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">Detail Barang</h6>
                    <small class="text-muted">Bonus ikut menambah stok batch</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow"><i class="bi bi-plus-circle"></i> Tambah Baris</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 22%">Produk</th>
                            <th style="width: 13%">Batch</th>
                            <th style="width: 13%">Expired</th>
                            <th style="width: 10%" class="text-end">Qty</th>
                            <th style="width: 10%" class="text-end">Bonus</th>
                            <th style="width: 15%" class="text-end">Harga Beli/item</th>
                            <th style="width: 7%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oldItems as $index => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $index }}][product_id]" class="form-select" required>
                                    <option value="" disabled {{ empty($item['product_id']) ? 'selected' : '' }}>Pilih produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->sku }} - {{ $product->nama_dagang }}
                                            @if($product->konsinyasi)
                                                (Konsinyasi)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][batch_no]" class="form-control" value="{{ $item['batch_no'] }}" required>
                            </td>
                            <td>
                                <input type="date" name="items[{{ $index }}][expired_date]" class="form-control" value="{{ $item['expired_date'] }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" class="form-control text-end" min="1" value="{{ $item['qty'] }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][bonus_qty]" class="form-control text-end" min="0" value="{{ $item['bonus_qty'] }}">
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][cost_price]" class="form-control text-end" value="{{ $item['cost_price'] }}" required>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm removeRow" aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @error('items')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const itemsTableBody = document.querySelector('#itemsTable tbody');
    const addItemBtn = document.getElementById('addItemRow');
    const productsOptions = `@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->sku }} - {{ $product->nama_dagang }}@if($product->konsinyasi) (Konsinyasi)@endif</option>@endforeach`;

    function addRow() {
        const index = itemsTableBody.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${index}][product_id]" class="form-select" required>
                    <option value="" disabled selected>Pilih produk</option>
                    ${productsOptions}
                </select>
            </td>
            <td><input type="text" name="items[${index}][batch_no]" class="form-control" required></td>
            <td><input type="date" name="items[${index}][expired_date]" class="form-control" required></td>
            <td><input type="number" name="items[${index}][qty]" class="form-control text-end" min="1" required></td>
            <td><input type="number" name="items[${index}][bonus_qty]" class="form-control text-end" min="0" value="0"></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" min="0" name="items[${index}][cost_price]" class="form-control text-end" required>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm removeRow" aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
            </td>
        `;
        itemsTableBody.appendChild(row);
    }

    addItemBtn?.addEventListener('click', addRow);

    itemsTableBody?.addEventListener('click', function(event) {
        if (event.target.closest('.removeRow')) {
            const row = event.target.closest('tr');
            if (itemsTableBody.querySelectorAll('tr').length > 1) {
                row.remove();
            }
        }
    });
</script>
@endpush
