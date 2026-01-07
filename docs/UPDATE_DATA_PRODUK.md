# 🔄 Tutorial Update Data Produk dari Excel Terbaru

Panduan untuk update data produk di aplikasi dengan file Excel terbaru yang berisi RAK A-G.

---

## 📋 Persiapan

### Yang Anda Butuhkan:
- ✅ File Excel terbaru: `NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx`
- ✅ Aplikasi sudah terinstall di laptop
- ✅ XAMPP (Apache + MySQL) sudah running
- ✅ Command Prompt / PowerShell

### Struktur File Excel:
File baru memiliki **7 sheet** dengan total **~600 produk**:
- **RAK A**: ~177 produk
- **RAK B**: ~45 produk  
- **RAK C**: ~114 produk (BARU!)
- **RAK D**: ~54 produk
- **RAK E**: ~77 produk (BARU!)
- **RAK F**: ~110 produk (BARU!)
- **RAK G**: ~20 produk (BARU!)

---

## 🔄 Langkah Update Data

### Metode 1: Replace Semua Data (Recommended)

Metode ini akan **menghapus semua data lama** dan import ulang dari Excel terbaru.

#### Step 1: Backup Data Lama (PENTING!)

```bash
# Buka Command Prompt di folder aplikasi
cd C:\projects\toko-obat

# Backup database (jika ada data penting)
php artisan backup
```

#### Step 2: Copy File Excel Baru

1. Pastikan file `NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx` sudah ada
2. Copy file ke folder: `C:\projects\toko-obat\docs\`
3. Ganti nama file existing jika perlu (atau hapus yang lama)

**Lokasi file:**
```
C:\projects\toko-obat\docs\NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx
```

#### Step 3: Jalankan Import Ulang

```bash
# Buka Command Prompt
cd C:\projects\toko-obat

# HATI-HATI: Perintah ini akan HAPUS SEMUA DATA dan import ulang
php artisan migrate:fresh --seed
```

**⚠️ WARNING:** Perintah `migrate:fresh` akan:
- Menghapus **semua tabel** di database
- Membuat ulang struktur database
- Import data dari Excel terbaru
- Membuat ulang user default (owner, kasir, admin gudang)

#### Step 4: Verifikasi Data Baru

1. Buka browser: http://localhost:8000
2. Login dengan user default:
   - Owner: `owner@rotua.test` / `password`
   - Kasir: `kasir@rotua.test` / `password`
   - Admin Gudang: `gudang@rotua.test` / `password`
3. Buka menu **Produk / Obat**
4. Cek apakah jumlah produk sudah ~600
5. Verifikasi lokasi rak menampilkan: RAK A - RAK G

---

### Metode 2: Update Tanpa Hapus Data Transaksi

Jika Anda **sudah punya data transaksi** dan tidak ingin hilang, gunakan metode ini:

#### Step 1: Backup Database

```bash
cd C:\projects\toko-obat
php artisan backup
```

Atau manual via phpMyAdmin:
1. Buka http://localhost/phpmyadmin
2. Pilih database `toko_obat_ro_tua`
3. Klik tab **Export**
4. Klik **Go** (simpan file .sql)

#### Step 2: Hapus Data Produk Lama (Keep Transactions)

```bash
cd C:\projects\toko-obat

# Jalankan seeder khusus tanpa hapus database
php artisan db:seed --class=ProductFromExcelSeeder
```

**❌ PROBLEM:** Metode ini akan ERROR karena ada duplicate SKU!

**✅ SOLUTION:** Gunakan script khusus untuk truncate products dulu:

```bash
# Buka tinker
php artisan tinker

# Jalankan command berikut:
DB::table('stock_movements')->delete();
DB::table('stock_batches')->delete();
DB::table('products')->delete();
exit

# Lalu jalankan seeder
php artisan db:seed --class=ProductFromExcelSeeder
```

#### Step 3: Verifikasi

1. Login ke aplikasi
2. Cek menu Produk (harus ~600 item)
3. Cek data penjualan (harus masih ada jika tidak pakai migrate:fresh)

---

## 🛠️ Troubleshooting

### Problem 1: "Excel file not found"

**Solusi:**
1. Pastikan file ada di: `C:\projects\toko-obat\docs\NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx`
2. Cek nama file (harus persis sama, termasuk spasi dan angka 4)
3. Jika nama berbeda, edit file seeder:

```php
// File: database/seeders/ProductFromExcelSeeder.php
// Baris 18:
$excelFile = database_path('../docs/NAMA ANDA.xlsx'); // Sesuaikan nama
```

### Problem 2: "Connection refused" / MySQL Error

**Solusi:**
1. Buka XAMPP Control Panel
2. Start Apache dan MySQL (harus hijau)
3. Tunggu 5 detik
4. Coba lagi

### Problem 3: Data lama masih muncul

**Solusi:**
```bash
# Clear cache aplikasi
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Refresh browser (Ctrl + F5)
```

### Problem 4: Duplicate entry error

**Solusi:**
```bash
# Hapus products dan dependencies dulu
php artisan tinker

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('stock_movements')->truncate();
DB::table('stock_batches')->truncate();
DB::table('products')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
exit

