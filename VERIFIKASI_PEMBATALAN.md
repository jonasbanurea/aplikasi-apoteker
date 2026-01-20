# VERIFIKASI LAPORAN SETELAH PEMBATALAN TRANSAKSI

## Test Case: Pembatalan Transaksi & Dampaknya

### Skenario Test
1. **Sebelum Pembatalan**
2. **Proses Pembatalan**  
3. **Setelah Pembatalan**

---

## 1️⃣ DATA SEBELUM PEMBATALAN

### A. Catat Data Awal

#### Dashboard
```
- Total Pendapatan Bulan Ini: Rp _________
- Transaksi Hari Ini: _____ transaksi
- Pendapatan Hari Ini: Rp _________
- Top Product #1: _________ (Qty: ___)
```

#### Stok Produk (contoh: Paracetamol 500mg)
```
- SKU: ___________
- Stok Sebelum: _____ unit
- Batch yang terjual:
  * Batch #1: Exp ________, Stok: _____ unit
  * Batch #2: Exp ________, Stok: _____ unit
```

#### Stock Movement Log
```sql
-- Lihat movement terakhir
SELECT * FROM stock_movements 
WHERE product_id = [ID_PRODUK]
ORDER BY created_at DESC LIMIT 5;
```

---

## 2️⃣ PROSES PEMBATALAN

### Transaksi yang Akan Dibatalkan
```
Invoice: _______________
Tanggal: _______________
Total: Rp _______________
Items:
- [Nama Produk 1]: Qty ___ @ Rp _____ = Rp _____
- [Nama Produk 2]: Qty ___ @ Rp _____ = Rp _____
```

### Langkah Pembatalan
1. Login sebagai **owner**
2. Buka menu **Penjualan** → Cari invoice
3. Klik **Detail** transaksi
4. Klik tombol **Batalkan Transaksi**
5. Isi alasan: "Test verifikasi sistem pembatalan"
6. Konfirmasi pembatalan

---

## 3️⃣ VERIFIKASI SETELAH PEMBATALAN

### ✅ A. Dashboard Harus Berubah

#### Total Pendapatan
```
✅ Pendapatan Bulan Ini = [Awal] - [Nilai Transaksi]
✅ Pendapatan Hari Ini = [Awal] - [Nilai Transaksi]
✅ Jumlah Transaksi tetap sama (transaksi tidak dihapus)
```

#### Chart & Top Products
```
✅ Chart 7 hari terakhir: nilai turun
✅ Top Products: qty turun sesuai pembatalan
```

---

### ✅ B. Stok Harus Kembali

#### Cek Stok Produk
```sql
-- Cek stok batch
SELECT sb.id, sb.batch_no, sb.expired_date, sb.qty_on_hand
FROM stock_batches sb
WHERE sb.product_id = [ID_PRODUK]
ORDER BY sb.expired_date;
```

**Expected Result:**
```
✅ Qty_on_hand bertambah sesuai qty yang dibatalkan
✅ Stok kembali ke batch yang sama (FEFO)
```

#### Cek Stock Movement
```sql
-- Cek movement pembatalan
SELECT * FROM stock_movements
WHERE ref_type = 'SALE_CANCEL'
AND ref_id = [ID_SALE]
ORDER BY created_at DESC;
```

**Expected Result:**
```
✅ Ada record baru type = 'IN'
✅ ref_type = 'SALE_CANCEL'
✅ qty sesuai dengan yang dibatalkan
✅ notes: "Pembatalan penjualan [INVOICE_NO]"
```

---

### ✅ C. Laporan Harus Akurat

#### 1. Laporan Penjualan
**Menu:** Laporan → Filter periode yang berisi transaksi dibatalkan

```
✅ Total Pendapatan TIDAK termasuk transaksi dibatalkan
✅ Jumlah Item Terjual TIDAK termasuk yang dibatalkan
✅ Sales Per Cashier TIDAK termasuk yang dibatalkan
✅ Sales Per Golongan TIDAK termasuk yang dibatalkan
✅ Gross Profit TIDAK termasuk yang dibatalkan
```

#### 2. Laporan Produk Terlaris
```sql
SELECT 
    p.nama_dagang,
    SUM(si.qty) as total_qty,
    SUM(si.line_total) as revenue
FROM sale_items si
JOIN sales s ON s.id = si.sale_id
JOIN products p ON p.id = si.product_id
WHERE s.is_cancelled = 0
AND s.sale_date >= '2026-01-01'
GROUP BY p.id
ORDER BY total_qty DESC
LIMIT 10;
```

**Expected Result:**
```
✅ Qty produk berkurang dari transaksi yang dibatalkan
✅ Revenue tidak termasuk transaksi yang dibatalkan
```

#### 3. Reorder List (Produk Perlu Restock)
```
✅ Avg daily sold tidak termasuk penjualan yang dibatalkan
✅ Reorder need calculation akurat
```

---

### ✅ D. Export & Audit Trail

#### Export Sales (Excel/PDF)
**Menu:** Penjualan → Export

