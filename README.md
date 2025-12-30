# Toko Obat Rotua - Aplikasi Manajemen Apoteker

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

Aplikasi manajemen apoteker lengkap dengan sistem role-based access control untuk Toko Obat Rotua.

## 📋 Fitur Utama

- ✅ **Authentication System** - Login dengan role-based access
- ✅ **Role Management** - 3 level user (Owner, Kasir, Admin Gudang)
- ✅ **Bootstrap 5 UI** - Interface modern dan responsive
- ✅ **Dashboard per Role** - Dashboard khusus sesuai role user
- ✅ **Manajemen Stok** - Batch FEFO, kartu stok
- ✅ **Penerimaan/Pembelian** - Hutang sederhana + konsinyasi
- ✅ **POS Penjualan** - FEFO allocation + struk 58mm
- 📊 **Laporan** (Coming in next steps)

## 🔐 User Roles

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Owner** | owner@rotua.test | password | Full access - Manajemen user, laporan, pengaturan |
| **Kasir** | kasir@rotua.test | password | Transaksi penjualan, riwayat transaksi |
| **Admin Gudang** | gudang@rotua.test | password | Manajemen stok, obat masuk/keluar |

## 💻 Teknologi

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5.3 + Bootstrap Icons
- **Database**: MySQL
- **Authentication**: Laravel Auth (Blade)
- **Permission**: Spatie Laravel Permission
- **Server**: XAMPP / Laragon (Windows)

---

## 🚀 Instalasi (Windows - XAMPP/Laragon)

### Prasyarat

