@echo off
REM =====================================================
REM Script untuk Generate QR Code URL Aplikasi
REM =====================================================

echo.
echo ========================================
echo  Generate QR Code untuk Akses Mudah
echo ========================================
echo.

REM Dapatkan IP Address otomatis
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP_ADDRESS=%%a
    goto :found_ip
)
:found_ip
set IP_ADDRESS=%IP_ADDRESS:~1%

set APP_URL=http://%IP_ADDRESS%:8000

echo URL Aplikasi Anda:
echo %APP_URL%
echo.
echo ========================================
echo  Cara Generate QR Code:
echo ========================================
echo.
echo 1. Buka website QR Code Generator:
echo    - https://www.qr-code-generator.com/
echo    - https://www.the-qrcode-generator.com/
echo    - https://qr.io/
echo.
echo 2. Pilih type: URL
echo.
echo 3. Copy dan paste URL berikut:
echo    %APP_URL%
echo.
echo 4. Klik "Create QR Code" atau "Generate"
echo.
echo 5. Download QR Code (format PNG atau SVG)
echo.
echo 6. Print dan tempel di area kasir
echo.
echo ========================================
echo  Cara Pakai QR Code:
echo ========================================
echo.
echo 1. Staff buka kamera HP
echo 2. Arahkan ke QR Code
echo 3. Tap notifikasi yang muncul
echo 4. Browser akan langsung buka aplikasi
echo 5. Login dengan akun kasir/gudang
echo.
echo ========================================

pause
