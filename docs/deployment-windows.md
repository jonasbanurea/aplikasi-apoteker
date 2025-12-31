# Panduan Deploy di Laptop Windows 10

Panduan ini menjelaskan cara mengambil aplikasi dari GitHub, menyiapkan lingkungan (XAMPP), menginstal dependensi, dan menjalankan aplikasi Laravel di Windows 10.

## Prasyarat
- **Git**: untuk clone repo dari GitHub.
- **XAMPP** (PHP 8.2+ dan MySQL/MariaDB): Apache + MySQL.
- **Composer**: dependency manager PHP.
- **Node.js + npm** (opsional, jika ingin build asset front-end). Versi LTS disarankan.
- **Browser**: Chrome/Edge.

## Langkah Instalasi

### 1) Siapkan Tools
1. **Install Git**: unduh dari https://git-scm.com/download/win, pilih opsi “Git from the command line”.
2. **Install XAMPP**: unduh dari https://www.apachefriends.org/, pilih paket dengan **PHP 8.2** atau lebih baru. Install ke `C:\xampp` (default).
3. **Install Composer**: unduh dari https://getcomposer.org/download/ dan saat instalasi pilih PHP di `C:\xampp\php\php.exe`.
4. (Opsional) **Install Node.js LTS**: https://nodejs.org/en (untuk compile asset bila diperlukan).

### 2) Clone Repo dari GitHub
1. Buka **Git Bash** atau **Command Prompt**.
2. Pindah ke folder kerja, contoh `C:\projects`:
   ```
   cd C:\projects
   ```
3. Clone repo:
   ```
   git clone <URL_REPO_GITHUB> toko-obat-ro-tua
   ```
4. Masuk ke folder proyek:
   ```
   cd toko-obat-ro-tua
   ```

### 3) Konfigurasi Environment
1. Duplikasi file env contoh:
   ```
   copy .env.example .env
   ```
2. Edit `.env` (Notepad/VS Code) dan sesuaikan:
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=toko_obat_ro_tua`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=` (kosong jika default XAMPP, atau isi jika Anda set password)
   - `APP_URL=http://localhost:8000` (atau http://127.0.0.1:8000)
   - Mail (opsional): jika tidak ada SMTP, set `MAIL_MAILER=log` agar email dicatat di log.

### 4) Siapkan Database
1. Jalankan **XAMPP Control Panel**, start **Apache** dan **MySQL**.
2. Buka phpMyAdmin: http://localhost/phpmyadmin.
3. Buat database baru bernama `toko_obat_ro_tua` (Collation utf8mb4_general_ci).

### 5) Install Dependency PHP
Pastikan Composer memakai PHP XAMPP (8.2). Di folder proyek:
```
composer install
```
Jika ada pesan `ext-...` missing, aktifkan extension di `C:\xampp\php\php.ini` (hapus `;` pada baris extension yang dibutuhkan), lalu restart Apache.

### 6) Generate Key & Migrasi
```
php artisan key:generate
php artisan migrate --seed
```
Seeder akan membuat data awal termasuk user default:
- Owner: owner@rotua.test / password
- Kasir: kasir@rotua.test / password
- Admin Gudang: gudang@rotua.test / password

### 7) (Opsional) Build Asset Front-end
Jika perlu recompile asset:
```
npm install
npm run build   # atau npm run dev untuk development
```
Jika tidak diperlukan, bisa lewati langkah ini (asset sudah dibundel dalam repo jika disertakan).

### 8) Jalankan Aplikasi
Gunakan PHP built-in server dari Laravel:
```
php artisan serve
```
Akses di browser: http://localhost:8000

### 9) Konfigurasi Printer Struk (Thermal 58mm)
- Cetak struk melalui halaman `sales/{id}/print`. Pastikan printer thermal sudah ter-install di Windows dan diset default atau dipilih saat print dialog.
- Struk sudah diset lebar 58mm, panjang menyesuaikan konten.

### 10) Pengiriman via ZIP (Alternatif)
Jika tidak ingin install Git di laptop client:
1. Download ZIP repo dari GitHub (`Code` → `Download ZIP`).
2. Extract ke folder, contoh `C:\projects\toko-obat-ro-tua`.
3. Lanjutkan langkah konfigurasi mulai dari **Langkah 3** (env) hingga selesai.

## Troubleshooting Cepat
- **Port 8000 bentrok**: jalankan `php artisan serve --port=8001` lalu akses http://localhost:8001.
- **DB Connection refused**: pastikan MySQL di XAMPP aktif; cek host/port/user/password di `.env`.
- **Migration gagal karena tabel sudah ada**: kosongkan DB (drop tables) lalu `php artisan migrate --seed` ulang.
- **Ext php_intl/openssl/gd missing**: aktifkan di `php.ini`, restart Apache, lalu ulangi `composer install`.
- **Email gagal (reports/email)**: set `MAIL_MAILER=log` atau isi SMTP yang valid.

## Ringkasan Perintah
```
# Clone repo
git clone <URL_REPO_GITHUB> toko-obat-ro-tua
cd toko-obat-ro-tua

# Setup
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed

# (opsional) build asset
npm install
npm run build

# Run
php artisan serve
```

Selesai. Aplikasi siap dipakai di laptop Windows 10.

---

## Jalankan Otomatis Saat Laptop Dinyalakan (Windows 10)

Goal: setiap kali laptop dinyalakan, service sudah siap dan browser membuka aplikasi full screen.

### 1) Buat Script Start Laravel
Simpan file batch, misal `start-app.bat` di folder proyek (atau lokasi lain), isi:
```
@echo off
cd /d C:\projects\toko-obat-ro-tua
REM start Apache & MySQL XAMPP (sesuaikan path xampp-control.exe)
start "" "C:\xampp\xampp-control.exe" --startapache --startmysql
REM tunggu 8 detik agar MySQL siap
timeout /t 8 /nobreak >nul
REM jalankan server Laravel di port 8000
start "" cmd /c "php artisan serve --host=0.0.0.0 --port=8000"
REM buka browser fullscreen ke app
start "" msedge --kiosk http://localhost:8000 --edge-kiosk-type=fullscreen
```
Catatan:
- Ganti path proyek dan xampp jika berbeda.
- Bisa pakai Chrome: `start "" chrome --kiosk http://localhost:8000`.
- `timeout` memberi waktu MySQL siap sebelum serve.

### 2) Tambahkan ke Startup Windows
1. Tekan `Win + R`, ketik `shell:startup`, Enter.
2. Akan terbuka folder Startup. Buat shortcut ke `start-app.bat` di folder ini.
3. Pada shortcut, klik kanan → Properties → Run: **Minimized** (agar jendela cmd tidak mengganggu).

### 3) Pastikan Apache/MySQL Otomatis
`xampp-control.exe --startapache --startmysql` pada script akan menyalakan service saat startup. Jika lebih stabil, Anda bisa set Apache/MySQL sebagai service lewat XAMPP Control Panel (SVC), lalu hilangkan baris startapache/startmysql di script.

### 4) Uji Coba
1. Reboot laptop.
2. Tunggu ±10-15 detik setelah login.
3. Browser harus otomatis terbuka fullscreen menampilkan aplikasi.
4. Jika gagal connect DB, tambah `timeout` lebih lama (misal 12 detik).

### 5) Pemulihan Manual
- Jika port 8000 dipakai, ubah port di script (misal 8001) lalu sesuaikan URL browser.
- Jika ingin mematikan, tutup browser dan stop `php artisan serve` (cmd window) atau matikan Apache/MySQL lewat XAMPP.