Pastikan Anda sudah menginstall:
- ✅ XAMPP atau Laragon (PHP 8.1+, MySQL, Apache)
- ✅ Composer (https://getcomposer.org/download/)
- ✅ Git (opsional, untuk clone repository)

---

### Langkah 1: Persiapan File

**Jika Anda sudah punya folder project ini**, lanjut ke Langkah 2.

**Jika clone dari Git:**
```bash
git clone <repository-url> toko-obat-rotua
cd toko-obat-rotua
```

---

### Langkah 2: Install Dependencies

Buka **Command Prompt** atau **Terminal** di folder project, lalu jalankan:

```bash
composer install
```

⏳ *Proses ini akan memakan waktu beberapa menit tergantung koneksi internet Anda.*

---

### Langkah 3: Setup File Environment

1. Copy file `.env.example` menjadi `.env`:
   ```bash
   copy .env.example .env
   ```

2. Generate Application Key:
   ```bash
   php artisan key:generate
   ```

---

### Langkah 4: Buat Database di phpMyAdmin

1. **Buka XAMPP Control Panel** atau **Laragon**
2. **Start** Apache dan MySQL
3. Buka browser dan akses: `http://localhost/phpmyadmin`
4. Klik tab **"Databases"**
5. Buat database baru dengan nama: **`toko_obat_rotua`**
   - Database name: `toko_obat_rotua`
   - Collation: `utf8mb4_unicode_ci`
6. Klik **"Create"**

![Create Database](https://via.placeholder.com/800x200/4CAF50/FFFFFF?text=Create+Database:+toko_obat_rotua)

---

### Langkah 5: Konfigurasi Database di .env

Buka file `.env` dengan text editor (Notepad++, VS Code, dll), dan pastikan konfigurasi database seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_obat_rotua
DB_USERNAME=root
DB_PASSWORD=
```

**Catatan:**
- Jika MySQL Anda menggunakan password, isi `DB_PASSWORD` sesuai password MySQL Anda
- Port default XAMPP: `3306`
- Port default Laragon: `3306`

---

### Langkah 6: Jalankan Migration

Migration akan membuat tabel-tabel di database:

```bash
php artisan migrate
```

Anda akan melihat output seperti:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table
Migrating: 0001_01_01_000001_create_cache_table
Migrated:  0001_01_01_000001_create_cache_table
...
```

✅ **Jika berhasil**, tabel-tabel sudah terbuat di database.

---

### Langkah 7: Jalankan Seeder (Data Awal)

Seeder akan mengisi database dengan data user default:

```bash
php artisan db:seed
```

Output yang muncul:
```
Roles created successfully!
Users created successfully!

Login credentials:
Owner: owner@rotua.test / password
Kasir: kasir@rotua.test / password
Admin Gudang: gudang@rotua.test / password
```

---

### Langkah 8: Jalankan Aplikasi

Jalankan Laravel development server:

```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000** atau **http://127.0.0.1:8000**

---

### Langkah 9: Login ke Aplikasi

1. Buka browser
2. Akses: **http://localhost:8000**
3. Anda akan melihat halaman login
4. Login dengan salah satu credentials:

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@rotua.test | password |
| Kasir | kasir@rotua.test | password |
| Admin Gudang | gudang@rotua.test | password |

5. Setelah login, Anda akan diarahkan ke dashboard sesuai role

---

## 📦 Step 2 - Master Data (Supplier & Produk)

### Supplier
- Kolom: `name`, `contact`, `payment_term_days` (default jatuh tempo pembayaran dalam hari)
- Catatan: Kami memilih **payment_term_days** (integer) agar jatuh tempo bisa dihitung fleksibel per faktur tanpa menyimpan tanggal tetap.

### Produk/Obat
- Kolom utama: `sku` (unique), `nama_dagang`, `nama_generik`, `bentuk`, `kekuatan_dosis`, `satuan`
- Golongan (enum): `OTC`, `BEBAS_TERBATAS`, `RESEP`, `PSIKOTROPIKA`, `NARKOTIKA`
- Boolean: `wajib_resep`, `konsinyasi`
- Harga: `harga_beli`, `harga_jual` (decimal)
- Stok: `minimal_stok`, lokasi rak: `lokasi_rak`

### Fitur CRUD & Search
- List + pagination + pencarian cepat (SKU/nama) untuk suppliers dan products
- FormRequest validation untuk semua field
- RBAC: **owner** dan **admin_gudang** bisa tambah/ubah/hapus; **kasir** read-only

> Jalankan kembali `php artisan migrate` setelah menarik update ini untuk membuat tabel baru.

---

## 🧊 Step 3 - Stok Batch & Kartu Stok

### Tabel
- `stock_batches`: product_id, batch_no, expired_date, qty_on_hand, cost_price, received_at (index product_id+expired_date untuk FEFO)
- `stock_movements`: type (IN/OUT/ADJUST), batch_id, product_id, qty, ref_type, ref_id, user_id, notes

### Fitur
- Halaman **Stok per Batch**: filter per produk, opsi near-expired (<=30 hari), FEFO sorting.
- Halaman **Kartu Stok**: mutasi masuk/keluar/adjust per produk/batch, filter tipe, ref info.
- RBAC: owner & admin_gudang bisa tambah batch dan catat mutasi; kasir read-only.

> Jalankan `php artisan migrate` untuk menambahkan tabel batch & movements.

---

## 💳 Step 4 - Pembelian / Penerimaan + Konsinyasi

### Tabel
- `purchases`: supplier_id, invoice_no, date, discount, total, due_date, status (POSTED/CONSIGNMENT), is_consignment
- `purchase_items`: purchase_id, product_id, batch_no, expired_date, qty, bonus_qty, cost_price

### Alur & Aturan
- Jatuh tempo dihitung dari `date + supplier.payment_term_days` jika non-konsinyasi; untuk konsinyasi due_date diset null.
- Konsinyasi bisa ditandai manual di form dan otomatis aktif jika ada produk dengan flag `konsinyasi`.
- Total = sum((qty + bonus_qty) * cost_price) - discount (minimal 0).

### Integrasi Stok
- Setiap item menambah/merge batch berdasarkan `product_id + batch_no` (unique), memperbarui expired_date/cost_price/received_at bila kosong.
- Qty masuk = qty + bonus_qty, menambah `qty_on_hand` batch dan mencatat `stock_movements` tipe IN dengan ref PURCHASE + user pencatat.

### RBAC
- Owner & Admin Gudang: bisa membuat penerimaan.
- Kasir: hanya lihat daftar/detail penerimaan.

> Jalankan `php artisan migrate` untuk menambahkan tabel purchases & purchase_items.

---

## 🛒 Step 5 - POS Penjualan + FEFO + Struk 58mm

### Tabel
- `sales`: invoice_no, user_id (kasir), sale_date, payment_method (CASH/NON_CASH), subtotal, discount_total, total, paid_amount, change_amount, no_resep, dokter, catatan.
- `sale_items`: sale_id, product_id, qty, price, discount (per unit), line_total.
- `sale_item_batches`: sale_item_id, stock_batch_id, qty (alokasi FEFO).

### Alur & Aturan
- POS form mendukung pencarian produk (SKU/nama), diskon per item, diskon total, metode bayar CASH/NON_CASH, dan data resep opsional (no_resep/dokter/catatan).
- Checkout berjalan dalam `DB::transaction`, invoice otomatis (`POS-yyyymmddHHMMSS-rand`).
- FEFO wajib via `FefoAllocatorService`: ambil batch ED terdekat yang belum expired, lock for update, error jika stok kurang (tidak boleh jual > stok).
- Alokasi batch disimpan di `sale_item_batches`; stok batch dikurangi dan `stock_movements` OUT dicatat per alokasi (ref SALE + user kasir).

### Struk 58mm
- Route: `/sales/{id}/print`, view 58mm dengan CSS sederhana + tombol `window.print()`.
- Memuat kasir, waktu, item, total, bayar, dan kembali.

### RBAC
- owner, admin_gudang, kasir dapat mengakses POS (index/create/store/show/print).

> Jalankan `php artisan migrate` untuk tabel sales/sale_items/sale_item_batches.

---

## 📱 Screenshot

### Login Page
- Modern login interface dengan gradient background
- Form login responsive
- Demo credentials info

### Dashboard Owner
- Total Pendapatan
- Total Transaksi
- Stok Obat
- Total User
- Grafik Penjualan
- Notifikasi

### Dashboard Kasir
- Transaksi Hari Ini
- Pendapatan Hari Ini
- Item Terjual
- Riwayat Transaksi

### Dashboard Admin Gudang
- Total Stok
- Stok Menipis
- Obat Masuk/Keluar
- Aktivitas Terakhir

---

## 🛠️ Troubleshooting

### Error: "SQLSTATE[HY000] [1049] Unknown database"
**Solusi:** Database belum dibuat. Kembali ke **Langkah 4** dan buat database `toko_obat_rotua` di phpMyAdmin.

### Error: "Class 'Spatie\Permission\...' not found"
**Solusi:** Dependencies belum terinstall. Jalankan:
```bash
composer install
```

### Error: "No application encryption key has been specified"
**Solusi:** Generate APP_KEY dengan command:
```bash
php artisan key:generate
```

### Port 8000 sudah digunakan
**Solusi:** Gunakan port lain:
```bash
php artisan serve --port=8001
```
Kemudian akses: http://localhost:8001

### MySQL di XAMPP tidak bisa start
**Solusi:** 
1. Cek apakah port 3306 sudah digunakan aplikasi lain
2. Tutup aplikasi yang menggunakan port 3306 (misal: Skype, MySQL lain)
3. Atau ubah port MySQL di XAMPP Config

---

## 📂 Struktur Project

```
toko-obat-rotua/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   └── Models/
│       └── User.php
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── permission.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── 2024_01_01_000003_create_permission_tables.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── public/
│   └── index.php
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard/
│       │   ├── owner.blade.php
│       │   ├── kasir.blade.php
│       │   ├── admin_gudang.blade.php
│       │   └── index.blade.php
│       └── layouts/
│           ├── admin.blade.php
│           └── app.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
└── README.md
```

---

## 🔄 Development Workflow

### Reset Database (Jika perlu reset dari awal)
```bash
php artisan migrate:fresh --seed
```

⚠️ **PERINGATAN:** Command ini akan **menghapus semua data** dan membuat ulang database!

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Check Routes
```bash
php artisan route:list
```

---

## 📝 TODO - Next Steps

Step 1 ✅ **COMPLETED**
- [x] Setup Laravel Project
- [x] Authentication (Login/Logout)
- [x] Role Management (Owner, Kasir, Admin Gudang)
- [x] Bootstrap 5 Admin Layout
- [x] Dashboard per Role

Step 2 ✅ **COMPLETED**
- [x] Master Data Supplier (CRUD + search)
- [x] Master Data Produk/Obat (CRUD + search)
- [x] FormRequest validation
- [x] RBAC: owner/admin_gudang manage, kasir read-only

Step 3 ✅ **COMPLETED**
- [x] Stok per batch (FEFO), near-expired filter
- [x] Kartu stok IN/OUT/ADJUST dengan ref + user

Step 4 ✅ **COMPLETED**
- [x] Penerimaan/pembelian + hutang sederhana
- [x] Flag konsinyasi (manual + auto dari produk konsinyasi)
- [x] Update batch (merge) + movement IN untuk tiap item

Step 5 ✅ **COMPLETED**
- [x] POS Penjualan dengan FEFO batch allocation
- [x] Struk 58mm (route /sales/{id}/print)
- [x] Mutasi OUT per batch + blok jual > stok

Next Steps
- [ ] Laporan & Dashboard Chart
- [ ] Notifikasi Stok Menipis/Expired
- [ ] Export/Print Report
- [ ] Deployment & hardening

---

## 👨‍💻 Author

**Toko Obat Rotua Development Team**

## 📄 License

Aplikasi ini dibuat untuk keperluan internal Toko Obat Rotua.

---

## 🆘 Bantuan

Jika ada masalah atau pertanyaan:
1. Cek bagian **Troubleshooting** di atas
2. Pastikan semua langkah instalasi sudah diikuti dengan benar
3. Pastikan XAMPP/Laragon berjalan dengan baik
4. Cek error log di `storage/logs/laravel.log`

---

**Selamat menggunakan Aplikasi Toko Obat Rotua! 🎉**
#   a p l i k a s i - a p o t e k e r  
 