@extends('layouts.admin')

@section('title', 'Transaksi POS')
@section('page-title', 'Transaksi POS')

@section('content')
@php
    $oldItems = old('items', [[
        'product_id' => '',
        'qty' => 1,
        'price' => '',
        'discount' => 0,
    ]]);
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">POS Penjualan</h4>
        <small class="text-muted">Search produk (SKU/nama), tambah item, hitung total, dan proses pembayaran.</small>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sales.store') }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" id="paymentMethod" class="form-select @error('payment_method') is-invalid @enderror" required>
                        <option value="CASH" {{ old('payment_method') === 'CASH' ? 'selected' : '' }}>CASH</option>
                        <option value="NON_CASH" {{ old('payment_method') === 'NON_CASH' ? 'selected' : '' }}>NON CASH</option>
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Diskon Total (Rp)</label>
                    <input type="number" step="0.01" min="0" name="discount_total" id="discountTotal" class="form-control @error('discount_total') is-invalid @enderror" value="{{ old('discount_total', 0) }}">
                    @error('discount_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3" id="paidAmountWrap">
                    <label class="form-label">Bayar (CASH)</label>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', 0) }}">
                    @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">No. Resep (opsional)</label>
                    <input type="text" name="no_resep" class="form-control @error('no_resep') is-invalid @enderror" value="{{ old('no_resep') }}" placeholder="No Resep">
                    @error('no_resep')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dokter (opsional)</label>
                    <input type="text" name="dokter" class="form-control @error('dokter') is-invalid @enderror" value="{{ old('dokter') }}" placeholder="Nama Dokter">
                    @error('dokter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-9">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="catatan" class="form-control @error('catatan') is-invalid @enderror" value="{{ old('catatan') }}" placeholder="Catatan resep / pasien">
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Cari Produk (SKU / Nama)</label>
                <div class="input-group">
                    <input list="productSearchList" id="productSearch" class="form-control" placeholder="Ketik SKU atau nama produk">
                    <datalist id="productSearchList">
                        @foreach($products as $product)
                            <option value="{{ $product->sku }} - {{ $product->nama_dagang }}" data-id="{{ $product->id }}" data-price="{{ $product->harga_jual }}"></option>
                        @endforeach
                    </datalist>
                    <button type="button" class="btn btn-outline-primary" id="addFromSearch"><i class="bi bi-plus-circle"></i> Tambah</button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">Item Penjualan</h6>
                    <small class="text-muted">Diskon per item = potongan per unit</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow"><i class="bi bi-plus-circle"></i> Baris Baru</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%">Produk</th>
                            <th style="width: 12%">Unit</th>
                            <th style="width: 8%" class="text-end">Qty</th>
                            <th style="width: 13%" class="text-end">Harga</th>
                            <th style="width: 13%" class="text-end">Diskon/Unit</th>
                            <th style="width: 13%" class="text-end">Subtotal</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oldItems as $index => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $index }}][product_id]" class="form-select product-select" required data-row="{{ $index }}">
                                    <option value="" disabled {{ empty($item['product_id']) ? 'selected' : '' }}>Pilih produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->harga_jual }}"
                                                data-jual-eceran="{{ $product->jual_eceran ? 1 : 0 }}"
                                                data-harga-eceran="{{ $product->harga_eceran ?? 0 }}"
                                                data-unit-kemasan="{{ $product->unit_kemasan }}"
                                                data-unit-terkecil="{{ $product->unit_terkecil }}"
                                                data-isi-per-kemasan="{{ $product->isi_per_kemasan }}"
                                                {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->sku }} - {{ $product->nama_dagang }}
                                            @if($product->jual_eceran)
                                                ({{ $product->unit_kemasan }} / {{ $product->unit_terkecil }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[{{ $index }}][unit]" class="form-select unit-select" required data-row="{{ $index }}">
                                    <option value="kemasan" selected>Kemasan</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $index }}][qty]" class="form-control text-end qty-input" min="1" value="{{ $item['qty'] }}" required data-row="{{ $index }}"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" class="form-control text-end price-input" value="{{ $item['price'] }}" required data-row="{{ $index }}">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][discount]" class="form-control text-end discount-input" value="{{ $item['discount'] ?? 0 }}" data-row="{{ $index }}">
                                </div>
                            </td>
                            <td class="text-end line-total">0</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm removeRow" aria-label="Hapus"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @error('items')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="row mt-4">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span id="summarySubtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon Total</span>
                                <span id="summaryDiscount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fw-semibold">
                                <span>Total</span>
                                <span id="summaryTotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Bayar</span>
                                <span id="summaryPaid">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Kembali</span>
                                <span id="summaryChange">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@php
    $productsJson = $products
        ->map(fn($p) => [
            'id' => $p->id,
            'label' => $p->sku . ' - ' . $p->nama_dagang,
            'price' => (float) $p->harga_jual,
            'jual_eceran' => (bool) $p->jual_eceran,
            'harga_eceran' => (float) ($p->harga_eceran ?? 0),
            'unit_kemasan' => $p->unit_kemasan ?? 'Kemasan',
            'unit_terkecil' => $p->unit_terkecil ?? 'Unit',
            'isi_per_kemasan' => (int) ($p->isi_per_kemasan ?? 1),
        ])
        ->values()
        ->toJson();
