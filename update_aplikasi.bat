@echo off
chcp 65001 >nul
title Update Aplikasi Toko Obat Ro Tua

echo.
echo ╔════════════════════════════════════════════════════╗
echo ║   UPDATE APLIKASI TOKO OBAT RO TUA DARI GITHUB    ║
echo ╚════════════════════════════════════════════════════╝
echo.

:: Cek apakah git terinstall
where git >nul 2>nul
if errorlevel 1 (
    echo [ERROR] Git tidak ditemukan!
    echo.
    echo Silakan:
    echo 1. Install Git dari https://git-scm.com/download/win
    echo 2. Atau gunakan cara manual: Download ZIP dari GitHub
    echo.
    echo Tekan tombol apapun untuk keluar...
    pause >nul
    exit /b 1
)

echo [INFO] Git ditemukan ✓
echo.

:: Cek apakah di dalam git repository
git rev-parse --git-dir >nul 2>nul
if errorlevel 1 (
    echo [ERROR] Folder ini bukan Git repository!
    echo.
    echo Aplikasi harus di-clone dari GitHub terlebih dahulu.
    echo Gunakan: git clone https://github.com/jonasbanurea/aplikasi-apoteker.git
    echo.
    pause
    exit /b 1
)

echo [1/6] Memeriksa status...
git status --short
echo.

:: Cek apakah ada perubahan lokal
git diff --quiet
if errorlevel 1 (
    echo [PERINGATAN] Ada perubahan lokal yang belum di-commit!
    echo.
    echo Pilihan:
    echo 1. Simpan perubahan lokal (git stash)
    echo 2. Buang perubahan lokal (git reset)
    echo 3. Batalkan update
    echo.
    choice /c 123 /n /m "Pilih (1/2/3): "
    
    if errorlevel 3 (
        echo Update dibatalkan.
        pause
        exit /b 0
    )
    
    if errorlevel 2 (
        echo.
        echo [2/6] Mereset perubahan lokal...
        git reset --hard HEAD
        echo Perubahan lokal dibuang ✓
    ) else (
        echo.
        echo [2/6] Menyimpan perubahan lokal...
        git stash
        echo Perubahan lokal disimpan ✓
    )
) else (
    echo [2/6] Tidak ada perubahan lokal ✓
)
echo.

echo [3/6] Mengunduh update dari GitHub...
git pull origin main

if errorlevel 1 (
    echo.
    echo [ERROR] Gagal pull dari GitHub!
    echo.
    echo Kemungkinan masalah:
    echo - Tidak ada koneksi internet
    echo - Ada conflict yang perlu diselesaikan
    echo.
    pause
    exit /b 1
)

echo Update berhasil diunduh ✓
echo.

echo [4/6] Update dependencies...
if exist composer.phar (
    php composer.phar install --no-interaction --quiet
) else if exist "%ProgramFiles%\Composer\composer.bat" (
    call composer install --no-interaction --quiet
) else (
    echo [SKIP] Composer tidak ditemukan, skip update dependencies
)
echo.

echo [5/6] Clear cache aplikasi...
php artisan config:clear >nul 2>nul
php artisan cache:clear >nul 2>nul
php artisan view:clear >nul 2>nul
php artisan route:clear >nul 2>nul
echo Cache dibersihkan ✓
echo.

echo [6/6] Cek versi terbaru...
git log --oneline -5
echo.

echo ╔════════════════════════════════════════════════════╗
echo ║            UPDATE BERHASIL! ✓                     ║
echo ╚════════════════════════════════════════════════════╝
echo.
echo Update Terbaru (11 Jan 2026):
echo ✓ Fitur edit expired date di Stock Batch
echo ✓ Filter stok per batch
echo ✓ Perbaikan UI dan bug fixes
echo.
echo Langkah selanjutnya:
echo 1. Refresh browser Anda (Ctrl+F5)
echo 2. Login kembali ke aplikasi
echo 3. Test fitur baru: Stok ^> Stok per Batch ^> Edit
echo.
echo Tekan tombol apapun untuk keluar...
pause >nul
