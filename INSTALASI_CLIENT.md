# 📦 Panduan Instalasi untuk Laptop Client

Panduan singkat dan mudah untuk menginstal Aplikasi Toko Obat Ro Tua di laptop client Windows 10/11.

## ✅ Checklist Persiapan

Sebelum mulai, pastikan sudah memiliki:
- [ ] Laptop/PC Windows 10 atau 11
- [ ] Koneksi internet (untuk download tools)
- [ ] Hak akses Administrator
- [ ] Minimal 5 GB ruang kosong di hard disk

---

## 📥 Langkah 1: Download & Install Tools

### 1.1. Install XAMPP (Apache + MySQL + PHP)
1. Download XAMPP dari: https://www.apachefriends.org/
2. Pilih versi **PHP 8.2** atau lebih baru
3. Jalankan installer, install ke `C:\xampp` (default)
4. Tunggu sampai selesai (±5-10 menit)

### 1.2. Install Composer (PHP Package Manager)
1. Download Composer dari: https://getcomposer.org/download/
2. Saat instalasi, pilih PHP dari: `C:\xampp\php\php.exe`
3. Ikuti wizard sampai selesai

### 1.3. Install Git (Opsional, untuk update aplikasi)
1. Download Git dari: https://git-scm.com/download/win
2. Install dengan opsi default
3. Pilih opsi "Git from the command line"

---

## 📂 Langkah 2: Download Aplikasi

### Pilihan A: Via Git (Recommended)
```bash
# Buka Command Prompt atau Git Bash
cd C:\
mkdir projects
cd projects
git clone https://github.com/jonasbanurea/aplikasi-apoteker.git toko-obat
cd toko-obat
```

### Pilihan B: Download ZIP
1. Buka: https://github.com/jonasbanurea/aplikasi-apoteker
2. Klik tombol **Code** → **Download ZIP**
3. Extract ZIP ke `C:\projects\toko-obat`

---

## ⚙️ Langkah 3: Konfigurasi PHP Extensions

**PENTING!** Aplikasi butuh extension PHP untuk import/export Excel.

1. Buka file: `C:\xampp\php\php.ini` dengan Notepad
2. Cari dan hapus `;` (titik koma) di awal baris berikut:

```ini
;extension=gd        → hapus ; menjadi: extension=gd
;extension=zip       → hapus ; menjadi: extension=zip
;extension=intl      → hapus ; menjadi: extension=intl
;extension=openssl   → hapus ; menjadi: extension=openssl
```

3. Save file
4. **Restart XAMPP** (Apache harus di-restart)

### Cara Restart XAMPP:
1. Buka **XAMPP Control Panel**
2. Jika Apache/MySQL running, klik **Stop**
3. Tunggu 3 detik
4. Klik **Start** lagi

---

## 🗄️ Langkah 4: Setup Database

1. Buka **XAMPP Control Panel**
2. Start **Apache** dan **MySQL**
3. Buka browser, akses: http://localhost/phpmyadmin
4. Klik tab **Database**
5. Buat database baru:
   - Nama: `toko_obat_ro_tua`
   - Collation: `utf8mb4_general_ci`
6. Klik **Create**

---

## 🔧 Langkah 5: Konfigurasi Aplikasi

1. Buka folder aplikasi: `C:\projects\toko-obat`
2. Copy file `.env.example` menjadi `.env`
3. **Buat folder storage** (PENTING!):
   - Buka Command Prompt di folder aplikasi
   - Jalankan:
   ```bash
   mkdir storage\framework\sessions
   mkdir storage\framework\views
   mkdir storage\framework\cache\data
   mkdir storage\logs
   ```
4. Edit file `.env` dengan Notepad, ubah:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_obat_ro_tua
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://localhost:8000
MAIL_MAILER=log
```

**Tips:** Jika MySQL XAMPP Anda pakai password, isi `DB_PASSWORD=passwordanda`

---

## 📦 Langkah 6: Install Dependencies

Buka **Command Prompt** di folder aplikasi:

```bash
cd C:\projects\toko-obat
composer install
```

Tunggu sampai selesai (±3-5 menit). Jika muncul error:
- Pastikan extension PHP sudah diaktifkan (Langkah 3)
- Restart Command Prompt
- Coba lagi

---

## 🚀 Langkah 7: Generate Key & Import Data

Jalankan perintah berikut satu per satu:

```bash
# Generate application key
php artisan key:generate

