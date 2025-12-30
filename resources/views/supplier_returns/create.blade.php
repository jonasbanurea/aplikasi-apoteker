@extends('layouts.admin')

@section('title', 'Buat Retur Supplier')
@section('page-title', 'Buat Retur Supplier')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('supplier-returns.store') }}" id="returnForm">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Retur</label>
                    <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', now()->format('Y-m-d')) }}" required>
                    @error('return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Opsional">
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Item Retur</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                    <i class="bi bi-plus-circle"></i> Tambah Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%">Batch</th>
                            <th>Produk</th>
                            <th>ED</th>
                            <th class="text-end">Qty On Hand</th>
                            <th class="text-end">Qty Retur</th>
                            <th>Alasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('items'))
                            @foreach(old('items') as $i => $oldItem)
                                <tr>
                                    <td>
                                        <select name="items[{{ $i }}][batch_id]" class="form-select batch-select" required data-selected="{{ $oldItem['batch_id'] ?? '' }}"></select>
                                        <div class="small text-muted batch-meta"></div>
                                    </td>
                                    <td class="product-name text-wrap"></td>
                                    <td class="expired-date"></td>
                                    <td class="text-end qty-on-hand">0</td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][qty]" class="form-control text-end" min="1" value="{{ $oldItem['qty'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $i }}][reason]" class="form-control" value="{{ $oldItem['reason'] ?? '' }}" placeholder="Opsional">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('supplier-returns.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Retur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    @php
        $batchPayload = $batches->map(function ($b) {
            return [
                'id' => $b->id,
                'batch_no' => $b->batch_no,
                'product' => $b->product?->nama_dagang,
                'sku' => $b->product?->sku,
                'expired_date' => $b->expired_date ? $b->expired_date->format('Y-m-d') : null,
                'qty_on_hand' => $b->qty_on_hand,
            ];
        })->values();
    @endphp
    const batches = @json($batchPayload);

    const tbody = document.querySelector('#itemsTable tbody');
    const addBtn = document.querySelector('#addRowBtn');

    function buildOptions(selectedId) {
        return batches.map(b => `<option value="${b.id}" ${b.id === selectedId ? 'selected' : ''}>${b.batch_no} - ${b.sku} ${b.product ?? ''}</option>`).join('');
    }

    function findBatch(id) {
        return batches.find(b => b.id === Number(id));
    }

    function refreshRow(row) {
        const select = row.querySelector('.batch-select');
        const meta = row.querySelector('.batch-meta');
        const prodCell = row.querySelector('.product-name');
        const expiredCell = row.querySelector('.expired-date');
        const qtyCell = row.querySelector('.qty-on-hand');
        const batch = findBatch(select.value);

        if (batch) {
            prodCell.textContent = `${batch.sku ?? ''} - ${batch.product ?? ''}`;
            expiredCell.textContent = batch.expired_date ?? '-';
            qtyCell.textContent = batch.qty_on_hand;
            meta.textContent = batch.expired_date ? `ED ${batch.expired_date}` : '';
        } else {
            prodCell.textContent = '';
            expiredCell.textContent = '';
            qtyCell.textContent = '';
            meta.textContent = '';
        }
    }

    function addRow(old = {}) {
        const idx = tbody.children.length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="items[${idx}][batch_id]" class="form-select batch-select" required>
                    <option value="" disabled selected>Pilih batch</option>
                    ${buildOptions(Number(old.batch_id) || null)}
                </select>
                <div class="small text-muted batch-meta"></div>
            </td>
            <td class="product-name text-wrap"></td>
            <td class="expired-date"></td>
            <td class="text-end qty-on-hand">0</td>
            <td><input type="number" name="items[${idx}][qty]" class="form-control text-end" min="1" value="${old.qty ?? 1}" required></td>
            <td><input type="text" name="items[${idx}][reason]" class="form-control" value="${old.reason ?? ''}" placeholder="Opsional"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        tr.querySelector('.batch-select').addEventListener('change', () => refreshRow(tr));
        tr.querySelector('.remove-row').addEventListener('click', () => tr.remove());
        refreshRow(tr);
    }

    function hydrateExistingRows() {
        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            const select = tr.querySelector('.batch-select');
            const selectedId = Number(select.getAttribute('data-selected')) || null;
            select.innerHTML = `<option value="" disabled ${selectedId ? '' : 'selected'}>Pilih batch</option>${buildOptions(selectedId)}`;
            select.addEventListener('change', () => refreshRow(tr));
            const removeBtn = tr.querySelector('.remove-row');
            removeBtn?.addEventListener('click', () => tr.remove());
            refreshRow(tr);
        });
    }

    hydrateExistingRows();

    addBtn?.addEventListener('click', () => addRow());

    if (tbody.children.length === 0) {
        addRow();
    }
})();
</script>
@endpush
