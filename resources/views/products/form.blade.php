@php
    $golonganOptions = ['OTC', 'BEBAS_TERBATAS', 'RESEP', 'PSIKOTROPIKA', 'NARKOTIKA'];
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">SKU <small class="text-muted">(Kode Unik Produk)</small></label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required placeholder="Contoh: OBT-0001, SRP-0001">
        <small class="text-muted">Format: OBT-0001 (Tablet), SRP-0001 (Sirup), TOP-0001 (Salep), ALK-0001 (Alkes)</small>
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Nama Dagang</label>
        <input type="text" name="nama_dagang" class="form-control @error('nama_dagang') is-invalid @enderror" value="{{ old('nama_dagang', $product->nama_dagang) }}" required>
        @error('nama_dagang')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Nama Generik</label>
        <input type="text" name="nama_generik" class="form-control @error('nama_generik') is-invalid @enderror" value="{{ old('nama_generik', $product->nama_generik) }}" placeholder="Opsional">
        @error('nama_generik')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Bentuk</label>
        <input type="text" name="bentuk" class="form-control @error('bentuk') is-invalid @enderror" value="{{ old('bentuk', $product->bentuk) }}" required>
        @error('bentuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Kekuatan Dosis</label>
        <input type="text" name="kekuatan_dosis" class="form-control @error('kekuatan_dosis') is-invalid @enderror" value="{{ old('kekuatan_dosis', $product->kekuatan_dosis) }}" required>
        @error('kekuatan_dosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Satuan</label>
        <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', $product->satuan) }}" required>
        @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Golongan</label>
        <select name="golongan" class="form-select @error('golongan') is-invalid @enderror" required>
            <option value="" disabled {{ old('golongan', $product->golongan) ? '' : 'selected' }}>Pilih</option>
            @foreach($golonganOptions as $option)
                <option value="{{ $option }}" {{ old('golongan', $product->golongan) === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Wajib Resep?</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="wajib_resep" value="0">
            <input class="form-check-input" type="checkbox" name="wajib_resep" value="1" {{ old('wajib_resep', $product->wajib_resep) ? 'checked' : '' }}>
            <label class="form-check-label">Tandai jika membutuhkan resep</label>
        </div>
        @error('wajib_resep')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Harga Beli</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" step="0.01" min="0" name="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror" value="{{ old('harga_beli', $product->harga_beli) }}" required>
        </div>
        @error('harga_beli')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Harga Jual</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" step="0.01" min="0" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" value="{{ old('harga_jual', $product->harga_jual) }}" required>
        </div>
        @error('harga_jual')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Lokasi Rak</label>
        <input type="text" name="lokasi_rak" class="form-control @error('lokasi_rak') is-invalid @enderror" value="{{ old('lokasi_rak', $product->lokasi_rak) }}" placeholder="Contoh: A1-03">
        @error('lokasi_rak')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Minimal Stok</label>
        <input type="number" min="0" name="minimal_stok" class="form-control @error('minimal_stok') is-invalid @enderror" value="{{ old('minimal_stok', $product->minimal_stok ?? 0) }}" required>
        @error('minimal_stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Konsinyasi?</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="konsinyasi" value="0">
            <input class="form-check-input" type="checkbox" name="konsinyasi" value="1" {{ old('konsinyasi', $product->konsinyasi) ? 'checked' : '' }}>
            <label class="form-check-label">Tandai jika konsinyasi</label>
        </div>
        @error('konsinyasi')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <!-- Section Penjualan Eceran -->
    <div class="col-12">
        <hr class="my-3">
        <h6 class="text-primary mb-0">
            <i class="bi bi-box-seam"></i> Pengaturan Penjualan Eceran
        </h6>
        <small class="text-muted">Untuk produk yang dijual per kapsul/tablet/ml (bukan per kemasan utuh)</small>
    </div>

    <div class="col-md-3">
        <label class="form-label">Jual Eceran?</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="jual_eceran" value="0">
            <input class="form-check-input" type="checkbox" id="jualEceranCheck" name="jual_eceran" value="1" {{ old('jual_eceran', $product->jual_eceran) ? 'checked' : '' }}>
            <label class="form-check-label">Aktifkan penjualan eceran</label>
        </div>
        @error('jual_eceran')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3" id="unitKemasanField">
        <label class="form-label">Unit Kemasan <small class="text-muted">(untuk harga jual)</small></label>
        <input type="text" name="unit_kemasan" id="unitKemasan" class="form-control @error('unit_kemasan') is-invalid @enderror" value="{{ old('unit_kemasan', $product->unit_kemasan) }}" placeholder="Contoh: STRIP, BOX, BOTOL">
        <small class="text-muted">Kemasan besar yang dijual normal</small>
        @error('unit_kemasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3" id="unitTerkecilField">
        <label class="form-label">Unit Terkecil <small class="text-muted">(untuk eceran)</small></label>
        <input type="text" name="unit_terkecil" id="unitTerkecil" class="form-control @error('unit_terkecil') is-invalid @enderror" value="{{ old('unit_terkecil', $product->unit_terkecil) }}" placeholder="Contoh: KAPSUL, TABLET, ML">
        <small class="text-muted">Unit satuan paling kecil</small>
        @error('unit_terkecil')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3" id="isiPerKemasanField">
        <label class="form-label">Isi Per Kemasan</label>
        <input type="number" min="1" name="isi_per_kemasan" id="isiPerKemasan" class="form-control @error('isi_per_kemasan') is-invalid @enderror" value="{{ old('isi_per_kemasan', $product->isi_per_kemasan) }}" placeholder="Contoh: 10">
        <small class="text-muted">Berapa unit terkecil dalam 1 kemasan</small>
        @error('isi_per_kemasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12" id="hargaEceranInfo">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> 
            <strong>Contoh:</strong> Sangobion Strip (10 kapsul) seharga Rp 26.000
            <ul class="mb-0 mt-2">
                <li>Unit Kemasan: <code>STRIP</code></li>
                <li>Unit Terkecil: <code>KAPSUL</code></li>
                <li>Isi Per Kemasan: <code>10</code></li>
                <li>Harga Jual: <code>Rp 26.000</code> (per strip)</li>
                <li><strong>Harga Eceran Otomatis: Rp 26.000 ÷ 10 = Rp 2.600 per kapsul</strong></li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle field eceran visibility
    const jualEceranCheck = document.getElementById('jualEceranCheck');
    const unitKemasanField = document.getElementById('unitKemasanField');
    const unitTerkecilField = document.getElementById('unitTerkecilField');
    const isiPerKemasanField = document.getElementById('isiPerKemasanField');
    const hargaEceranInfo = document.getElementById('hargaEceranInfo');

    function toggleEceranFields() {
        const isChecked = jualEceranCheck?.checked;
        const fields = [unitKemasanField, unitTerkecilField, isiPerKemasanField, hargaEceranInfo];
        
        fields.forEach(field => {
            if (field) {
                if (isChecked) {
                    field.style.display = 'block';
                } else {
                    field.style.display = 'none';
                }
            }
        });

        // Make fields required/optional based on checkbox
        const unitKemasan = document.getElementById('unitKemasan');
        const unitTerkecil = document.getElementById('unitTerkecil');
        const isiPerKemasan = document.getElementById('isiPerKemasan');
        
        if (unitKemasan) unitKemasan.required = isChecked;
        if (unitTerkecil) unitTerkecil.required = isChecked;
        if (isiPerKemasan) isiPerKemasan.required = isChecked;
    }

    jualEceranCheck?.addEventListener('change', toggleEceranFields);
    
    // Initial state
    if (jualEceranCheck) {
        toggleEceranFields();
    }
</script>
@endpush
