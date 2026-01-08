@echo off
chcp 65001 > nul
title Cek Status MySQL - Toko Obat Ro Tua
color 0A

echo.
echo ═══════════════════════════════════════════════════════════════
echo   CEK STATUS MySQL - TOKO OBAT RO TUA
echo ═══════════════════════════════════════════════════════════════
echo.

:: Cek apakah MySQL service berjalan
echo [1/4] Cek MySQL Service...
sc query MySQL > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    sc query MySQL | find "RUNNING" > nul
    if %ERRORLEVEL% EQU 0 (
        echo ✅ MySQL Service: RUNNING
    ) else (
        echo ❌ MySQL Service: TIDAK RUNNING
        echo    ^> Solusi: Buka XAMPP/Laragon, klik Start MySQL
    )
) else (
    echo ℹ️  MySQL Service: Tidak terdaftar sebagai Windows Service
    echo    ^> Ini normal untuk XAMPP/Laragon portable
)

echo.
echo [2/4] Cek Port 3306...
netstat -ano | findstr ":3306" > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo ✅ Port 3306: ADA APLIKASI YANG MENGGUNAKAN
    echo    Detail:
    netstat -ano | findstr ":3306"
) else (
    echo ❌ Port 3306: TIDAK ADA YANG MENGGUNAKAN
    echo    ^> MySQL kemungkinan TIDAK BERJALAN
    echo    ^> Solusi: Start MySQL di XAMPP/Laragon Control Panel
)

echo.
echo [3/4] Cek Koneksi ke Database...

:: Cari mysqladmin
set MYSQL_ADMIN=
if exist "C:\xampp\mysql\bin\mysqladmin.exe" (
    set MYSQL_ADMIN=C:\xampp\mysql\bin\mysqladmin.exe
) else if exist "C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqladmin.exe" (
    set MYSQL_ADMIN=C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqladmin.exe
) else (
    for /d %%i in (C:\laragon\bin\mysql\*) do (
        if exist "%%i\bin\mysqladmin.exe" (
            set MYSQL_ADMIN=%%i\bin\mysqladmin.exe
        )
    )
)

if not defined MYSQL_ADMIN (
    echo ⚠️  mysqladmin.exe tidak ditemukan
    echo    Cek manual di XAMPP/Laragon Control Panel
    goto test_phpmyadmin
)

"%MYSQL_ADMIN%" -u root ping 2>nul | find "alive" > nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ Koneksi Database: BERHASIL
    echo    MySQL server is alive!
) else (
    echo ❌ Koneksi Database: GAGAL
    echo    ^> MySQL tidak merespon
    echo    ^> Solusi: Start MySQL di XAMPP/Laragon Control Panel
)

:test_phpmyadmin
echo.
echo [4/4] Cek phpMyAdmin...
echo    Membuka browser ke http://localhost/phpmyadmin
echo    Jika muncul halaman phpMyAdmin = MySQL OK ✅
echo    Jika error "Cannot connect" = MySQL masih mati ❌
timeout /t 2 > nul
start http://localhost/phpmyadmin

echo.
echo ═══════════════════════════════════════════════════════════════
echo   HASIL DIAGNOSA
echo ═══════════════════════════════════════════════════════════════
echo.
echo Jika semua ✅ = MySQL berjalan normal, backup bisa dilakukan
echo Jika ada ❌ = MySQL bermasalah, ikuti solusi di atas
echo.
echo ═══════════════════════════════════════════════════════════════
echo.
pause
