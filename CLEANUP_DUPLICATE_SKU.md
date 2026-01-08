# Panduan Membersihkan Duplikasi SKU

## Jika Terjadi Error Duplicate SKU

### Langkah 1: Cek Data Duplikat
```sql
-- Cek SKU yang duplikat
SELECT sku, COUNT(*) as jumlah 
FROM products 
GROUP BY sku 
HAVING COUNT(*) > 1;

-- Cek produk dengan nama sama
SELECT nama_dagang, COUNT(*) as jumlah 
FROM products 
GROUP BY nama_dagang 
HAVING COUNT(*) > 1;
```

### Langkah 2: Hapus Data Duplikat (HATI-HATI!)

**Opsi A: Hapus SEMUA data produk dan mulai fresh**
```sql
-- PERINGATAN: Ini akan menghapus SEMUA data produk!
TRUNCATE TABLE stock_movements;
TRUNCATE TABLE stock_batches;
TRUNCATE TABLE products;

-- Reset auto increment
ALTER TABLE products AUTO_INCREMENT = 1;
```

**Opsi B: Hapus produk duplikat saja (berdasarkan ID tertinggi)**
```sql
-- Backup dulu!
CREATE TABLE products_backup AS SELECT * FROM products;

-- Hapus duplikat, simpan yang ID terkecil (yang pertama diinsert)
DELETE p1 FROM products p1
INNER JOIN products p2 
WHERE 
    p1.sku = p2.sku
    AND p1.id > p2.id;
```

### Langkah 3: Jalankan Ulang Seeder
```bash
cd C:\Aplikasi-apoteker
php artisan db:seed --class=ProductFromExcelSeeder
```

## Mencegah Duplikasi di Masa Depan

Seeder sudah diperbaiki dengan fitur:
1. ✅ Cek SKU terakhir di database
2. ✅ Validasi SKU sebelum insert
3. ✅ Skip produk yang sudah ada (by nama)
4. ✅ Transaction untuk data integrity
5. ✅ Continue on error (tidak berhenti total)

## Tips Sebelum Import

### 1. Backup Database

**Opsi A: Via phpMyAdmin (PALING MUDAH & AMAN)** ✅ RECOMMENDED
1. Buka browser: `http://localhost/phpmyadmin`
2. Klik database `apotek_rotua` di sidebar kiri
3. Klik tab "Export" di atas
4. Pilih "Quick" export method
5. Format: SQL
6. Klik tombol "Go"
7. File .sql akan terdownload otomatis

**Opsi B: Via Command Line**
```bash
# Pastikan MySQL sedang berjalan!
# Untuk XAMPP
C:\xampp\mysql\bin\mysqldump -u root apotek_rotua > backup_sebelum_import.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.x\bin\mysqldump -u root apotek_rotua > backup_sebelum_import.sql
```

**Jika Error: "Can't create TCP/IP socket"** ⚠️
Lihat bagian Troubleshooting → Backup Gagal di bawah

### 2. Cek Jumlah Produk Sekarang
```sql
SELECT COUNT(*) as total_produk FROM products;
SELECT MAX(sku) as sku_terakhir FROM products WHERE sku LIKE 'OBT%';
```

### 3. Test Import Sebagian Dulu
Edit file Excel:
- Ambil 10-20 baris saja
- Test import
- Jika sukses, baru import semua

## Troubleshooting

### Backup Gagal: "Can't create TCP/IP socket (10106)"
**Penyebab**: MySQL server tidak berjalan atau ada masalah koneksi

**Solusi (coba urut dari atas):**

**1. Cek & Start MySQL Service**
```bash
# Cek status MySQL
# Buka Services (Win+R → services.msc)
# Cari "MySQL" atau "MariaDB"
# Klik kanan → Start

# Atau via XAMPP Control Panel
# Start "MySQL" module
```

**2. Cek Port 3306**
```bash
# Cek apakah port 3306 digunakan
netstat -ano | findstr :3306

# Jika tidak ada output, berarti MySQL tidak running
```

**3. Test Koneksi MySQL**
```bash
# Test login MySQL
mysql -u root -p

# Jika berhasil masuk, MySQL OK
# Ketik: exit
```

**4. Gunakan phpMyAdmin untuk Backup** (Cara termudah!)
- Buka: `http://localhost/phpmyadmin`
- Pilih database `apotek_rotua`
- Tab "Export" → Quick → Go
- Simpan file .sql yang didownload

**5. Restart MySQL Service**
```bash
# Via XAMPP: Stop lalu Start MySQL
# Via Laragon: Klik "Stop All" lalu "Start All"

# Via Command Line (as Administrator):
net stop mysql
net start mysql
```

**6. Cek Firewall/Antivirus**
- Pastikan port 3306 tidak diblock
- Temporarily disable antivirus untuk test

### Error: SKU Already Exists
**Penyebab**: Ada produk dengan SKU sama di database

**Solusi**:
1. Cek dengan query di atas
2. Hapus duplikat
3. Jalankan ulang seeder

### Error: Transaction Rolled Back
**Penyebab**: Ada error saat insert data

**Solusi**:
1. Lihat log error detail
2. Perbaiki data di Excel
3. Jalankan ulang

### Import Lambat
**Solusi**:
1. Disable foreign key check temporary:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Run import
SET FOREIGN_KEY_CHECKS=1;
```

2. Tingkatkan buffer MySQL di `my.ini`:
```ini
innodb_buffer_pool_size = 256M
bulk_insert_buffer_size = 16M
```

## Kontak
Jika masih ada masalah, dokumentasikan:
1. Screenshot error lengkap
2. Query hasil pengecekan duplikat
3. Jumlah data di database
