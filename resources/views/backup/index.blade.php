@extends('layouts.admin')

@section('title', 'Backup')
@section('page-title', 'Backup Aplikasi')

@section('content')
<div class="card">
    <div class="card-body">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Error Backup</h5>
            <div style="white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 13px;">{{ session('error') }}</div>
            <hr>
            <div class="mb-0">
                <strong>Langkah Perbaikan:</strong>
                <ol class="mb-0 mt-2">
                    <li>Buka <strong>XAMPP Control Panel</strong> atau <strong>Laragon</strong></li>
                    <li>Pastikan <strong>MySQL</strong> service sedang <span class="badge bg-success">Running</span></li>
                    <li>Jika belum, klik tombol <strong>Start</strong> untuk MySQL</li>
                    <li>Tunggu sampai status berubah menjadi hijau</li>
                    <li>Refresh halaman ini dan coba backup lagi</li>
                </ol>
                <div class="mt-3">
                    <a href="{{ asset('TROUBLESHOOTING_BACKUP.md') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-book"></i> Baca Panduan Lengkap
                    </a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Backup Berhasil!</h5>
            <p class="mb-0">{{ session('success') }}</p>
            <hr>
            <p class="mb-0"><small><i class="bi bi-info-circle"></i> Simpan file backup di minimal 3 tempat berbeda (lokal, USB, cloud) untuk keamanan maksimal.</small></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="alert alert-info">
            <h6><i class="bi bi-info-circle-fill"></i> Tentang Backup</h6>
            <ul class="mb-0">
                <li>Backup akan menyimpan <strong>database (SQL)</strong> dan <strong>file asset</strong> ke dalam file ZIP</li>
                <li>Proses memakan waktu <strong>1-5 menit</strong> tergantung ukuran data</li>
                <li>Pastikan <strong>MySQL service sedang berjalan</strong> sebelum backup</li>
                <li>File akan disimpan di folder Documents secara default</li>
            </ul>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-cloud-download"></i> Backup Otomatis</h6>
                        <p class="card-text">Database + File dalam ZIP</p>
                        <form method="POST" action="{{ route('backup.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Lokasi Simpan</label>
                                <input type="text" name="target_dir" class="form-control form-control-sm" value="{{ $defaultPath }}" placeholder="C:\\Users\\...">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-download"></i> Backup Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-database"></i> Backup Manual</h6>
                        <p class="card-text">Via phpMyAdmin (Lebih Reliable)</p>
                        <div class="alert alert-success mb-2 py-2">
                            <small><strong>✅ RECOMMENDED</strong> jika backup otomatis gagal</small>
                        </div>
                        <ol class="small mb-3">
                            <li>Buka phpMyAdmin</li>
                            <li>Pilih database</li>
                            <li>Tab Export → Quick → SQL</li>
                            <li>Klik Go → Save file</li>
                        </ol>
                        <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-success w-100">
                            <i class="bi bi-box-arrow-up-right"></i> Buka phpMyAdmin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-question-circle"></i> Troubleshooting</h6>
                <p class="card-text"><strong>Jika backup gagal:</strong></p>
                <ol class="mb-2">
                    <li>Pastikan <strong>MySQL service Running</strong> di XAMPP/Laragon</li>
                    <li>Test buka <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a> - harus bisa dibuka</li>
                    <li>Restart XAMPP/Laragon jika perlu</li>
                    <li>Coba backup manual via phpMyAdmin (Export → Quick → SQL → Go)</li>
                </ol>
                <a href="{{ asset('TROUBLESHOOTING_BACKUP.md') }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="bi bi-book"></i> Panduan Lengkap Troubleshooting
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

