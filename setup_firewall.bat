@echo off
REM =====================================================
REM Script untuk Membuka Port 8000 di Windows Firewall
REM Jalankan sebagai Administrator (Run as Administrator)
REM =====================================================

echo.
echo ========================================
echo  Setup Firewall untuk Akses Jaringan
echo ========================================
echo.

REM Cek apakah dijalankan sebagai Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Script ini harus dijalankan sebagai Administrator!
    echo.
    echo Cara menjalankan sebagai Administrator:
    echo 1. Klik kanan file setup_firewall.bat
    echo 2. Pilih "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Membuka port 8000 untuk Laravel...
echo.

REM Hapus rule lama jika ada
netsh advfirewall firewall delete rule name="Laravel App - Port 8000" >nul 2>&1

REM Tambahkan rule baru
netsh advfirewall firewall add rule name="Laravel App - Port 8000" dir=in action=allow protocol=TCP localport=8000

if %errorLevel% equ 0 (
    echo.
    echo ========================================
    echo  SUCCESS: Firewall berhasil dikonfigurasi!
    echo ========================================
    echo.
    echo Port 8000 sudah dibuka untuk akses jaringan
    echo Aplikasi sudah bisa diakses dari device lain
    echo.
) else (
    echo.
    echo ========================================
    echo  ERROR: Gagal mengkonfigurasi firewall
    echo ========================================
    echo.
    echo Silakan coba cara manual:
    echo 1. Buka Windows Defender Firewall
    echo 2. Klik "Advanced settings"
    echo 3. Pilih "Inbound Rules" - "New Rule"
    echo 4. Pilih "Port" - TCP - 8000
    echo 5. Allow the connection
    echo.
)

pause
