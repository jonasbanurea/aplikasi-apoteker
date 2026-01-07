@echo off
REM =====================================================
REM Script Auto-Start Aplikasi Toko Obat Ro Tua
REM =====================================================
REM Sesuaikan path berikut dengan lokasi instalasi Anda:

REM Path ke folder aplikasi (sesuaikan dengan lokasi Anda)
set APP_PATH=D:\PROJECT\APOTEKER\Aplikasi

REM Path ke folder XAMPP (sesuaikan jika berbeda)
set XAMPP_DIR=D:\xampp

REM Port aplikasi (default 8000, ubah jika bentrok)
set APP_PORT=8000

REM Browser pilihan: chrome atau msedge
set BROWSER=chrome

REM =====================================================
REM Jangan edit di bawah ini kecuali tahu yang dilakukan
REM =====================================================

echo.
echo ========================================
echo  Starting Toko Obat Ro Tua Application
echo ========================================
echo.

REM Pindah ke folder aplikasi
cd /d %APP_PATH%

echo [1/5] Checking XAMPP services...
REM Cek apakah Apache sudah jalan
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo Apache already running
) else (
    echo Starting Apache...
    start "" "%XAMPP_DIR%\apache\bin\httpd.exe"
)

REM Cek apakah MySQL sudah jalan
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo MySQL already running
) else (
    echo Starting MySQL...
    start "" "%XAMPP_DIR%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP_DIR%\mysql\bin\my.ini"
)

echo [2/5] Waiting for services to be ready (10 seconds)...
timeout /t 10 /nobreak >nul

echo [3/5] Starting Laravel server on port %APP_PORT%...
start "" cmd /c "php artisan serve --host=0.0.0.0 --port=%APP_PORT%"

echo [4/5] Waiting for Laravel server (5 seconds)...
timeout /t 5 /nobreak >nul

echo [5/5] Opening browser...
timeout /t 2 /nobreak >nul
if "%BROWSER%"=="chrome" (
    start "" chrome http://localhost:%APP_PORT%
) else (
    start "" msedge http://localhost:%APP_PORT%
)

echo.
echo ========================================
echo  Application Started Successfully!
echo  Access: http://localhost:%APP_PORT%
echo ========================================
echo.
echo Press any key to exit this window...
pause >nul