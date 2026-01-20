# Panduan Memperbaiki Harga Produk di Laptop Client

## Masalah: Harga Beli = Harga Jual (Margin 0%)

Produk **HUFAGRIP** memiliki harga beli dan harga jual yang sama, sehingga tidak ada margin keuntungan.

---

## 📱 SOLUSI 1: Via Aplikasi Web (PALING MUDAH)

### Langkah-langkah:

#### A. Buka Aplikasi di Browser Client
```
http://[IP-SERVER]:8000
atau
http://localhost:8000  (jika di laptop yang sama)
```

#### B. Login sebagai Owner atau Admin Gudang
- Username: `owner@rotua.test` atau `gudang@rotua.test`
- Password: `password`

#### C. Edit Produk
1. Buka menu **Produk** (sidebar kiri)
2. **Cari produk HUFAGRIP:**
   - Gunakan search box
   - Atau scroll cari manual
   
3. **Klik tombol Edit** (icon pensil) pada produk HUFAGRIP

4. **Update Harga:**
   ```
   Contoh HUFAGRIP:
   - Harga Beli (Cost): Rp 15.000
   - Harga Jual (Price): Rp 25.000  (margin 66%)
   
   Atau sesuaikan dengan harga pasar/competitor
   ```

5. **Klik Simpan**

#### D. Verifikasi
- Kembali ke halaman produk
- Cek margin sudah benar
- Atau buka menu **Dashboard** → lihat margin produk

---

## 💾 SOLUSI 2: Via Database MySQL (LEBIH CEPAT)

Jika akses ke laptop client secara remote atau banyak produk yang perlu diperbaiki.

### A. Via phpMyAdmin (XAMPP/Laragon)

#### 1. Buka phpMyAdmin di Client
```
http://localhost/phpmyadmin
```

#### 2. Pilih Database
- Database: `toko_obat_rotua` (atau nama database yang dipakai)

#### 3. Cek Data HUFAGRIP
```sql
-- Tab SQL, jalankan query ini:
SELECT 
    id,
    sku,
    nama_dagang,
    harga_beli,
    harga_jual,
    (harga_jual - harga_beli) as margin_rp,
    ROUND(((harga_jual - harga_beli) / harga_beli * 100), 2) as margin_persen
FROM products
WHERE nama_dagang LIKE '%HUFAGRIP%';
```

**Output contoh masalah:**
```
id  | sku        | nama_dagang     | harga_beli | harga_jual | margin_rp | margin_persen
123 | HFG-TAB    | HUFAGRIP TAB    | 15000      | 15000      | 0         | 0.00
```

#### 4. Update Harga Jual
```sql
-- Ganti harga jual HUFAGRIP menjadi 25000 (contoh)
UPDATE products
SET harga_jual = 25000
WHERE nama_dagang LIKE '%HUFAGRIP%'
AND harga_beli = harga_jual;

-- Atau jika tahu ID-nya:
UPDATE products
SET harga_jual = 25000
WHERE id = 123;
```

#### 5. Verifikasi
```sql
-- Cek lagi
SELECT 
    sku,
    nama_dagang,
    harga_beli,
    harga_jual,
    (harga_jual - harga_beli) as margin_rp,
    ROUND(((harga_jual - harga_beli) / harga_beli * 100), 2) as margin_persen
FROM products
WHERE nama_dagang LIKE '%HUFAGRIP%';
```

**Output setelah diperbaiki:**
```
sku        | nama_dagang     | harga_beli | harga_jual | margin_rp | margin_persen
HFG-TAB    | HUFAGRIP TAB    | 15000      | 25000      | 10000     | 66.67
```

---

### B. Via Command Line (PowerShell/CMD di Client)

```bash
# 1. Masuk ke MySQL (di laptop client)
cd C:\xampp\mysql\bin   # atau C:\laragon\bin\mysql\mariadb-xxx\bin
mysql -u root -p toko_obat_rotua

# 2. Cek data
SELECT id, sku, nama_dagang, harga_beli, harga_jual
FROM products
WHERE nama_dagang LIKE '%HUFAGRIP%';

# 3. Update harga
UPDATE products
SET harga_jual = 25000
WHERE nama_dagang LIKE '%HUFAGRIP%'
AND harga_beli = harga_jual;

# 4. Exit
EXIT;
```

---

## 🔍 SOLUSI 3: Cek Semua Produk dengan Margin 0%

Mungkin ada produk lain yang juga bermasalah selain HUFAGRIP.

### A. Query untuk Cek Semua Produk Margin 0

```sql
-- Cek semua produk yang harga beli = harga jual
SELECT 
    id,
    sku,
    nama_dagang,
    harga_beli,
    harga_jual,
    golongan,
    satuan
FROM products
WHERE harga_beli = harga_jual
AND harga_beli > 0
ORDER BY nama_dagang;
```

### B. Update Multiple Produk (Hati-hati!)

```sql
-- Tambah margin 30% untuk semua produk yang margin 0
UPDATE products
SET harga_jual = ROUND(harga_beli * 1.30, -2)  -- Bulatkan ke ratusan
WHERE harga_beli = harga_jual
AND harga_beli > 0;

-- Contoh hasil:
-- Harga Beli: 15000 → Harga Jual: 19500 (margin 30%)
-- Harga Beli: 20000 → Harga Jual: 26000 (margin 30%)
```

### C. Update dengan Margin Berbeda per Golongan

