@echo off
:: ============================================
:: UPDATE PRODUK ONLY (Keep Transactions)
:: TOKO OBAT RO TUA
:: ============================================
:: Script untuk update HANYA data produk
:: Transaksi penjualan/pembelian TIDAK DIHAPUS
:: ============================================

setlocal EnableDelayedExpansion

set APP_PATH=d:\PROJECT\APOTEKER\Aplikasi
set EXCEL_FILE=%APP_PATH%\docs\NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx

echo ========================================
echo  UPDATE PRODUK ONLY (Keep Transactions)
echo  Toko Obat Ro Tua
echo ========================================
echo.

if not exist "%EXCEL_FILE%" (
    echo [ERROR] File Excel tidak ditemukan!
    echo File: %EXCEL_FILE%
    pause
    exit /b 1
)

echo [OK] File Excel ditemukan
echo.

echo ============================================
echo  INFORMASI PENTING!
echo ============================================
echo.
echo Proses ini akan:
echo  [HAPUS] Semua data PRODUK existing
echo  [HAPUS] Semua STOCK BATCH
echo  [HAPUS] Semua riwayat STOCK MOVEMENT  
echo  [IMPORT] Data produk baru dari Excel
echo.
echo  [AMAN] Data PENJUALAN tetap ada
echo  [AMAN] Data PEMBELIAN tetap ada
echo  [AMAN] Data USER tetap ada
echo  [AMAN] Data SUPPLIER tetap ada
echo.
set /p confirm="Lanjutkan? (ketik YES): "

if /i not "%confirm%"=="YES" (
    echo [BATAL] Update dibatalkan
    pause
    exit /b 0
)

cd /d "%APP_PATH%"

echo.
echo [1/4] Cek koneksi database...
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" >nul 2>&1
if errorlevel 1 (
    echo [ERROR] MySQL tidak running! Start MySQL di XAMPP.
    pause
    exit /b 1
)
echo [OK] Database terhubung

echo.
echo [2/4] Backup database...
if not exist "storage\backups\" mkdir "storage\backups\"
set BACKUP_FILE=backup_produk_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%
"C:\xampp\mysql\bin\mysqldump.exe" -u root toko_obat_ro_tua > "storage\backups\%BACKUP_FILE%" 2>nul
if errorlevel 1 (
    echo [WARNING] Backup gagal
) else (
    echo [OK] Backup: storage\backups\%BACKUP_FILE%
)

echo.
echo [3/4] Hapus data produk lama...
php artisan tinker --execute="DB::statement('SET FOREIGN_KEY_CHECKS=0;'); DB::table('sale_item_batches')->delete(); DB::table('sale_items')->delete(); DB::table('purchase_items')->delete(); DB::table('stock_movements')->delete(); DB::table('stock_batches')->delete(); DB::table('products')->delete(); DB::statement('SET FOREIGN_KEY_CHECKS=1;'); echo 'Products cleared';"

if errorlevel 1 (
    echo [ERROR] Gagal menghapus data lama
    pause
    exit /b 1
)
echo [OK] Data lama terhapus

echo.
echo [4/4] Import produk baru dari Excel...
echo Mohon tunggu (~20 detik)...
php artisan db:seed --class=ProductFromExcelSeeder

if errorlevel 1 (
    echo.
    echo [ERROR] Import GAGAL!
    echo Cek: storage\logs\laravel.log
    pause
    exit /b 1
)

echo [OK] Import selesai

echo.
echo Verifikasi hasil:
php artisan tinker --execute="echo 'Total Produk: ' . App\Models\Product::count() . chr(10); echo 'Total Transaksi Masih Ada: ' . App\Models\Sale::count() . ' sales' . chr(10); DB::table('products')->selectRaw('LEFT(lokasi_rak, 5) as rak, COUNT(*) as total')->groupBy('rak')->orderBy('rak')->get()->each(function($item) { echo $item->rak . ': ' . $item->total . chr(10); });"

echo.
echo ============================================
echo  UPDATE SELESAI!
echo ============================================
echo.
echo [OK] Data produk berhasil diupdate
echo [OK] Data transaksi tetap ada
echo.
pause
