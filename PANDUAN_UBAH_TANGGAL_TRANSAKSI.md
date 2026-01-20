# Panduan Mengubah Tanggal Transaksi - Database MySQL

## ⚠️ PENTING - BACKUP DULU!

Sebelum melakukan perubahan apapun, **WAJIB BACKUP DATABASE!**

---

## 🔧 LANGKAH 1: BACKUP DATABASE

### Via phpMyAdmin (XAMPP/Laragon)
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Pilih database aplikasi (contoh: `toko_obat_rotua`)
3. Klik tab **Export**
4. Pilih **Quick** export method
5. Format: **SQL**
6. Klik **Go**
7. Simpan file: `backup_sebelum_ubah_tanggal_[TANGGAL].sql`

### Via Command Line
```bash
# Jalankan di terminal (PowerShell/CMD)
cd C:\xampp\mysql\bin   # atau C:\laragon\bin\mysql\mariadb-xxx\bin

# Backup database
mysqldump -u root -p toko_obat_rotua > D:\backup_transaksi_%date:~0,10%.sql
```

---

## 🔍 LANGKAH 2: CEK DATA YANG AKAN DIUBAH

### A. Cek Transaksi Tanggal 13 (yang salah input)

```sql
-- Login ke MySQL dulu
mysql -u root -p toko_obat_rotua

-- Atau via phpMyAdmin, buka tab SQL dan jalankan:

-- 1. Lihat semua transaksi tanggal 13
SELECT 
    id,
    invoice_no,
    sale_date,
    total,
    is_cancelled,
    user_id
FROM sales
WHERE DATE(sale_date) = '2026-01-13'
ORDER BY sale_date;
```

**Output contoh:**
```
+----+-------------+---------------------+---------+--------------+---------+
| id | invoice_no  | sale_date           | total   | is_cancelled | user_id |
+----+-------------+---------------------+---------+--------------+---------+
| 45 | INV-20260113-001 | 2026-01-13 09:15:00 | 125000 | 0 | 2 |
| 46 | INV-20260113-002 | 2026-01-13 10:30:00 | 85000  | 0 | 2 |
| 47 | INV-20260113-003 | 2026-01-13 14:20:00 | 200000 | 0 | 3 |
+----+-------------+---------------------+---------+--------------+---------+
```

### B. Identifikasi Transaksi yang Seharusnya Tanggal 12

```sql
-- Catat ID transaksi yang mau diubah
-- Misalnya: ID 45, 46 seharusnya tanggal 12
-- Pastikan is_cancelled = 0 (tidak dibatalkan)
```

---

## ✏️ LANGKAH 3: UPDATE TANGGAL TRANSAKSI

### Metode 1: Update Berdasarkan ID (PALING AMAN)

```sql
-- Update SATU transaksi dulu (test)
UPDATE sales
SET sale_date = '2026-01-12 09:15:00'
WHERE id = 45
AND is_cancelled = 0;

-- Cek hasil
SELECT id, invoice_no, sale_date, total 
FROM sales 
WHERE id = 45;
```

**Jika sudah benar, lanjutkan untuk ID lain:**

```sql
-- Update transaksi kedua
UPDATE sales
SET sale_date = '2026-01-12 10:30:00'
WHERE id = 46
AND is_cancelled = 0;

-- Update transaksi ketiga
UPDATE sales
SET sale_date = '2026-01-12 14:20:00'
WHERE id = 47
AND is_cancelled = 0;
```

### Metode 2: Update Multiple Sekaligus (Hati-hati!)

```sql
-- Update SEMUA transaksi tanggal 13 ke tanggal 12
-- HANYA gunakan jika yakin SEMUA transaksi tgl 13 salah!

UPDATE sales
SET sale_date = DATE_SUB(sale_date, INTERVAL 1 DAY)
WHERE DATE(sale_date) = '2026-01-13'
AND is_cancelled = 0;
```

### Metode 3: Update dengan Preserve Jam

```sql
-- Ubah tanggal tapi jam tetap sama
UPDATE sales
SET sale_date = CONCAT('2026-01-12 ', TIME(sale_date))
WHERE DATE(sale_date) = '2026-01-13'
AND is_cancelled = 0;
```

---

## ✅ LANGKAH 4: VERIFIKASI HASIL

### A. Cek Transaksi yang Sudah Diubah

```sql
-- Lihat transaksi tanggal 12
SELECT 
    id,
    invoice_no,
    sale_date,
    total,
    user_id
FROM sales
WHERE DATE(sale_date) = '2026-01-12'
ORDER BY sale_date;
```

### B. Pastikan Tidak Ada Transaksi Salah di Tanggal 13

```sql
-- Cek apakah masih ada transaksi di tanggal 13
SELECT 
    id,
    invoice_no,
    sale_date,
    total
FROM sales
WHERE DATE(sale_date) = '2026-01-13'
ORDER BY sale_date;
```

### C. Cek Total Pendapatan Per Tanggal

```sql
-- Bandingkan total per tanggal
SELECT 
    DATE(sale_date) as tanggal,
    COUNT(*) as jumlah_transaksi,
    SUM(total) as total_pendapatan
FROM sales
WHERE DATE(sale_date) BETWEEN '2026-01-12' AND '2026-01-13'
AND is_cancelled = 0
GROUP BY DATE(sale_date)
ORDER BY tanggal;
```

