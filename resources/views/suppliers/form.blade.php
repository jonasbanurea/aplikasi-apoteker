<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nama Supplier</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $supplier->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Kontak</label>
        <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $supplier->contact) }}" placeholder="Nama / No. HP / Email">
        @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Term Pembayaran (hari)</label>
        <input type="number" min="0" max="365" name="payment_term_days" class="form-control @error('payment_term_days') is-invalid @enderror" value="{{ old('payment_term_days', $supplier->payment_term_days ?? 30) }}" required>
        @error('payment_term_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Default jatuh tempo pembayaran dalam hari</small>
    </div>
</div>
