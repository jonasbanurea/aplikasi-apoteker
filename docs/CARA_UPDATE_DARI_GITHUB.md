# 🔄 Cara Update Aplikasi dari GitHub

Tutorial untuk download update terbaru aplikasi dari GitHub.

---

## 📋 Persiapan

**Yang Dibutuhkan:**
- Git sudah terinstall (jika pakai `git clone` sebelumnya)
- Atau bisa download manual via browser

---

## Metode 1: Via Git Pull (Recommended)

**Jika aplikasi sudah diinstall dengan Git:**

### Step 1: Backup Data Penting

```bash
cd C:\projects\toko-obat

# Backup database
php artisan backup

# Atau manual backup via phpMyAdmin
```

### Step 2: Pull Update dari GitHub

```bash
cd C:\projects\toko-obat
git pull origin main
```

**Jika muncul error "Your local changes...":**

```bash
# Simpan perubahan lokal dulu
git stash

# Pull update
git pull origin main

# Kembalikan perubahan lokal (jika perlu)
git stash pop
```

### Step 3: Update Dependencies (Jika Ada)

```bash
composer install
php artisan config:clear
php artisan cache:clear
```

### Step 4: Update Data Produk

```bash
# Double-click:
update_produk.bat

# Atau manual:
php artisan migrate:fresh --seed
```

**SELESAI!** ✅

---

## Metode 2: Download Manual (Tanpa Git)

**Jika tidak pakai Git atau ingin download ulang:**

### Step 1: Backup Data & Config Lama

**Backup hal penting:**
1. Database (via phpMyAdmin → Export)
2. File `.env` (copy ke tempat aman)
3. Folder `storage/` (jika ada upload files)

### Step 2: Download ZIP dari GitHub

1. Buka browser: https://github.com/jonasbanurea/aplikasi-apoteker
2. Klik tombol **Code** (hijau)
3. Klik **Download ZIP**
4. Extract ZIP ke folder baru: `C:\projects\toko-obat-new`

### Step 3: Copy File Penting dari Folder Lama

```bash
# Copy file .env
copy C:\projects\toko-obat\.env C:\projects\toko-obat-new\.env

# Copy storage jika ada
xcopy C:\projects\toko-obat\storage C:\projects\toko-obat-new\storage /E /I /Y
```

### Step 4: Install Dependencies

```bash
cd C:\projects\toko-obat-new
composer install
```

### Step 5: Rename Folder

```bash
# Rename folder lama
ren C:\projects\toko-obat C:\projects\toko-obat-old

# Rename folder baru jadi aktif
ren C:\projects\toko-obat-new C:\projects\toko-obat
```

### Step 6: Update Data Produk

```bash
cd C:\projects\toko-obat
update_produk.bat
```

**SELESAI!** ✅

---

## Metode 3: Update File Excel Saja (Quick)

**Jika hanya ingin update data produk tanpa update code:**

### Step 1: Download File Excel Terbaru

1. Buka: https://github.com/jonasbanurea/aplikasi-apoteker/tree/main/docs
2. Klik file: `NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx`
3. Klik tombol **Download** (atau Download raw file)
4. Simpan ke: `C:\projects\toko-obat\docs\`

### Step 2: Update Seeder (Edit Manual)

1. Buka file: `C:\projects\toko-obat\database\seeders\ProductFromExcelSeeder.php`
2. Edit baris 18, ubah menjadi:

```php
$excelFile = database_path('../docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx');
```

3. Save file

### Step 3: Import Data Baru

```bash
cd C:\projects\toko-obat
php artisan migrate:fresh --seed
```

**SELESAI!** ✅

---

## ✅ Verifikasi Update Berhasil

Cek apakah update berhasil:

### 1. Cek Versi File

```bash
cd C:\projects\toko-obat

# Cek apakah file baru ada
dir docs\NAMA*

# Harus muncul: NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx
```

### 2. Cek Script Helper

```bash
# Cek apakah script baru ada
dir *.bat

# Harus ada:
# - update_produk.bat
# - update_produk_only.bat
```

### 3. Cek Data Produk

1. Login ke aplikasi: http://localhost:8000
2. Buka menu **Produk / Obat**
3. Lihat total produk (harus ~597)
4. Cek kolom **Lokasi Rak** (harus ada RAK A-G)

### 4. Cek via Tinker

```bash
php artisan tinker

# Cek total produk
App\Models\Product::count();
# Harus: ~597

# Cek RAK yang ada
DB::table('products')
  ->selectRaw('DISTINCT LEFT(lokasi_rak, 5) as rak')
  ->orderBy('rak')
  ->pluck('rak');
# Harus: ["RAK A", "RAK B", "RAK C", "RAK D", "RAK E", "RAK F", "RAK G"]

exit
```

---

## 🐛 Troubleshooting

### Problem 1: "git command not found"

**Solusi:** Gunakan Metode 2 (Download Manual)

### Problem 2: "Your local changes would be overwritten"

**Solusi:**
```bash
# Simpan perubahan lokal
git stash

# Atau reset ke versi GitHub (HATI-HATI!)
git reset --hard origin/main
```

### Problem 3: "File Excel not found" setelah update

**Solusi:**
```bash
# Cek apakah file ada
dir "C:\projects\toko-obat\docs\NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx"

# Jika tidak ada, download manual dari GitHub
```

### Problem 4: Composer error setelah pull

**Solusi:**
```bash
composer install
composer dump-autoload
php artisan config:clear
```

---

## 📊 Update Log

**Update terakhir (Januari 2026):**
- ✅ File Excel baru dengan RAK A-G (~600 produk)
- ✅ Seeder diupdate untuk multi-sheet
- ✅ Script helper untuk update otomatis
- ✅ Dokumentasi lengkap update data
- ✅ Export functionality untuk 5 jenis data

---

## 🔄 Kapan Harus Update?

**Wajib update jika:**
- Ada file Excel baru dengan data lebih lengkap
- Ada fitur baru (export, laporan, dll)
- Ada bug fix atau security patch
- Ada perubahan database structure

**Optional update jika:**
- Hanya update dokumentasi
- Hanya perubahan kecil di UI
- Aplikasi sudah berjalan normal

---

## 📞 Bantuan

**Jika ada masalah saat update:**

1. **Cek log error:** `storage\logs\laravel.log`
2. **Restore backup:** Jika gagal, kembalikan database dari backup
3. **Rollback Git:**
   ```bash
   git log --oneline  # Lihat commit history
   git reset --hard COMMIT_HASH  # Kembali ke commit tertentu
   ```

---

**Dibuat:** Januari 2026  
**Last Update:** Januari 8, 2026  
**GitHub:** https://github.com/jonasbanurea/aplikasi-apoteker
