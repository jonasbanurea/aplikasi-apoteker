@echo off
REM =====================================================
REM Script untuk Test Koneksi Jaringan
REM =====================================================

echo.
echo ========================================
echo  Test Koneksi Jaringan
echo  Toko Obat Ro Tua
echo ========================================
echo.

REM Dapatkan IP Address otomatis
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP_ADDRESS=%%a
    goto :found_ip
)
:found_ip
set IP_ADDRESS=%IP_ADDRESS:~1%

echo [INFO] IP Address laptop ini: %IP_ADDRESS%
echo.

REM ==============================================
REM TEST 1: Cek Network Adapter
REM ==============================================
echo ========================================
echo  [TEST 1/5] Network Adapter Status
echo ========================================
echo.

ipconfig | findstr /C:"IPv4" /C:"Subnet" /C:"Default Gateway"

echo.

REM ==============================================
REM TEST 2: Cek Firewall Rules
REM ==============================================
echo ========================================
echo  [TEST 2/5] Firewall Rules - Port 8000
echo ========================================
echo.

netsh advfirewall firewall show rule name="Laravel App - Port 8000" >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Firewall rule untuk port 8000 DITEMUKAN
    netsh advfirewall firewall show rule name="Laravel App - Port 8000"
) else (
    echo [WARNING] Firewall rule untuk port 8000 TIDAK DITEMUKAN
    echo.
    echo Solusi: Jalankan setup_firewall.bat sebagai Administrator
)

echo.

REM ==============================================
REM TEST 3: Cek Port 8000 sedang digunakan
REM ==============================================
echo ========================================
echo  [TEST 3/5] Port 8000 Status
echo ========================================
echo.

netstat -ano | findstr :8000 >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Port 8000 sedang DIGUNAKAN (aplikasi berjalan)
    echo.
    netstat -ano | findstr :8000
) else (
    echo [WARNING] Port 8000 TIDAK DIGUNAKAN (aplikasi belum berjalan)
    echo.
    echo Solusi: Jalankan start_aplikasi.bat
)

echo.

REM ==============================================
REM TEST 4: Cek MySQL Service
REM ==============================================
echo ========================================
echo  [TEST 4/5] MySQL Service Status
echo ========================================
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL service BERJALAN
) else (
    echo [WARNING] MySQL service TIDAK BERJALAN
    echo.
    echo Solusi: Start MySQL di XAMPP/Laragon Control Panel
)

echo.

REM ==============================================
REM TEST 5: Generate Test Instructions
REM ==============================================
echo ========================================
echo  [TEST 5/5] Instruksi Test dari Device Lain
echo ========================================
echo.

echo Untuk test dari HP/Tablet/Laptop lain:
echo.
echo 1. Pastikan device terhubung ke WiFi yang SAMA
echo.
echo 2. Buka Command Prompt atau Terminal di device tersebut
echo.
echo 3. Test PING ke laptop server:
echo    ping %IP_ADDRESS%
echo.
echo    Jika berhasil, akan muncul "Reply from %IP_ADDRESS%"
echo    Jika gagal, muncul "Request timed out"
echo.
echo 4. Buka browser dan akses:
echo    http://%IP_ADDRESS%:8000
echo.
echo    Jika berhasil, akan muncul halaman login aplikasi
echo.

REM ==============================================
REM SUMMARY
REM ==============================================
echo.
echo ========================================
echo  RINGKASAN
echo ========================================
echo.

set ISSUES=0

REM Check network
ipconfig | findstr /C:"IPv4" >nul 2>&1
if %errorLevel% neq 0 (
    echo [X] Network adapter: TIDAK TERHUBUNG
    set /a ISSUES+=1
) else (
    echo [V] Network adapter: OK
)

REM Check firewall
netsh advfirewall firewall show rule name="Laravel App - Port 8000" >nul 2>&1
if %errorLevel% neq 0 (
    echo [X] Firewall port 8000: BELUM DIBUKA
    set /a ISSUES+=1
) else (
    echo [V] Firewall port 8000: OK
)

REM Check port usage
netstat -ano | findstr :8000 >nul 2>&1
if %errorLevel% neq 0 (
    echo [X] Laravel server: BELUM BERJALAN
    set /a ISSUES+=1
) else (
    echo [V] Laravel server: OK
)

REM Check MySQL
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [V] MySQL service: OK
) else (
    echo [X] MySQL service: BELUM BERJALAN
    set /a ISSUES+=1
)

echo.

if %ISSUES% gtr 0 (
    echo ========================================
    echo  STATUS: ADA MASALAH ^(%ISSUES% issues^)
    echo ========================================
    echo.
    echo Silakan perbaiki masalah di atas sebelum test akses
    echo.
    echo Quick Fix:
    echo 1. Jalankan: setup_network_full.bat ^(as Admin^)
    echo 2. Jalankan: start_aplikasi.bat
    echo 3. Jalankan script ini lagi untuk verifikasi
) else (
    echo ========================================
    echo  STATUS: SEMUA OK! SIAP DIGUNAKAN
    echo ========================================
    echo.
    echo Aplikasi sudah siap diakses dari device lain!
    echo.
    echo URL Akses:
    echo http://%IP_ADDRESS%:8000
    echo.
    echo Silakan coba akses dari HP/tablet Anda
)

echo.
echo ========================================

pause
