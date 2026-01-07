@echo off
:: ============================================
:: UPDATE DATA PRODUK - TOKO OBAT RO TUA
:: ============================================
:: Script untuk update data produk dari Excel terbaru
:: File Excel: NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx
:: ============================================

setlocal EnableDelayedExpansion

:: Konfigurasi
set APP_PATH=d:\PROJECT\APOTEKER\Aplikasi
set EXCEL_FILE=%APP_PATH%\docs\NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx

echo ========================================
echo  UPDATE DATA PRODUK
echo  Toko Obat Ro Tua
echo ========================================
echo.

:: Cek apakah file Excel ada
if not exist "%EXCEL_FILE%" (
    echo [ERROR] File Excel tidak ditemukan!
    echo Lokasi yang dicari: %EXCEL_FILE%
    echo.
    echo Pastikan file sudah ada di folder docs\
    echo File: NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx
    pause
    exit /b 1
)

echo [OK] File Excel ditemukan
echo Lokasi: %EXCEL_FILE%
echo.

:: Konfirmasi dari user
echo.
echo ============================================
echo  PERINGATAN PENTING!
echo ============================================
echo.
echo Proses ini akan:
echo  1. MENGHAPUS SEMUA DATA PRODUK yang ada
echo  2. MENGHAPUS SEMUA STOCK BATCH
echo  3. MENGHAPUS SEMUA RIWAYAT STOCK MOVEMENT
echo  4. Import data baru dari Excel (~600 produk)
echo.
echo Data transaksi (penjualan/pembelian) akan IKUT TERHAPUS
echo karena menggunakan migrate:fresh!
echo.
echo Pastikan Anda sudah BACKUP database terlebih dahulu!
echo.
set /p confirm="Lanjutkan? (ketik YES untuk lanjut): "

if /i not "%confirm%"=="YES" (
    echo.
    echo [BATAL] Update data dibatalkan.
    pause
    exit /b 0
)

echo.
echo ============================================
echo  PROSES UPDATE DIMULAI...
echo ============================================
echo.

:: Pindah ke folder aplikasi
cd /d "%APP_PATH%"

:: Cek apakah MySQL running
echo [1/4] Memeriksa koneksi database...
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Database tidak terhubung!
    echo.
    echo Pastikan:
    echo  - XAMPP Control Panel dibuka
    echo  - MySQL sudah di-START (warna hijau)
    echo  - Port 3306 tidak dipakai aplikasi lain
    pause
    exit /b 1
)
echo [OK] Database terhubung

echo.
echo [2/4] Membuat backup otomatis...
set BACKUP_FILE=backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%
echo Backup ke: storage\backups\%BACKUP_FILE%

if not exist "storage\backups\" mkdir "storage\backups\"
"C:\xampp\mysql\bin\mysqldump.exe" -u root --skip-comments toko_obat_ro_tua > "storage\backups\%BACKUP_FILE%" 2>nul
if errorlevel 1 (
    echo [WARNING] Backup gagal, tapi lanjutkan...
) else (
    echo [OK] Backup berhasil
)

echo.
echo [3/4] Menghapus data lama dan membuat ulang database...
echo Mohon tunggu (~30 detik)...
php artisan migrate:fresh --seed

if errorlevel 1 (
    echo.
    echo [ERROR] Import data GAGAL!
    echo.
    echo Kemungkinan penyebab:
    echo  - File Excel corrupt/tidak bisa dibaca
    echo  - Format Excel tidak sesuai
    echo  - MySQL error
    echo.
    echo Cek log error di: storage\logs\laravel.log
    pause
    exit /b 1
)

echo [OK] Import data selesai

echo.
echo [4/4] Verifikasi hasil import...
php artisan tinker --execute="echo 'Total Produk: ' . App\Models\Product::count() . chr(10); DB::table('products')->selectRaw('LEFT(lokasi_rak, 5) as rak, COUNT(*) as total')->groupBy('rak')->orderBy('rak')->get()->each(function($item) { echo $item->rak . ': ' . $item->total . chr(10); });"

echo.
echo ============================================
echo  UPDATE DATA SELESAI!
echo ============================================
echo.
echo Langkah selanjutnya:
echo  1. Login ke aplikasi: http://localhost:8000
echo  2. User default:
echo     - Owner: owner@rotua.test / password
echo     - Kasir: kasir@rotua.test / password
echo     - Admin Gudang: gudang@rotua.test / password
echo  3. Verifikasi data di menu Produk
echo  4. Ganti password user jika perlu
echo.
echo Backup tersimpan di: storage\backups\%BACKUP_FILE%
echo.
pause
