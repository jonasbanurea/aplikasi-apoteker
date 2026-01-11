# Toko Obat Ro Tua - Aplikasi Manajemen Apotek

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

Aplikasi manajemen apotek dengan RBAC (Owner, Kasir, Admin Gudang), POS FEFO, pembelian, stok, laporan, dan backup.

**📚 [INDEX_DOKUMENTASI.md](INDEX_DOKUMENTASI.md)** - Panduan navigasi lengkap semua dokumentasi

## Fitur Utama
- Autentikasi & RBAC (Spatie Permission), proteksi user non-aktif
- Dashboard per role dengan grafik, top produk, alert stok/expired
- Master data supplier & produk (import/export Excel)
- Pembelian/penerimaan + batch merge, konsinyasi, hutang sederhana
- Stok per batch, kartu stok (IN/OUT/ADJUST), stock opname + approval
- POS penjualan FEFO, struk thermal 58mm
- Laporan (PDF, email) dan audit log aksi penting
- Backup zip (DB + asset) ke Documents

## Akun Default
| Role | Email | Password |
|---|---|---|
| Owner | owner@rotua.test | password |
| Kasir | kasir@rotua.test | password |
| Admin Gudang | gudang@rotua.test | password |

## Instalasi Cepat (Windows + XAMPP)
1) Clone / ekstrak repo, masuk folder project.
2) `copy .env.example .env` lalu edit DB di `.env` (db name/user/pass). Opsional: `MAIL_MAILER=log`.
3) Start Apache & MySQL (XAMPP), buat DB `toko_obat_ro_tua`.
4) `composer install`
5) `php artisan key:generate`
6) `php artisan migrate --seed`
7) (Opsional) `npm install && npm run build`
8) `php artisan serve` lalu buka http://localhost:8000

## Perintah Berguna
- Reset data uji: `php artisan migrate:fresh --seed`
- Import produk dari Excel: `php artisan db:seed --class=ProductFromExcelSeeder`
- Clear cache: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
- Jalankan di port lain: `php artisan serve --port=8001`

## Struktur Singkat
```
app/Http/Controllers (Dashboard, POS, Stok, Pembelian, Backup, Auth)
app/Models (User, Product, StockBatch, Sale, Purchase, dll)
database/migrations (tabel user/role, produk, stok, purchase, sale, audit_log)
resources/views (layouts, dashboard per role, POS, stok, opname, laporan, backup)
routes/web.php (RBAC, backup owner-only)
config/stock.php, reports.php, mail.php
```

## Akses Aplikasi dari Device Lain

### 📱 Akses Lokal (WiFi - Recommended untuk Operasional)

**Untuk akses dari HP/Tablet dalam toko (WiFi sama):**

Ikuti panduan lengkap di [PANDUAN_AKSES_JARINGAN.md](PANDUAN_AKSES_JARINGAN.md) yang mencakup:
1. ✅ Set IP Static di Windows 11
2. ✅ Konfigurasi Firewall (script otomatis tersedia)
3. ✅ Cara akses dari device lain
4. ✅ Troubleshooting koneksi

**Quick Start:**
```bash
# 1. Setup lengkap (Run as Administrator)
setup_network_full.bat

# 2. Jalankan aplikasi
start_aplikasi.bat

# 3. Akses dari HP/tablet - gunakan URL yang ditampilkan
# Contoh: http://192.168.1.100:8000
```

### 🌐 Akses Internet (Dari Mana Saja - Optional)

**Untuk akses dari luar toko (owner monitoring dari rumah):**

Lihat [PANDUAN_AKSES_INTERNET.md](PANDUAN_AKSES_INTERNET.md) untuk berbagai opsi:
1. 🚀 **Ngrok** - Termudah untuk demo/testing (5 menit setup)
2. ☁️ **Cloudflare Tunnel** - Gratis & aman untuk production
3. 🔐 **VPN** - Paling aman untuk owner
4. 🌍 **Cloud Hosting** - Production grade dengan biaya bulanan

**Quick Start Ngrok:**
```bash
# 1. Download & install
download_ngrok.bat

# 2. Daftar & dapatkan authtoken di ngrok.com
# 3. Setup authtoken
ngrok.exe config add-authtoken YOUR_TOKEN

# 4. Jalankan dengan Ngrok
start_with_ngrok.bat
```

**Perbandingan:** Lihat [PERBANDINGAN_AKSES.md](PERBANDINGAN_AKSES.md) untuk memilih metode yang tepat.

---

## Backup (Owner Menu)
- Menu: Owner → Backup
- Hasil: ZIP berisi database.sql + public + storage/app/public ke Documents (default)
- **Jika backup gagal dengan error "Can't create TCP/IP socket":**
  1. Buka XAMPP/Laragon Control Panel
  2. Pastikan MySQL service sedang **Running** (hijau)
  3. Jika belum, klik **Start** untuk MySQL
  4. Atau jalankan: `cek_mysql_status.bat` untuk diagnosa
  5. Lihat panduan lengkap: [TROUBLESHOOTING_BACKUP.md](TROUBLESHOOTING_BACKUP.md)
