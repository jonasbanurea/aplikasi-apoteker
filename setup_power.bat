@echo off
REM =====================================================
REM Script untuk Setting Power Management
REM Agar laptop tidak sleep saat digunakan sebagai server
REM Jalankan sebagai Administrator (Run as Administrator)
REM =====================================================

echo.
echo ========================================
echo  Setup Power Management untuk Server
echo ========================================
echo.

REM Cek apakah dijalankan sebagai Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Script ini harus dijalankan sebagai Administrator!
    echo.
    echo Cara menjalankan sebagai Administrator:
    echo 1. Klik kanan file setup_power.bat
    echo 2. Pilih "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Mengonfigurasi power settings...
echo.

REM Set power scheme ke High Performance
powercfg /setactive 8c5e7fda-e8bf-4a96-9a85-a6e23a8c635c

REM AC = Saat laptop colokan listrik (plugged in)
REM DC = Saat laptop pakai baterai

echo [1/6] Setting monitor timeout...
REM Monitor tetap hidup (tidak mati) saat plugged in
powercfg /change monitor-timeout-ac 0

echo [2/6] Setting sleep timeout...
REM Laptop tidak sleep saat plugged in
powercfg /change standby-timeout-ac 0

echo [3/6] Setting disk timeout...
REM Hard disk tidak mati saat plugged in
powercfg /change disk-timeout-ac 0

echo [4/6] Setting hibernate...
REM Disable hibernate
powercfg /change hibernate-timeout-ac 0

echo [5/6] Setting USB selective suspend...
REM Disable USB selective suspend untuk stabilitas
powercfg /setacvalueindex SCHEME_CURRENT 2a737441-1930-4402-8d77-b2bebba308a3 48e6b7a6-50f5-4782-a5d4-53bb8f07e226 0

echo [6/6] Applying settings...
powercfg /setactive SCHEME_CURRENT

echo.
echo ========================================
echo  SUCCESS: Power settings dikonfigurasi!
echo ========================================
echo.
echo Perubahan yang dilakukan:
echo - Power scheme: High Performance
echo - Monitor: Tidak akan mati
echo - Sleep: Dinonaktifkan (saat colokan listrik)
echo - Hard disk: Tetap aktif
echo - Hibernate: Dinonaktifkan
echo - USB: Selective suspend dinonaktifkan
echo.
echo CATATAN PENTING:
echo - Setting ini hanya berlaku saat laptop COLOKAN LISTRIK
echo - Saat pakai baterai, setting normal masih berlaku
echo - Jika laptop dipakai sebagai server, JANGAN cabut charger
echo.
echo SETTING LID (Tutup Laptop):
echo Untuk setting apa yang terjadi saat tutup laptop:
echo 1. Buka Settings ^> System ^> Power
echo 2. Klik "Additional power settings"
echo 3. Klik "Choose what closing the lid does"
echo 4. Set "When I close the lid (Plugged in)" ke "Do nothing"
echo 5. Klik Save changes
echo.

pause
