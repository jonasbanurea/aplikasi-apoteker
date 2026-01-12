@extends('layouts.admin')

@section('title', 'Detail Penjualan')
@section('page-title', 'Detail Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">
            {{ $sale->invoice_no }}
            @if($sale->is_cancelled)
                <span class="badge bg-danger ms-2">DIBATALKAN</span>
            @endif
        </h4>
        <small class="text-muted">{{ $sale->sale_date?->format('d M Y H:i') }}</small>
    </div>
    <div>
        @if(!$sale->is_cancelled)
            <a href="{{ route('sales.print', $sale) }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-printer"></i> Cetak HTML
            </a>
            <a href="{{ route('sales.print-thermal', $sale) }}" class="btn btn-primary" download>
                <i class="bi bi-receipt"></i> Print Thermal 58mm
            </a>
            @role('owner')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-circle"></i> Batalkan
                </button>
            @endrole
        @endif
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Kasir</div>
                <div class="fw-semibold">{{ $sale->user?->name ?? '-' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Metode</div>
                @if($sale->payment_method === \App\Models\Sale::METHOD_CASH)
                    <span class="badge bg-success">CASH</span>
                @else
                    <span class="badge bg-info text-dark">NON CASH</span>
                @endif
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Bayar</div>
                <div>Rp {{ number_format($sale->paid_amount, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Kembali</div>
                <div>Rp {{ number_format($sale->change_amount, 2, ',', '.') }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Resep / Dokter</div>
                <div>{{ $sale->no_resep ?? '-' }} {{ $sale->dokter ? ' - ' . $sale->dokter : '' }}</div>
            </div>
            <div class="col-md-12">
                <div class="text-muted small">Catatan</div>
                <div>{{ $sale->catatan ?? '-' }}</div>
            </div>
            @if($sale->is_cancelled)
                <div class="col-md-12">
                    <div class="alert alert-danger mb-0 mt-2">
                        <strong><i class="bi bi-exclamation-triangle"></i> Transaksi Dibatalkan</strong><br>
                        <small>Dibatalkan oleh: {{ $sale->cancelledBy?->name ?? '-' }}</small><br>
                        <small>Waktu: {{ $sale->cancelled_at?->format('d M Y H:i') }}</small><br>
                        <small>Alasan: {{ $sale->cancel_reason ?? '-' }}</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Diskon/Unit</th>
                        <th class="text-end">Subtotal</th>
                        <th>Batch (FEFO)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($sale->items as $item)
                        @php $subtotal += $item->line_total; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->product->nama_dagang ?? '-' }}</div>
                                <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                            </td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->discount, 2, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->line_total, 2, ',', '.') }}</td>
                            <td>
                                @forelse($item->batches as $alloc)
                                    <div>Batch {{ $alloc->stockBatch->batch_no ?? '-' }} ({{ $alloc->qty }})</div>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Subtotal</th>
                        <th class="text-end">Rp {{ number_format($subtotal, 2, ',', '.') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Diskon Total</th>
                        <th class="text-end">Rp {{ number_format($sale->discount_total, 2, ',', '.') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Total</th>
                        <th class="text-end">Rp {{ number_format($sale->total, 2, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Cancel -->
@role('owner')
@if(!$sale->is_cancelled)
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Batalkan Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sales.cancel', $sale) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Perhatian!</strong> Pembatalan transaksi akan mengembalikan stok produk.
                    </div>
                    <p>Apakah Anda yakin ingin membatalkan transaksi <strong>{{ $sale->invoice_no }}</strong>?</p>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Alasan Pembatalan</label>
                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required>Transaksi duplikat</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endrole
@endsection
