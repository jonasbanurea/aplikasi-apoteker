@echo off
REM =====================================================
REM Script Setup Lengkap untuk Akses Jaringan
REM Jalankan sebagai Administrator (Run as Administrator)
REM =====================================================

echo.
echo ========================================
echo  SETUP LENGKAP - Akses Aplikasi Jaringan
echo  Toko Obat Ro Tua
echo ========================================
echo.

REM Cek apakah dijalankan sebagai Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo ==============================================
    echo  PENTING: Jalankan sebagai Administrator!
    echo ==============================================
    echo.
    echo Script ini membutuhkan akses Administrator untuk:
    echo - Membuka firewall port 8000
    echo - Setting power management
    echo.
    echo Cara menjalankan sebagai Administrator:
    echo 1. Klik kanan file setup_network_full.bat
    echo 2. Pilih "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Setup akan mengonfigurasi:
echo [1] Windows Firewall - Port 8000
echo [2] Power Management - Tidak sleep
echo [3] Menampilkan IP Address untuk akses
echo.
echo Tekan Ctrl+C untuk batalkan atau
pause

REM ==============================================
REM STEP 1: Setup Firewall
REM ==============================================
echo.
echo ========================================
echo  [STEP 1/2] Konfigurasi Firewall
echo ========================================
echo.

echo Membuka port 8000...
netsh advfirewall firewall delete rule name="Laravel App - Port 8000" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel App - Port 8000" dir=in action=allow protocol=TCP localport=8000

if %errorLevel% equ 0 (
    echo [OK] Port 8000 berhasil dibuka
) else (
    echo [ERROR] Gagal membuka port 8000
    echo Silakan buka manual melalui Windows Firewall
)

REM ==============================================
REM STEP 2: Setup Power Management
REM ==============================================
echo.
echo ========================================
echo  [STEP 2/2] Konfigurasi Power Management
echo ========================================
echo.

echo Setting High Performance mode...
powercfg /setactive 8c5e7fda-e8bf-4a96-9a85-a6e23a8c635c >nul 2>&1

echo Nonaktifkan sleep dan hibernate...
powercfg /change monitor-timeout-ac 0 >nul 2>&1
powercfg /change standby-timeout-ac 0 >nul 2>&1
powercfg /change disk-timeout-ac 0 >nul 2>&1
powercfg /change hibernate-timeout-ac 0 >nul 2>&1

if %errorLevel% equ 0 (
    echo [OK] Power management dikonfigurasi
) else (
    echo [WARNING] Gagal setting power management
)

REM ==============================================
REM STEP 3: Informasi IP Address
REM ==============================================
echo.
echo ========================================
echo  [INFO] IP Address untuk Akses
echo ========================================
echo.

REM Dapatkan IP Address otomatis
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP_ADDRESS=%%a
    goto :found_ip
)
:found_ip
set IP_ADDRESS=%IP_ADDRESS:~1%

echo IP Address laptop ini: %IP_ADDRESS%
echo.
echo ========================================
echo  SETUP SELESAI!
echo ========================================
echo.
echo Yang sudah dikonfigurasi:
echo [V] Firewall dibuka untuk port 8000
echo [V] Power management diset (tidak sleep)
echo.
echo LANGKAH SELANJUTNYA:
echo ========================================
echo.
echo 1. Jalankan aplikasi dengan double-click:
echo    start_aplikasi.bat
echo.
echo 2. Dari HP/Tablet, buka browser dan akses:
echo    http://%IP_ADDRESS%:8000
echo.
echo 3. Login dengan akun:
echo    - Kasir: kasir@rotua.test / password
echo    - Gudang: gudang@rotua.test / password
echo    - Owner: owner@rotua.test / password
echo.
echo TIPS PENTING:
echo ========================================
echo.
echo - Pastikan HP/tablet terhubung WiFi yang SAMA
echo - Jangan cabut charger laptop (biarkan colokan)
echo - Bookmark URL di browser HP agar mudah akses
echo - Generate QR Code dengan: generate_qr_code.bat
echo.
echo DOKUMENTASI LENGKAP:
echo ========================================
echo.
echo - Panduan lengkap: PANDUAN_AKSES_JARINGAN.md
echo - Quick reference: QUICK_REF_AKSES_JARINGAN.md
echo - Troubleshooting: TROUBLESHOOTING.md
echo.
echo ========================================
echo  Siap digunakan!
echo ========================================
echo.

pause
