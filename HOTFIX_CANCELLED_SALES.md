# HOTFIX: Pembatalan Penjualan Tidak Mengurangi Pendapatan

## Masalah
Ketika transaksi penjualan dibatalkan, total pendapatan tidak berkurang. Stok sudah dikembalikan dengan benar, tetapi query pendapatan tidak mengecualikan transaksi yang dibatalkan.

## Penyebab
Semua query yang menghitung total pendapatan tidak memiliki filter `WHERE is_cancelled = false`, sehingga transaksi yang dibatalkan tetap dihitung dalam total pendapatan.

## Solusi
Menambahkan filter `->where('is_cancelled', false)` pada semua query yang menghitung pendapatan:

### File yang Diperbaiki

#### 1. DashboardController.php
- ✅ Total pendapatan bulan ini
- ✅ Total pendapatan bulan lalu  
- ✅ Transaksi hari ini
- ✅ Pendapatan hari ini
- ✅ Item terjual hari ini
- ✅ Total transaksi bulan ini
- ✅ Recent sales (5 terakhir)
- ✅ Sales chart (7 hari terakhir)
- ✅ Top products bulan ini

#### 2. ReportController.php
- ✅ Sales base query (semua periode)
- ✅ Sales per item
- ✅ Sales per golongan
- ✅ Weekly payment method
- ✅ Gross profit calculation
- ✅ Reorder list calculation (avg daily sales)

#### 3. SalesExport.php
- ✅ Tetap export semua transaksi (termasuk yang dibatalkan) untuk audit trail
- ✅ Kolom status sudah menunjukkan transaksi yang dibatalkan

## Verifikasi
Untuk memastikan perbaikan bekerja:

1. **Cek Dashboard:**
   - Total pendapatan sebelum pembatalan: Rp X
   - Batalkan 1 transaksi senilai Rp Y
   - Total pendapatan setelah pembatalan: Rp (X - Y) ✅

2. **Cek Laporan:**
   - Filter periode yang mengandung transaksi yang dibatalkan
   - Pastikan total pendapatan tidak termasuk transaksi yang dibatalkan
   - Cek grafik penjualan per hari tidak menghitung transaksi dibatalkan

3. **Cek Export:**
   - Export data penjualan
   - Pastikan transaksi yang dibatalkan tetap muncul dengan status "DIBATALKAN"
   - Total di Excel/PDF tidak menghitung transaksi yang dibatalkan

## Testing Manual

```sql
-- Cek transaksi yang dibatalkan
SELECT COUNT(*), SUM(total) 
FROM sales 
WHERE is_cancelled = 1;

-- Cek total pendapatan (tanpa yang dibatalkan)
SELECT COUNT(*), SUM(total) 
FROM sales 
WHERE is_cancelled = 0 
AND sale_date >= '2026-01-01';

-- Cek dashboard bulan ini
SELECT COUNT(*), SUM(total) 
FROM sales 
WHERE is_cancelled = 0 
AND MONTH(sale_date) = MONTH(NOW())
AND YEAR(sale_date) = YEAR(NOW());
```

## Fitur yang Masih Berfungsi
- ✅ Pembatalan transaksi (hanya owner)
- ✅ Stok dikembalikan otomatis
- ✅ Stock movement tercatat (SALE_CANCEL)
- ✅ Alasan pembatalan tersimpan
- ✅ Transaksi dibatalkan tetap ada di database (audit trail)
- ✅ UI menampilkan badge "DIBATALKAN" dengan background merah
- ✅ Export tetap menampilkan semua transaksi termasuk yang dibatalkan

## Update: 15 Januari 2026
Hotfix berhasil diterapkan. Semua query pendapatan sekarang mengecualikan transaksi yang dibatalkan (`is_cancelled = true`).
