@extends('layouts.admin')

@section('title', 'Backup')
@section('page-title', 'Backup Aplikasi')

@section('content')
<div class="card">
    <div class="card-body">
        <p>Backup akan menyimpan database (SQL) dan asset (storage/public + public) ke file ZIP di folder sementara.</p>
        <form method="POST" action="{{ route('backup.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Lokasi Simpan (default Documents)</label>
                <input type="text" name="target_dir" class="form-control" value="{{ $defaultPath }}" placeholder="Contoh: C:\\Users\\Nama\\Documents\\toko-obat-ro-tua-backup">
                <small class="text-muted">Folder akan dibuat jika belum ada. Pastikan drive memiliki ruang cukup.</small>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-download"></i> Buat Backup</button>
        </form>
    </div>
</div>
@endsection