# Buat tabel database dan import data awal
php artisan migrate --seed
```

**Seeder akan membuat:**
- ✅ 3 user default (Owner, Kasir, Admin Gudang)
- ✅ 40+ produk obat dari Excel
- ✅ Stock batch dan stock movement

**User Default:**
- Owner: `owner@rotua.test` / `password`
- Kasir: `kasir@rotua.test` / `password`
- Admin Gudang: `gudang@rotua.test` / `password`

---

## 🌐 Langkah 8: Jalankan Aplikasi

### Cara Manual (untuk testing):
```bash
php artisan serve
```
Akses: http://localhost:8000

### Cara Otomatis (untuk produksi):
1. Buka folder aplikasi
2. Double-click file: `start_aplikasi.bat`
3. Tunggu ±15 detik, browser akan otomatis terbuka

**Edit `start_aplikasi.bat` untuk sesuaikan:**
- Path aplikasi (baris 7)
- Path XAMPP (baris 10)
- Port (baris 13)
- Browser: chrome atau msedge (baris 16)

---

## 🔄 Langkah 9: Setup Auto-Start (Opsional)

Agar aplikasi otomatis jalan saat laptop dinyalakan:

1. Tekan `Win + R`
2. Ketik: `shell:startup`
3. Enter
4. Copy file `start_aplikasi.bat` ke folder yang terbuka
5. Atau buat shortcut ke `start_aplikasi.bat`

**Pengaturan Shortcut:**
- Klik kanan shortcut → Properties
- Run: **Minimized**
- OK

---

## ✅ Verifikasi Instalasi

Cek apakah semua berjalan dengan benar:

1. ✅ **XAMPP Control Panel**: Apache & MySQL berwarna hijau
2. ✅ **Browser**: http://localhost:8000 terbuka
3. ✅ **Login**: Bisa login dengan user default
4. ✅ **Produk**: Menu Produk menampilkan 40+ item
5. ✅ **Export**: Tombol "Export Excel" berfungsi
6. ✅ **Stock**: Stock per batch terlihat

---

## 🐛 Troubleshooting

### Problem: "Port 8000 already in use"
**Solusi:**
```bash
php artisan serve --port=8001
```
Atau edit `start_aplikasi.bat`, ubah `APP_PORT=8001`

### Problem: "Connection refused" saat akses database
**Solusi:**
1. Pastikan MySQL di XAMPP running (hijau)
2. Cek `.env`: DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD
3. Test koneksi via phpMyAdmin

### Problem: "Extension gd not found"
**Solusi:**
1. Edit `C:\xampp\php\php.ini`
2. Aktifkan: `extension=gd` dan `extension=zip`
3. Restart Apache di XAMPP
4. Jalankan `composer install` lagi

### Problem: "Class 'ProductsExport' not found"
**Solusi:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Problem: "Failed to open stream: No such file or directory" (storage/framework/sessions)
**Solusi:**
```bash
cd C:\projects\toko-obat
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\framework\cache\data
mkdir storage\logs
```

### Problem: Seeder error "Excel file not found"
**Solusi:**
1. Pastikan file `docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx` ada
2. Jika tidak ada, skip seeder produk:
```bash
# Edit database/seeders/DatabaseSeeder.php
# Comment baris: ProductFromExcelSeeder::class,
php artisan migrate:fresh --seed
```

### Problem: Pagination icon terlalu besar
**Solusi:** Sudah diperbaiki di versi terbaru. Clear cache:
```bash
php artisan view:clear
php artisan config:clear
```

---

## 📞 Support & Bantuan

### Dokumentasi Lengkap:
- [Deployment Windows](docs/deployment-windows.md)
- [Import/Export Products](docs/import-export-products.md)
- [Excel Format Guide](docs/excel-import-format.md)
- [User Guide](docs/user-guide.md)

### Command Berguna:
```bash
# Reset semua data (hati-hati!)
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Import produk tambahan
php artisan db:seed --class=ProductFromExcelSeeder

# Cek versi PHP
php -v

# Cek extension PHP
php -m | findstr "gd zip"
```

---

## 📋 Checklist Akhir

Sebelum digunakan di toko, pastikan:
- [ ] Semua extension PHP aktif
- [ ] Database terisi dengan benar
- [ ] User default bisa login
- [ ] Produk tampil (40+ items)
- [ ] Export Excel berfungsi
- [ ] Printer thermal sudah terinstall
- [ ] Auto-start berfungsi (jika diaktifkan)
- [ ] Backup database sudah diatur
- [ ] Password user sudah diganti (security)

---

## 🎉 Selamat!

Aplikasi Toko Obat Ro Tua sudah siap digunakan!

**Login Pertama:**
1. Buka: http://localhost:8000
2. Login sebagai Owner: `owner@rotua.test` / `password`
3. Ganti password di menu Profile
4. Mulai gunakan aplikasi

**Tips Keamanan:**
- ⚠️ Ganti semua password default
- ⚠️ Backup database secara berkala
- ⚠️ Jangan expose port ke internet

---

**Dibuat:** Januari 2026  
**Versi:** 1.0  
**GitHub:** https://github.com/jonasbanurea/aplikasi-apoteker
