@echo off
REM =====================================================
REM Script Download Ngrok Otomatis
REM =====================================================

echo.
echo ========================================
echo  Download Ngrok untuk Windows
echo ========================================
echo.

REM Cek apakah ngrok.exe sudah ada
if exist "ngrok.exe" (
    echo [OK] ngrok.exe sudah ada di folder ini
    echo.
    echo Versi Ngrok:
    ngrok.exe version
    echo.
    echo Jika ingin update, hapus ngrok.exe lalu jalankan script ini lagi
    pause
    exit /b 0
)

echo [INFO] Downloading Ngrok...
echo.

REM Download ngrok menggunakan PowerShell
powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-windows-amd64.zip' -OutFile 'ngrok.zip' }"

if %errorLevel% neq 0 (
    echo.
    echo [ERROR] Gagal download Ngrok
    echo.
    echo Silakan download manual:
    echo 1. Buka: https://ngrok.com/download
    echo 2. Pilih Windows 64-bit
    echo 3. Ekstrak ngrok.exe ke folder: %CD%
    echo.
    pause
    exit /b 1
)

echo [INFO] Extracting Ngrok...
powershell -Command "Expand-Archive -Path 'ngrok.zip' -DestinationPath '.' -Force"

REM Hapus file zip
del ngrok.zip

if exist "ngrok.exe" (
    echo.
    echo ========================================
    echo  [SUCCESS] Ngrok berhasil diinstall!
    echo ========================================
    echo.
    echo Versi Ngrok:
    ngrok.exe version
    echo.
    echo LANGKAH SELANJUTNYA:
    echo ========================================
    echo.
    echo 1. Daftar akun gratis di Ngrok:
    echo    https://dashboard.ngrok.com/signup
    echo.
    echo 2. Dapatkan authtoken dari:
    echo    https://dashboard.ngrok.com/get-started/your-authtoken
    echo.
    echo 3. Setup authtoken dengan command:
    echo    ngrok.exe config add-authtoken YOUR_AUTH_TOKEN
    echo.
    echo 4. Jalankan aplikasi dengan Ngrok:
    echo    start_with_ngrok.bat
    echo.
    echo ========================================
) else (
    echo.
    echo [ERROR] Gagal ekstrak Ngrok
    echo.
    echo Silakan download manual dari: https://ngrok.com/download
)

echo.
pause
