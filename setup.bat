@echo off
echo ========================================
echo   Toko Obat Ro Tua - Setup Script
echo ========================================
echo.

echo [1/7] Installing Composer Dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo.

echo [2/7] Copying .env file...
if not exist .env (
    copy .env.example .env
    echo .env file created successfully!
) else (
    echo .env file already exists, skipping...
)
echo.

echo [3/7] Generating Application Key...
php artisan key:generate
echo.

echo [4/7] Please create database in phpMyAdmin:
echo    - Database name: toko_obat_rotua
echo    - Collation: utf8mb4_unicode_ci
echo.
echo After creating database, press any key to continue...
pause > nul
echo.

echo [5/7] Running Migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed! Please check your database configuration.
    pause
    exit /b 1
)
echo.

echo [6/7] Seeding Database...
php artisan db:seed
echo.

echo [7/7] Clearing Cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo ========================================
echo   Setup Complete! ✓
echo ========================================
echo.
echo Login Credentials:
echo - Owner: owner@rotua.test / password
echo - Kasir: kasir@rotua.test / password
echo - Admin Gudang: gudang@rotua.test / password
echo.
echo To start the server, run:
echo    php artisan serve
echo.
echo Then open: http://localhost:8000
echo.
pause
