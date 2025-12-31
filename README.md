# Toko Obat Ro Tua - Aplikasi Manajemen Apotek

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

Aplikasi manajemen apotek dengan RBAC (Owner, Kasir, Admin Gudang), POS FEFO, pembelian, stok, laporan, dan backup.

## Fitur Utama
- Autentikasi & RBAC (Spatie Permission), proteksi user non-aktif
- Dashboard per role dengan grafik, top produk, alert stok/expired
- Master data supplier & produk
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

## Backup (Owner Menu)
- Menu: Owner → Backup
- Hasil: ZIP berisi database.sql + public + storage/app/public ke Documents (default). Set `MYSQLDUMP_PATH` jika diperlukan (contoh: `C:\xampp\mysql\bin\mysqldump.exe`).

## Troubleshooting Ringkas
- DB unknown: pastikan DB dibuat dan .env sesuai.
- Key missing: `php artisan key:generate`.
- mysqldump not found: set `MYSQLDUMP_PATH` atau pastikan XAMPP bin di PATH.
- Login ditolak: user mungkin non-aktif (`is_active=false`).

## Lisensi
Aplikasi internal Toko Obat Ro Tua.