```sql
-- Margin berbeda berdasarkan golongan obat
-- Misal: Obat Bebas = 30%, Obat Keras = 25%, dll

-- Obat Bebas (margin 30%)
UPDATE products
SET harga_jual = ROUND(harga_beli * 1.30, -2)
WHERE harga_beli = harga_jual
AND golongan = 'OBAT BEBAS';

-- Obat Keras (margin 25%)
UPDATE products
SET harga_jual = ROUND(harga_beli * 1.25, -2)
WHERE harga_beli = harga_jual
AND golongan = 'OBAT KERAS';

-- Alkes (margin 35%)
UPDATE products
SET harga_jual = ROUND(harga_beli * 1.35, -2)
WHERE harga_beli = harga_jual
AND golongan = 'ALKES';
```

---

## 📋 TEMPLATE UPDATE HARGA MANUAL

Jika ingin update satu per satu dengan harga spesifik:

```sql
-- Update HUFAGRIP
UPDATE products SET harga_jual = 25000 WHERE nama_dagang LIKE '%HUFAGRIP%';

-- Update produk lain (sesuaikan nama dan harga)
UPDATE products SET harga_jual = 35000 WHERE nama_dagang LIKE '%PARACETAMOL 500%';
UPDATE products SET harga_jual = 45000 WHERE nama_dagang LIKE '%AMOXICILLIN%';
UPDATE products SET harga_jual = 50000 WHERE nama_dagang LIKE '%BODREX%';
```

---

## 🚨 PENTING - BACKUP DULU!

Sebelum update harga via database, **WAJIB BACKUP!**

### Cara Backup di Client:

```bash
# Via phpMyAdmin:
# 1. Pilih database
# 2. Tab Export → Quick → SQL → Go
# 3. Simpan: backup_products_[TANGGAL].sql

# Via Command Line:
cd C:\xampp\mysql\bin
mysqldump -u root -p toko_obat_rotua products > D:\backup_products_%date:~0,10%.sql
```

---

## ✅ VERIFIKASI SETELAH UPDATE

### A. Via Aplikasi
1. Login ke aplikasi
2. Buka menu **Produk**
3. Cari HUFAGRIP
4. Lihat kolom **Harga Beli** dan **Harga Jual**
5. Pastikan ada selisih (margin)

### B. Via Database
```sql
-- Cek margin semua produk
SELECT 
    COUNT(*) as total_produk,
    SUM(CASE WHEN harga_beli = harga_jual THEN 1 ELSE 0 END) as margin_0_persen,
    AVG(ROUND(((harga_jual - harga_beli) / harga_beli * 100), 2)) as avg_margin_persen
FROM products
WHERE harga_beli > 0;
```

### C. Test Transaksi
1. Buat transaksi penjualan HUFAGRIP
2. Cek di dashboard
3. Pastikan ada margin keuntungan

---

## 📞 PANDUAN REMOTE (Dari Laptop Server ke Client)

Jika mau update dari laptop server ke client via jaringan:

### A. Akses Database Client dari Server

```bash
# Di laptop server, connect ke MySQL client
mysql -h [IP_CLIENT] -u root -p toko_obat_rotua

# Contoh:
mysql -h 192.168.1.100 -u root -p toko_obat_rotua

# Lalu jalankan query update seperti di atas
```

### B. Akses phpMyAdmin Client dari Browser Server

```
http://[IP_CLIENT]/phpmyadmin
# Contoh: http://192.168.1.100/phpmyadmin
```

### C. Remote Desktop ke Client

```
# Gunakan Windows Remote Desktop
mstsc
# Masukkan IP client
# Login → akses aplikasi/database
```

---

## 🎯 REKOMENDASI HARGA HUFAGRIP

Berdasarkan harga pasar umum:

```
Produk: HUFAGRIP TABLET
- Harga Beli (dari supplier): Rp 12.000 - 15.000 per strip
- Harga Jual Apotek: Rp 20.000 - 25.000 per strip
- Margin: 30% - 66%
- Rekomendasi: Rp 22.000 - 24.000
```

---

## 📊 QUERY UNTUK REPORT MARGIN

Untuk cek produk dengan margin rendah atau negatif:

```sql
-- Produk dengan margin < 10%
SELECT 
    sku,
    nama_dagang,
    harga_beli,
    harga_jual,
    ROUND(((harga_jual - harga_beli) / harga_beli * 100), 2) as margin_persen
FROM products
WHERE harga_beli > 0
AND ((harga_jual - harga_beli) / harga_beli * 100) < 10
ORDER BY margin_persen ASC;

-- Produk dengan margin negatif (rugi!)
SELECT 
    sku,
    nama_dagang,
    harga_beli,
    harga_jual,
    (harga_jual - harga_beli) as margin_rp
FROM products
WHERE harga_jual < harga_beli;
```

---

## 🔧 CARA CEPAT (Copy-Paste Query)

**1. Untuk HUFAGRIP Saja:**
```sql
UPDATE products 
SET harga_jual = 24000 
WHERE nama_dagang LIKE '%HUFAGRIP%' 
AND harga_beli = harga_jual;
```

**2. Untuk Semua Produk Margin 0 (Auto +30%):**
```sql
UPDATE products 
SET harga_jual = ROUND(harga_beli * 1.30, -2) 
WHERE harga_beli = harga_jual 
AND harga_beli > 0;
```

---

## ✅ CHECKLIST

- [ ] Backup database dulu
- [ ] Cek harga beli HUFAGRIP saat ini
- [ ] Tentukan harga jual yang wajar (cek pasar)
- [ ] Update via aplikasi atau database
- [ ] Verifikasi perubahan
- [ ] Cek produk lain yang mungkin bermasalah
- [ ] Test transaksi penjualan
- [ ] Lihat margin di dashboard

---

## 🎉 SELESAI!

Setelah update, harga HUFAGRIP di laptop client sudah diperbaiki dan ada margin keuntungan.

**Kontak jika butuh bantuan lebih lanjut!**