@endphp
<script>
    const products = {!! $productsJson !!};

    const itemsTableBody = document.querySelector('#itemsTable tbody');
    const addItemBtn = document.getElementById('addItemRow');
    const addFromSearchBtn = document.getElementById('addFromSearch');
    const productSearchInput = document.getElementById('productSearch');
    const discountTotalInput = document.getElementById('discountTotal');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const paidAmountInput = document.getElementById('paidAmount');
    const paidAmountWrap = document.getElementById('paidAmountWrap');

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function getProductData(productId) {
        return products.find(p => p.id == productId);
    }

    function updateUnitOptions(row, productId) {
        const product = getProductData(productId);
        const unitSelect = row.querySelector('.unit-select');
        const priceInput = row.querySelector('.price-input');
        
        if (!product || !unitSelect) return;

        // Reset unit options
        unitSelect.innerHTML = '';
        
        // Tambah option kemasan
        const kemasanOption = document.createElement('option');
        kemasanOption.value = 'kemasan';
        kemasanOption.textContent = product.unit_kemasan + ' - Rp ' + product.price.toLocaleString('id-ID');
        kemasanOption.dataset.price = product.price;
        unitSelect.appendChild(kemasanOption);
        
        // Tambah option eceran jika tersedia
        if (product.jual_eceran && product.harga_eceran > 0) {
            const eceranOption = document.createElement('option');
            eceranOption.value = 'eceran';
            eceranOption.textContent = product.unit_terkecil + ' - Rp ' + product.harga_eceran.toLocaleString('id-ID');
            eceranOption.dataset.price = product.harga_eceran;
            unitSelect.appendChild(eceranOption);
        }
        
        // Set harga default (kemasan)
        priceInput.value = product.price;
    }

    function updatePriceByUnit(row) {
        const unitSelect = row.querySelector('.unit-select');
        const priceInput = row.querySelector('.price-input');
        const selectedOption = unitSelect?.selectedOptions[0];
        
        if (selectedOption && selectedOption.dataset.price) {
            priceInput.value = selectedOption.dataset.price;
        }
        
        recalc();
    }

    function recalc() {
        let subtotal = 0;
        const rows = itemsTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || 0);
            const price = parseFloat(row.querySelector('.price-input')?.value || 0);
            const disc = parseFloat(row.querySelector('.discount-input')?.value || 0);
            const net = Math.max(0, price - disc);
            const lineTotal = net * qty;
            subtotal += lineTotal;
            row.querySelector('.line-total').innerText = formatRupiah(lineTotal);
        });

        const discountTotal = parseFloat(discountTotalInput.value || 0);
        const total = Math.max(0, subtotal - discountTotal);

        let paid = total;
        if (paymentMethodSelect.value === 'CASH') {
            paid = parseFloat(paidAmountInput.value || 0);
        } else {
            paidAmountInput.value = total.toFixed(2);
        }
        const change = Math.max(0, paid - total);

        document.getElementById('summarySubtotal').innerText = formatRupiah(subtotal);
        document.getElementById('summaryDiscount').innerText = formatRupiah(discountTotal);
        document.getElementById('summaryTotal').innerText = formatRupiah(total);
        document.getElementById('summaryPaid').innerText = formatRupiah(paid);
        document.getElementById('summaryChange').innerText = formatRupiah(change);
    }

    function addRow(productId = '', price = '') {
        const index = itemsTableBody.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        
        const productOptions = products.map(p => {
            const info = p.jual_eceran ? ` (${p.unit_kemasan} / ${p.unit_terkecil})` : '';
            return `<option value="${p.id}" 
                        data-price="${p.price}"
                        data-jual-eceran="${p.jual_eceran ? 1 : 0}"
                        data-harga-eceran="${p.harga_eceran}"
                        data-unit-kemasan="${p.unit_kemasan}"
                        data-unit-terkecil="${p.unit_terkecil}"
                        data-isi-per-kemasan="${p.isi_per_kemasan}"
                        ${p.id == productId ? 'selected' : ''}>${p.label}${info}</option>`;
        }).join('');
        
        row.innerHTML = `
            <td>
                <select name="items[${index}][product_id]" class="form-select product-select" required data-row="${index}">
                    <option value="" disabled ${!productId ? 'selected' : ''}>Pilih produk</option>
                    ${productOptions}
                </select>
            </td>
            <td>
                <select name="items[${index}][unit]" class="form-select unit-select" required data-row="${index}">
                    <option value="kemasan" selected>Kemasan</option>
                </select>
            </td>
            <td><input type="number" name="items[${index}][qty]" class="form-control text-end qty-input" min="1" value="1" required data-row="${index}"></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" min="0" name="items[${index}][price]" class="form-control text-end price-input" value="${price}" required data-row="${index}">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" min="0" name="items[${index}][discount]" class="form-control text-end discount-input" value="0" data-row="${index}">
                </div>
            </td>
            <td class="text-end line-total">0</td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm removeRow" aria-label="Hapus"><i class="bi bi-trash"></i></button>
            </td>
        `;
        itemsTableBody.appendChild(row);
        
        // Update unit options jika productId ada
        if (productId) {
            updateUnitOptions(row, productId);
        }
        
        recalc();
    }

    addItemBtn?.addEventListener('click', () => addRow());

    itemsTableBody?.addEventListener('click', function(event) {
        if (event.target.closest('.removeRow')) {
            const row = event.target.closest('tr');
            if (itemsTableBody.querySelectorAll('tr').length > 1) {
                row.remove();
                recalc();
            }
        }
    });

    itemsTableBody?.addEventListener('change', function(event) {
        if (event.target.classList.contains('product-select')) {
            const opt = event.target.selectedOptions[0];
            const productId = opt?.value;
            const row = event.target.closest('tr');
            
            if (productId && row) {
                updateUnitOptions(row, productId);
            }
        }
        
        if (event.target.classList.contains('unit-select')) {
            const row = event.target.closest('tr');
            updatePriceByUnit(row);
        }
        
        recalc();
    });

    itemsTableBody?.addEventListener('input', function(event) {
        if (event.target.classList.contains('qty-input') || event.target.classList.contains('price-input') || event.target.classList.contains('discount-input')) {
            recalc();
        }
    });

    discountTotalInput?.addEventListener('input', recalc);
    paymentMethodSelect?.addEventListener('change', function() {
        if (this.value === 'CASH') {
            paidAmountWrap.classList.remove('d-none');
        } else {
            paidAmountWrap.classList.add('d-none');
        }
        recalc();
    });
    paidAmountInput?.addEventListener('input', recalc);

    addFromSearchBtn?.addEventListener('click', function() {
        const val = productSearchInput.value;
        const match = products.find(p => val && val.toLowerCase() === p.label.toLowerCase());
        if (match) {
            addRow(match.id, match.price);
            productSearchInput.value = '';
        }
    });

    // Initialize unit options untuk existing rows
    document.addEventListener('DOMContentLoaded', function() {
        const rows = itemsTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const productSelect = row.querySelector('.product-select');
            if (productSelect && productSelect.value) {
                updateUnitOptions(row, productSelect.value);
            }
        });
        recalc();
    });

    recalc();
</script>
@endpush