- Set `MYSQLDUMP_PATH` di `.env` jika diperlukan (contoh: `C:\xampp\mysql\bin\mysqldump.exe`)

## Import & Export Data Produk
- Import dari Excel: File Excel harus disimpan di folder `docs/` dengan format kolom yang sesuai (NO, NAMA BARANG, SEDIAAN, LOK BARANG, STOK, KATEGORI, HRG BELI, MARGIN, HRG JUAL, EXP DATE)
- Export ke Excel: Klik tombol "Export Excel" di halaman Produk untuk mengunduh daftar produk terbaru
- File contoh: `docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx`
- **Panduan penamaan SKU**: Lihat [PANDUAN_SKU_PRODUK.md](docs/PANDUAN_SKU_PRODUK.md) untuk format dan contoh penamaan SKU yang benar

## Troubleshooting Ringkas
- DB unknown: pastikan DB dibuat dan .env sesuai.
- Key missing: `php artisan key:generate`.
- **Backup gagal (Error 10106)**: MySQL tidak berjalan → start MySQL di XAMPP/Laragon → lihat [TROUBLESHOOTING_BACKUP.md](TROUBLESHOOTING_BACKUP.md)
- mysqldump not found: set `MYSQLDUMP_PATH` atau pastikan XAMPP bin di PATH.
- Login ditolak: user mungkin non-aktif (`is_active=false`).

## Utilitas Helper (Batch Scripts)

### Setup & Konfigurasi
- `setup_network_full.bat` - **[RUN AS ADMIN]** Setup lengkap firewall + power management untuk akses lokal
- `setup_firewall.bat` - **[RUN AS ADMIN]** Setup firewall Windows untuk akses dari device lain
- `setup_power.bat` - **[RUN AS ADMIN]** Konfigurasi power management (laptop tidak sleep)
- `setup.bat` - Setup awal aplikasi (install dependencies, migrate, seed)

### Jalankan Aplikasi
- `start_aplikasi.bat` - Start aplikasi dengan satu klik (sudah support akses jaringan)
- `start_mysql_and_app.bat` - Auto-start MySQL dan aplikasi
- `start_with_ngrok.bat` - Start aplikasi dengan Ngrok (akses dari internet)

### Testing & Maintenance
- `test_network.bat` - Test koneksi jaringan dan verifikasi setup
- `cek_mysql_status.bat` - Diagnosa status MySQL dan koneksi database
- `update_produk.bat` - Update data produk dari Excel dengan backup otomatis
- `update_aplikasi.bat` - Update aplikasi dari GitHub (untuk customer) ⭐ NEW

### Utilities
- `download_ngrok.bat` - Download Ngrok untuk akses internet
- `generate_qr_code.bat` - Generate instruksi QR Code untuk akses mudah

### Dokumentasi Akses
- 📘 **`PANDUAN_AKSES_JARINGAN.md`** - Panduan lengkap akses dari device lain (lokal/WiFi)
- 🌐 **`PANDUAN_AKSES_INTERNET.md`** - Panduan lengkap akses dari internet (luar jaringan)
- 📊 **`DIAGRAM_AKSES_JARINGAN.md`** - Diagram dan flow chart arsitektur jaringan
- 📱 **`QUICK_REF_AKSES_JARINGAN.md`** - Quick reference card untuk kasir/staff
- 🔄 **`PERBANDINGAN_AKSES.md`** - Perbandingan metode akses lokal vs internet
- 🎯 **`DECISION_TREE_AKSES.md`** - Decision tree untuk memilih metode akses yang tepat
- ❓ **`FAQ_AKSES_INTERNET.md`** - Frequently Asked Questions tentang akses internet
- 🔒 **`SECURITY_CHECKLIST.md`** - Checklist keamanan untuk akses internet

### Dokumentasi Update & Maintenance
- 🔄 **`PANDUAN_UPDATE_CUSTOMER.md`** - Panduan update aplikasi untuk customer ⭐ NEW
- 📝 **`CARA_UPDATE_DARI_GITHUB.md`** - Panduan update lengkap dari GitHub
- ✏️ **`CARA_EDIT_EXPIRED_DATE.md`** - Cara edit tanggal expired obat ⭐ NEW

## 🆕 Update Terbaru (11 Januari 2026)

### Fitur Baru:
- ✅ **Edit Stock Batch**: Edit expired date, qty, cost price langsung
- ✅ **Filter Stock Batch**: Filter per produk dan mendekati expired
- ✅ **Update Script**: Script otomatis update aplikasi dari GitHub

### Cara Update Aplikasi:
```bash
# Cara termudah (double-click):
update_aplikasi.bat

# Atau manual:
cd C:\projects\toko-obat
git pull origin main
php artisan config:clear
php artisan cache:clear
```

**Detail lengkap:** Lihat [PANDUAN_UPDATE_CUSTOMER.md](docs/PANDUAN_UPDATE_CUSTOMER.md)

## Lisensi
Aplikasi internal Toko Obat Ro Tua.