---

## 🔄 LANGKAH 5: UPDATE INVOICE NUMBER (OPSIONAL)

Jika invoice number menggunakan format tanggal (contoh: `INV-20260113-001`), mungkin perlu diubah:

```sql
-- Lihat invoice yang perlu diubah
SELECT id, invoice_no, sale_date
FROM sales
WHERE DATE(sale_date) = '2026-01-12'
AND invoice_no LIKE 'INV-20260113-%';

-- Update invoice number
UPDATE sales
SET invoice_no = REPLACE(invoice_no, 'INV-20260113-', 'INV-20260112-')
WHERE DATE(sale_date) = '2026-01-12'
AND invoice_no LIKE 'INV-20260113-%';
```

---

## 🚨 TROUBLESHOOTING

### Problem 1: Duplikat Invoice Number

```sql
-- Cek duplikat invoice
SELECT invoice_no, COUNT(*) as jumlah
FROM sales
GROUP BY invoice_no
HAVING jumlah > 1;

-- Fix: Update invoice number yang duplikat
UPDATE sales
SET invoice_no = CONCAT(invoice_no, '-FIX')
WHERE id = [ID_YANG_DUPLIKAT];
```

### Problem 2: Shift Tidak Sesuai

```sql
-- Cek shift transaksi
SELECT 
    s.id,
    s.invoice_no,
    s.sale_date,
    s.shift_id,
    sh.date as shift_date
FROM sales s
LEFT JOIN shifts sh ON sh.id = s.shift_id
WHERE DATE(s.sale_date) = '2026-01-12';

-- Jika shift tidak sesuai, update shift_id
UPDATE sales
SET shift_id = [SHIFT_ID_YANG_BENAR]
WHERE id = [ID_TRANSAKSI];
```

### Problem 3: Salah Update

```sql
-- ROLLBACK - Restore dari backup
-- Via phpMyAdmin:
-- 1. Drop database (hati-hati!)
-- 2. Create database baru
-- 3. Import file backup

-- Via Command Line:
mysql -u root -p toko_obat_rotua < D:\backup_transaksi_xxx.sql
```

---

## 📋 TEMPLATE QUERY LENGKAP

### Untuk Copy-Paste (Sesuaikan ID dan tanggal!)

```sql
-- ===================================
-- BACKUP CEK DULU!
-- ===================================

-- 1. CEK DATA SEBELUM UPDATE
SELECT id, invoice_no, sale_date, total, is_cancelled
FROM sales
WHERE DATE(sale_date) = '2026-01-13'
ORDER BY sale_date;

-- 2. UPDATE TRANSAKSI (GANTI ID SESUAI HASIL CEK!)
START TRANSACTION;

UPDATE sales SET sale_date = '2026-01-12 09:15:00' WHERE id = 45;
UPDATE sales SET sale_date = '2026-01-12 10:30:00' WHERE id = 46;
UPDATE sales SET sale_date = '2026-01-12 14:20:00' WHERE id = 47;

-- 3. CEK HASIL
SELECT id, invoice_no, sale_date, total
FROM sales
WHERE DATE(sale_date) IN ('2026-01-12', '2026-01-13')
ORDER BY sale_date;

-- 4. JIKA SUDAH BENAR, COMMIT
COMMIT;

-- JIKA ADA KESALAHAN, ROLLBACK
-- ROLLBACK;

-- ===================================
-- VERIFIKASI AKHIR
-- ===================================

-- Cek total per tanggal
SELECT 
    DATE(sale_date) as tanggal,
    COUNT(*) as jumlah_transaksi,
    SUM(total) as total_pendapatan
FROM sales
WHERE DATE(sale_date) BETWEEN '2026-01-12' AND '2026-01-13'
AND is_cancelled = 0
GROUP BY DATE(sale_date);
```

---

## ✅ CHECKLIST SEBELUM UPDATE

- [ ] Database sudah di-backup
- [ ] Sudah mengidentifikasi ID transaksi yang akan diubah
- [ ] Sudah cek is_cancelled = 0 (tidak dibatalkan)
- [ ] Menggunakan `START TRANSACTION` untuk safety
- [ ] Sudah verifikasi hasil sebelum COMMIT

---

## 🎯 TIPS KEAMANAN

1. **Selalu gunakan WHERE dengan ID spesifik** - jangan update tanpa WHERE!
2. **Test di 1 record dulu** sebelum update banyak
3. **Gunakan TRANSACTION** agar bisa di-rollback jika salah
4. **Backup sebelum dan sesudah** perubahan
5. **Catat semua query** yang dijalankan untuk dokumentasi

---

## 📞 JIKA BUTUH BANTUAN

**Jangan panic!** Selama sudah backup, data aman.

### Emergency Rollback:
```bash
# Restore dari backup
mysql -u root -p toko_obat_rotua < D:\path\to\backup.sql
```

### Cek Log Aplikasi:
```
storage/logs/laravel.log
```

---

**Selamat mencoba! Semoga berhasil!** 🚀
