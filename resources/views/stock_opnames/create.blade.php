@extends('layouts.admin')

@section('title', 'Buat Stock Opname')
@section('page-title', 'Buat Stock Opname')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('stock-opnames.store') }}" id="opnameForm">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Opname</label>
                    <input type="date" name="opname_date" class="form-control @error('opname_date') is-invalid @enderror" value="{{ old('opname_date', now()->format('Y-m-d')) }}" required>
                    @error('opname_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-9">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror" value="{{ old('notes') }}" placeholder="Opsional">
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Item Opname</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                    <i class="bi bi-plus-circle"></i> Tambah Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 28%">Batch</th>
                            <th>Produk</th>
                            <th>ED</th>
                            <th class="text-end">Qty Sistem</th>
                            <th class="text-end">Qty Terjual</th>
                            <th class="text-end">Qty Fisik</th>
                            <th>Reason</th>
                            <th>Catatan</th>
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
                                    <td class="text-end system-qty">0</td>
                                    <td class="text-end text-muted qty-sold">0</td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][physical_qty]" class="form-control text-end" min="0" value="{{ $oldItem['physical_qty'] ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $i }}][reason]" class="form-select" required>
                                            <option value="SELISIH_OPNAME" {{ ($oldItem['reason'] ?? '') === 'SELISIH_OPNAME' ? 'selected' : '' }}>SELISIH_OPNAME</option>
                                            <option value="RUSAK" {{ ($oldItem['reason'] ?? '') === 'RUSAK' ? 'selected' : '' }}>RUSAK</option>
                                            <option value="KADALUARSA" {{ ($oldItem['reason'] ?? '') === 'KADALUARSA' ? 'selected' : '' }}>KADALUARSA</option>
                                            <option value="HILANG" {{ ($oldItem['reason'] ?? '') === 'HILANG' ? 'selected' : '' }}>HILANG</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="items[{{ $i }}][notes]" class="form-control" value="{{ $oldItem['notes'] ?? '' }}" placeholder="Opsional"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('stock-opnames.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Opname
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
        $batchPayload = $batches->map(function ($b) use ($qtySoldPerProduct) {
            return [
                'id' => $b->id,
                'batch_no' => $b->batch_no,
                'product' => $b->product?->nama_dagang,
                'sku' => $b->product?->sku,
                'product_id' => $b->product_id,
                'expired_date' => $b->expired_date ? $b->expired_date->format('Y-m-d') : null,
                'qty_on_hand' => $b->qty_on_hand,
                'qty_sold' => $qtySoldPerProduct[$b->product_id] ?? 0,
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
        const systemCell = row.querySelector('.system-qty');
        const qtySoldCell = row.querySelector('.qty-sold');
        const physicalInput = row.querySelector('input[name$="[physical_qty]"]');
        const batch = findBatch(select.value);

        if (batch) {
            prodCell.textContent = `${batch.sku ?? ''} - ${batch.product ?? ''}`;
            expiredCell.textContent = batch.expired_date ?? '-';
            systemCell.textContent = batch.qty_on_hand;
            qtySoldCell.textContent = batch.qty_sold ?? 0;
            meta.textContent = batch.expired_date ? `ED ${batch.expired_date}` : '';
            if (!physicalInput.value) {
                physicalInput.value = batch.qty_on_hand;
            }
        } else {
            prodCell.textContent = '';
            expiredCell.textContent = '';
            systemCell.textContent = '';
            qtySoldCell.textContent = '0';
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
            <td class="text-end system-qty">0</td>
            <td class="text-end text-muted qty-sold">0</td>
            <td><input type="number" name="items[${idx}][physical_qty]" class="form-control text-end" min="0" value="${old.physical_qty ?? ''}" required></td>
            <td>
                <select name="items[${idx}][reason]" class="form-select" required>
                    <option value="SELISIH_OPNAME">SELISIH_OPNAME</option>
                    <option value="RUSAK" ${old.reason === 'RUSAK' ? 'selected' : ''}>RUSAK</option>
                    <option value="KADALUARSA" ${old.reason === 'KADALUARSA' ? 'selected' : ''}>KADALUARSA</option>
                    <option value="HILANG" ${old.reason === 'HILANG' ? 'selected' : ''}>HILANG</option>
                </select>
            </td>
            <td><input type="text" name="items[${idx}][notes]" class="form-control" value="${old.notes ?? ''}" placeholder="Opsional"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        tr.querySelector('.batch-select').addEventListener('change', () => refreshRow(tr));
        tr.querySelector('.remove-row').addEventListener('click', () => tr.remove());
        refreshRow(tr);
    }

    function hydrateExistingRows() {
        tbody.querySelectorAll('tr').forEach((tr) => {
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
