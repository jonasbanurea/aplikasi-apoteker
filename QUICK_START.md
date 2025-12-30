# Quick Start Guide - Toko Obat Rotua

## Panduan Instalasi Cepat (5 Menit)

### 1️⃣ Install Dependencies
```bash
composer install
```

### 2️⃣ Setup Environment
```bash
copy .env.example .env
php artisan key:generate
```

### 3️⃣ Buat Database
- Buka phpMyAdmin: http://localhost/phpmyadmin
- Buat database baru: **toko_obat_rotua**
- Collation: utf8mb4_unicode_ci

### 4️⃣ Setup Database di .env
Edit file `.env`:
```env
DB_DATABASE=toko_obat_rotua
DB_USERNAME=root
DB_PASSWORD=
```

### 5️⃣ Migrasi Database
```bash
php artisan migrate
php artisan db:seed
```

### 6️⃣ Jalankan Server
```bash
php artisan serve
```

### 7️⃣ Login
- URL: http://localhost:8000
- **Owner:** owner@rotua.test / password
- **Kasir:** kasir@rotua.test / password  
- **Admin Gudang:** gudang@rotua.test / password

---

## ⚡ Reset Database (Fresh Install)
```bash
php artisan migrate:fresh --seed
```

## 🧹 Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📋 Check Routes
```bash
php artisan route:list
```

---

**Selamat! Aplikasi siap digunakan! 🎉**

Untuk panduan lengkap, baca [README.md](README.md)
