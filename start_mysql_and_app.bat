@echo off
chcp 65001 > nul
title Start MySQL & Aplikasi - Toko Obat Ro Tua
color 0B

echo.
echo ═══════════════════════════════════════════════════════════════
echo   START MySQL ^& APLIKASI - TOKO OBAT RO TUA
echo ═══════════════════════════════════════════════════════════════
echo.

:: Cek apakah MySQL sudah running
echo [1] Cek status MySQL...
netstat -ano | findstr ":3306" > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ MySQL sudah berjalan di port 3306
    goto start_app
)

echo ⚠️  MySQL belum berjalan
echo.
echo [2] Mencoba start MySQL...
echo.

:: Try XAMPP
if exist "C:\xampp\xampp_start.exe" (
    echo 🔧 Mendeteksi XAMPP...
    echo    Mohon START MySQL secara manual di XAMPP Control Panel
    start "" "C:\xampp\xampp-control.exe"
    timeout /t 3 > nul
    goto wait_mysql
)

:: Try Laragon
if exist "C:\laragon\laragon.exe" (
    echo 🔧 Mendeteksi Laragon...
    echo    Mohon START ALL di Laragon Control Panel
    start "" "C:\laragon\laragon.exe"
    timeout /t 3 > nul
    goto wait_mysql
)

echo ❌ XAMPP/Laragon tidak ditemukan di lokasi default
echo    Mohon start MySQL secara manual:
echo    1. Buka XAMPP Control Panel atau Laragon
echo    2. Klik Start MySQL
echo    3. Tunggu sampai status Running (hijau)
echo    4. Jalankan script ini lagi
echo.
pause
exit /b 1

:wait_mysql
echo.
echo [3] Menunggu MySQL siap...
set /a timeout=45
set /a counter=0

:check_mysql
netstat -ano | findstr ":3306" > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ MySQL port 3306 terbuka!
    echo    Menunggu tambahan 10 detik untuk MySQL fully ready...
    timeout /t 10 > nul
    goto start_app
)

set /a counter+=1
if %counter% GEQ %timeout% (
    echo ❌ Timeout menunggu MySQL
    echo    Mohon start MySQL secara manual di XAMPP/Laragon
    pause
    exit /b 1
)

echo    Menunggu... (%counter%/%timeout%)
timeout /t 1 > nul
goto check_mysql

:start_app
echo.
echo [4] Membuka aplikasi...
echo    URL: http://localhost:8000
echo.
timeout /t 2 > nul

:: Start Laravel dev server jika belum jalan
tasklist /FI "WINDOWTITLE eq php artisan serve*" 2>nul | find "php.exe" > nul
if %ERRORLEVEL% EQU 0 (
    echo ℹ️  Laravel server sudah berjalan
) else (
    echo 🚀 Menjalankan Laravel development server...
    start "Laravel Server - Toko Obat Ro Tua" /MIN cmd /k "cd /d %~dp0 && php artisan serve"
    timeout /t 3 > nul
)

:: Buka browser
start http://localhost:8000

echo.
echo ═══════════════════════════════════════════════════════════════
echo   APLIKASI SIAP DIGUNAKAN
echo ═══════════════════════════════════════════════════════════════
echo.
echo ✅ MySQL: Running ^& Fully Ready
echo ✅ Laravel: Running  
echo ✅ Browser: Opened
echo.
echo 💡 TIPS BACKUP:
echo    MySQL sudah fully ready - aman untuk backup database
echo.
echo Jika halaman tidak muncul, tunggu 5-10 detik lalu refresh browser
echo.
echo Untuk STOP aplikasi: Tutup jendela terminal Laravel
echo ═══════════════════════════════════════════════════════════════
echo.
pause