```
✅ Transaksi dibatalkan TETAP MUNCUL di export
✅ Status = "DIBATALKAN" atau badge merah
✅ Kolom cancelled_at, cancelled_by terisi
✅ Alasan pembatalan tercatat
```

#### Database Audit
```sql
-- Cek sales table
SELECT 
    invoice_no,
    total,
    is_cancelled,
    cancelled_at,
    cancel_reason
FROM sales
WHERE invoice_no = '[INVOICE_NO]';
```

**Expected Result:**
```
✅ is_cancelled = 1
✅ cancelled_at = [timestamp]
✅ cancelled_by = [user_id owner]
✅ cancel_reason = alasan yang diinput
✅ Data transaksi TIDAK DIHAPUS (tetap ada)
```

---

## 4️⃣ QUERY VERIFIKASI LENGKAP

### Test SQL Queries

```sql
-- 1. Total Pendapatan Bulan Ini (TANPA yang dibatalkan)
SELECT 
    COUNT(*) as total_transaksi,
    SUM(total) as total_pendapatan
FROM sales
WHERE MONTH(sale_date) = MONTH(NOW())
AND YEAR(sale_date) = YEAR(NOW())
AND is_cancelled = 0;

-- 2. Transaksi yang Dibatalkan
SELECT 
    COUNT(*) as total_dibatalkan,
    SUM(total) as nilai_dibatalkan
FROM sales
WHERE is_cancelled = 1;

-- 3. Stock Movement untuk Transaksi Dibatalkan
SELECT 
    sm.type,
    sm.ref_type,
    sm.qty,
    sm.notes,
    p.nama_dagang,
    sb.batch_no
FROM stock_movements sm
JOIN products p ON p.id = sm.product_id
JOIN stock_batches sb ON sb.id = sm.batch_id
WHERE sm.ref_type = 'SALE_CANCEL'
ORDER BY sm.created_at DESC
LIMIT 20;

-- 4. Stok Produk yang Terdampak
SELECT 
    p.sku,
    p.nama_dagang,
    SUM(sb.qty_on_hand) as total_stok
FROM products p
LEFT JOIN stock_batches sb ON sb.product_id = p.id
WHERE p.id IN (
    SELECT DISTINCT product_id 
    FROM sale_items si
    JOIN sales s ON s.id = si.sale_id
    WHERE s.is_cancelled = 1
)
GROUP BY p.id;

-- 5. Top Products (HARUS exclude yang dibatalkan)
SELECT 
    p.nama_dagang,
    SUM(si.qty) as total_qty,
    SUM(si.line_total) as revenue
FROM sale_items si
JOIN sales s ON s.id = si.sale_id
JOIN products p ON p.id = si.product_id
WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
AND s.is_cancelled = 0
GROUP BY p.id
ORDER BY total_qty DESC
LIMIT 10;
```

---

## 5️⃣ CHECKLIST FINAL

### Pendapatan ✅
- [ ] Dashboard total pendapatan berkurang
- [ ] Laporan penjualan tidak termasuk yang dibatalkan
- [ ] Chart penjualan tidak termasuk yang dibatalkan
- [ ] Export menunjukkan status DIBATALKAN dengan jelas

### Stok ✅
- [ ] Stok batch kembali (qty_on_hand bertambah)
- [ ] Stock movement type IN tercatat
- [ ] Stock movement ref_type = SALE_CANCEL
- [ ] Notes pembatalan jelas

### Laporan ✅
- [ ] Sales per cashier tidak termasuk yang dibatalkan
- [ ] Sales per item tidak termasuk yang dibatalkan
- [ ] Sales per golongan tidak termasuk yang dibatalkan
- [ ] Gross profit tidak termasuk yang dibatalkan
- [ ] Reorder list avg sales tidak termasuk yang dibatalkan
- [ ] Top products tidak termasuk yang dibatalkan

### Audit Trail ✅
- [ ] Transaksi tidak dihapus dari database
- [ ] is_cancelled = 1
- [ ] cancelled_at, cancelled_by, cancel_reason terisi
- [ ] UI menampilkan badge DIBATALKAN
- [ ] Export tetap menampilkan semua transaksi

---

## 📊 HASIL TEST

**Tanggal Test:** _______________  
**Tester:** _______________  
**Status:** [ ] PASS / [ ] FAIL

### Catatan:
```
________________________________________
________________________________________
________________________________________
```

### Screenshot:
- [ ] Dashboard sebelum pembatalan
- [ ] Dashboard setelah pembatalan  
- [ ] Detail transaksi dengan badge DIBATALKAN
- [ ] Stock movement log
- [ ] Laporan PDF/Excel

---

## 🎯 KESIMPULAN

Jika semua checklist di atas ✅ PASS, maka sistem pembatalan transaksi sudah bekerja dengan benar:

1. **Pendapatan:** Berkurang otomatis dari dashboard & laporan
2. **Stok:** Dikembalikan otomatis ke batch yang sama
3. **Audit Trail:** Transaksi tetap tersimpan dengan status dibatalkan
4. **Laporan:** Tidak menghitung transaksi yang dibatalkan
5. **Export:** Menampilkan semua transaksi termasuk yang dibatalkan

**Sistem siap digunakan!** ✅