# Lalu import lagi
php artisan db:seed --class=ProductFromExcelSeeder
```

### Problem 5: Sheet tidak terbaca semua

**Verifikasi jumlah sheet:**
```bash
php artisan tinker

$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load(database_path('../docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx'));
echo "Total sheets: " . $excel->getSheetCount() . "\n";
foreach($excel->getWorksheetIterator() as $sheet) {
    echo $sheet->getTitle() . ": " . $sheet->getHighestRow() . " rows\n";
}
exit
```

Harus menampilkan 7 sheets (RAK A-G).

---

## 📊 Verifikasi Hasil Import

### Cek via Database (phpMyAdmin):

1. Buka http://localhost/phpmyadmin
2. Pilih database `toko_obat_ro_tua`
3. Klik tabel `products`
4. Query untuk cek distribusi:

```sql
-- Cek total produk
SELECT COUNT(*) as total FROM products;

-- Cek distribusi per RAK
SELECT 
    SUBSTRING(lokasi_rak, 1, 5) as rak,
    COUNT(*) as jumlah
FROM products
GROUP BY SUBSTRING(lokasi_rak, 1, 5)
ORDER BY rak;
```

**Expected result:**
```
rak    | jumlah
-------|-------
RAK A  | ~177
RAK B  | ~45
RAK C  | ~114
RAK D  | ~54
RAK E  | ~77
RAK F  | ~110
RAK G  | ~20
-------|-------
TOTAL  | ~597
```

### Cek via Aplikasi:

1. Login ke http://localhost:8000
2. Menu **Produk / Obat**
3. Scroll ke bawah, cek pagination: "Menampilkan 1-15 dari XXX data"
4. Harus ~597 produk
5. Cek kolom **Lokasi Rak** menampilkan RAK A - RAK G

### Cek via Command Line:

```bash
php artisan tinker

# Cek total
App\Models\Product::count();

# Cek per RAK
DB::table('products')
  ->selectRaw('LEFT(lokasi_rak, 5) as rak, COUNT(*) as total')
  ->groupBy('rak')
  ->orderBy('rak')
  ->get();

exit
```

---

## 🔐 Keamanan & Best Practices

### Sebelum Update Data:

1. ✅ **BACKUP DATABASE** (wajib!)
   ```bash
   php artisan backup
   ```

2. ✅ **Simpan file Excel lama** (untuk rollback jika error)

3. ✅ **Export data lama ke Excel**
   - Buka menu Produk
   - Klik "Export Excel"
   - Simpan sebagai backup

4. ✅ **Catat user & password** (jika pakai migrate:fresh)
   - Owner: owner@rotua.test / password
   - Kasir: kasir@rotua.test / password
   - Admin Gudang: gudang@rotua.test / password

### Setelah Update Data:

1. ✅ Verifikasi jumlah produk
2. ✅ Cek sample produk dari tiap RAK (A-G)
3. ✅ Test fungsi pencarian produk
4. ✅ Test transaksi penjualan (ambil sample produk)
5. ✅ Cek stock batch & movements
6. ✅ Export Excel untuk verifikasi

---

## 📝 Checklist Update

Print dan centang saat update:

```
[ ] 1. Backup database lama
[ ] 2. Export products lama ke Excel
[ ] 3. Copy file NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx ke folder docs/
[ ] 4. Stop aplikasi (jika sedang running)
[ ] 5. Jalankan: php artisan migrate:fresh --seed
[ ] 6. Tunggu selesai (~30 detik)
[ ] 7. Login dengan user default
[ ] 8. Verifikasi jumlah produk (~597)
[ ] 9. Cek sample dari RAK A, C, E, G
[ ] 10. Test pencarian produk
[ ] 11. Test transaksi
[ ] 12. Export Excel untuk verifikasi final
```

---

## ⏱️ Estimasi Waktu

- **Backup data:** 1-2 menit
- **Copy file Excel:** 10 detik
- **Import data (migrate:fresh):** 30-60 detik
- **Verifikasi:** 5 menit
- **Total:** ~10 menit

---

## 🆘 Rollback Jika Error

Jika ada masalah setelah update:

### Restore dari Backup SQL:

```bash
# Via command line
mysql -u root -p toko_obat_ro_tua < backup-2026-01-07.sql

# Via phpMyAdmin:
1. Buka phpMyAdmin
2. Pilih database toko_obat_ro_tua
3. Klik tab Import
4. Choose File -> pilih backup .sql
5. Klik Go
```

### Restore file Excel lama:

```bash
cd C:\projects\toko-obat\docs
# Copy kembali file lama
# Edit ProductFromExcelSeeder.php (ubah nama file)
php artisan migrate:fresh --seed
```

---

## 📞 Support

Jika ada masalah:

1. **Cek log error:**
   - Lokasi: `C:\projects\toko-obat\storage\logs\laravel.log`
   - Buka dengan Notepad, cari error terakhir

2. **Screenshot error** untuk bantuan

3. **Dokumentasi lengkap:**
   - [Deployment Guide](deployment-windows.md)
   - [Export Features](export-features.md)
   - [User Guide](user-guide.md)

---

**Dibuat:** Januari 2026  
**Versi:** 2.0 (Update untuk Excel dengan RAK A-G)  
**File Excel:** NAMA -NAMA OBAT DI TOKO OBAT RO TUA4.xlsx
