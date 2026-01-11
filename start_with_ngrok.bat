@echo off
REM =====================================================
REM Script untuk Setup dan Jalankan Ngrok
REM Akses aplikasi dari internet dengan mudah
REM =====================================================

echo.
echo ========================================
echo  Setup Ngrok - Akses dari Internet
echo  Toko Obat Ro Tua
echo ========================================
echo.

REM Cek apakah ngrok.exe ada
if not exist "ngrok.exe" (
    echo [WARNING] ngrok.exe tidak ditemukan
    echo.
    echo Cara install Ngrok:
    echo ========================================
    echo.
    echo 1. Buka browser, kunjungi:
    echo    https://ngrok.com/download
    echo.
    echo 2. Download "Windows (64-bit)"
    echo.
    echo 3. Ekstrak file ngrok.exe ke folder:
    echo    %CD%
    echo.
    echo 4. Daftar akun gratis di:
    echo    https://dashboard.ngrok.com/signup
    echo.
    echo 5. Dapatkan authtoken dari:
    echo    https://dashboard.ngrok.com/get-started/your-authtoken
    echo.
    echo 6. Jalankan script ini lagi
    echo.
    echo ========================================
    pause
    exit /b 1
)

REM Cek apakah sudah auth
if not exist "%USERPROFILE%\.ngrok2\ngrok.yml" (
    echo [INFO] Belum setup authtoken
    echo.
    echo Untuk setup authtoken:
    echo ========================================
    echo.
    echo 1. Login ke https://dashboard.ngrok.com/
    echo.
    echo 2. Copy authtoken Anda
    echo.
    echo 3. Jalankan command:
    echo    ngrok.exe config add-authtoken YOUR_AUTH_TOKEN
    echo.
    echo 4. Contoh:
    echo    ngrok.exe config add-authtoken 2abc123def456ghi789jkl
    echo.
    echo ========================================
    echo.
    set /p AUTH_TOKEN="Paste authtoken Anda (atau tekan Enter untuk skip): "
    
    if not "!AUTH_TOKEN!"=="" (
        echo.
        echo Setting up authtoken...
        ngrok.exe config add-authtoken !AUTH_TOKEN!
        echo.
        echo [OK] Authtoken berhasil disimpan
    )
)

echo.
echo ========================================
echo  Starting Aplikasi dengan Ngrok
echo ========================================
echo.

REM Cek apakah Laravel sudah running
netstat -ano | findstr :8000 >nul 2>&1
if %errorLevel% neq 0 (
    echo [1/2] Starting Laravel server...
    start "" cmd /c "php artisan serve --host=0.0.0.0 --port=8000"
    
    echo [INFO] Waiting for Laravel to start...
    timeout /t 5 /nobreak >nul
) else (
    echo [OK] Laravel server sudah berjalan
)

echo.
echo [2/2] Starting Ngrok tunnel...
echo.
echo ========================================
echo  INFORMASI PENTING
echo ========================================
echo.
echo - Ngrok akan membuat URL public untuk aplikasi
echo - URL ini bisa diakses dari mana saja (internet)
echo - Jangan matikan window ini selama akses
echo - Tekan Ctrl+C untuk stop Ngrok
echo.
echo URL akan ditampilkan di layar berikut ini
echo Copy URL tersebut dan share ke user
echo.
echo ========================================
echo.

pause

REM Start ngrok
ngrok.exe http 8000
