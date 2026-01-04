@echo off
REM =====================================================
REM Script Auto-Start Aplikasi Toko Obat Ro Tua
REM =====================================================
REM Sesuaikan path berikut dengan lokasi instalasi Anda:

REM Path ke folder aplikasi (sesuaikan dengan lokasi Anda)
set APP_PATH=D:\PROJECT\APOTEKER\Aplikasi

REM Path ke XAMPP Control (sesuaikan jika berbeda)
set XAMPP_PATH=D:\xampp\xampp-control.exe

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

echo [1/4] Starting XAMPP (Apache + MySQL)...
start "" "%XAMPP_PATH%" --startapache --startmysql

echo [2/4] Waiting for MySQL to be ready (10 seconds)...
timeout /t 10 /nobreak >nul

echo [3/4] Starting Laravel server on port %APP_PORT%...
start "" cmd /c "php artisan serve --host=0.0.0.0 --port=%APP_PORT%"

echo [4/4] Opening browser in kiosk mode...
timeout /t 3 /nobreak >nul
if "%BROWSER%"=="chrome" (
    start "" chrome --kiosk http://localhost:%APP_PORT%
) else (
    start "" msedge --kiosk http://localhost:%APP_PORT% --edge-kiosk-type=fullscreen
)

echo.
echo ========================================
echo  Application Started Successfully!
echo  Access: http://localhost:%APP_PORT%
echo ========================================
echo.
echo Press any key to exit this window...
pause >nul