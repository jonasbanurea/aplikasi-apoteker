# HOTFIX: Export Penerimaan Barang - Field Kosong

## Masalah
Export penerimaan barang memiliki banyak field yang kosong karena field yang digunakan tidak sesuai dengan struktur database.

## Penyebab
Export menggunakan field yang tidak ada di model `Purchase`:
- ❌ `po_number` → seharusnya `invoice_no`
- ❌ `subtotal` → harus dihitung dari items
- ❌ `discount_amount` → seharusnya `discount`
- ❌ `tax_amount` → tidak ada di database
- ❌ `total_amount` → seharusnya `total`
- ❌ `notes` → tidak ada di database

## Struktur Database Purchases
```php
- id
- supplier_id
- invoice_no          // Nomor invoice dari supplier
- date                // Tanggal pembelian
- discount            // Diskon dalam rupiah
- total               // Total setelah diskon
- due_date            // Tanggal jatuh tempo (nullable)
- status              // POSTED atau CONSIGNMENT
- is_consignment      // Boolean
- created_at, updated_at
```

## Solusi

### 1. Perbaikan PurchasesExport.php ✅
**File:** [app/Exports/PurchasesExport.php](d:\\PROJECT\\APOTEKER\\Aplikasi\\app\\Exports\\PurchasesExport.php)

**Perubahan:**
- ✅ Ganti `po_number` → `invoice_no`
- ✅ Hitung `subtotal` dari sum items
- ✅ Ganti `discount_amount` → `discount`
- ✅ Hapus `tax_amount` (tidak ada di database)
- ✅ Ganti `total_amount` → `total`
- ✅ Hapus `notes` (tidak ada di database)
- ✅ Tambah kolom `Jatuh Tempo` (due_date)
- ✅ Tambah kolom `Konsinyasi` (is_consignment)

**Heading Baru:**
```
1. No Invoice
2. Tanggal Pembelian
3. Supplier
4. Status
5. Jatuh Tempo
6. Subtotal (Rp)
7. Diskon (Rp)
8. Total (Rp)
9. Jumlah Item
10. Konsinyasi
```

### 2. Tambah Export Detail Item ✅
**File Baru:** [app/Exports/PurchaseItemsExport.php](d:\\PROJECT\\APOTEKER\\Aplikasi\\app\\Exports\\PurchaseItemsExport.php)

Export detail per item produk yang dibeli dengan informasi lengkap:

**Heading:**
```
1. No Invoice
2. Tanggal
3. Supplier
4. Status
5. SKU
6. Nama Produk
7. Batch No
8. Expired Date
9. Qty
10. Bonus
11. Harga Beli
12. Subtotal Item
```

**Fitur:**
- Menampilkan setiap item produk dalam baris terpisah
- Informasi batch dan expired date
- Harga beli dan subtotal per item
- Qty dan bonus qty

### 3. Update Controller ✅
**File:** [app/Http/Controllers/PurchaseController.php](d:\\PROJECT\\APOTEKER\\Aplikasi\\app\\Http\\Controllers\\PurchaseController.php)

**Perubahan:**
```php
// Tambah import
use App\Exports\PurchaseItemsExport;

// Update method export dengan opsi type
public function export(Request $request)
{
    $type = $request->input('type', 'summary'); // summary atau detail
    
    if ($type === 'detail') {
        return Excel::download(new PurchaseItemsExport(...), 'purchase-items-detail.xlsx');
    }
    
    return Excel::download(new PurchasesExport(...), 'purchases-summary.xlsx');
}
```

### 4. Update View ✅
**File:** [resources/views/purchases/index.blade.php](d:\\PROJECT\\APOTEKER\\Aplikasi\\resources\\views\\purchases\\index.blade.php)

**Perubahan:**
- Ganti tombol export tunggal dengan dropdown
- Tambah opsi "Export Ringkasan" (summary)
- Tambah opsi "Export Detail Item" (detail)

**UI Baru:**
```
[Export Excel ▼]
  ├─ Export Ringkasan
  └─ Export Detail Item
```

## Cara Penggunaan

### Export Ringkasan (Summary)
1. Buka menu **Penerimaan Barang**
2. Klik dropdown **Export Excel**
3. Pilih **Export Ringkasan**
4. File: `purchases-summary-YYYY-MM-DD.xlsx`

**Isi Export Ringkasan:**
- Satu baris per transaksi pembelian
- Total dan jumlah item per transaksi
- Subtotal dihitung otomatis dari items

### Export Detail Item
1. Buka menu **Penerimaan Barang**
2. Klik dropdown **Export Excel**
3. Pilih **Export Detail Item**
4. File: `purchase-items-detail-YYYY-MM-DD.xlsx`

**Isi Export Detail:**
- Satu baris per item produk
- Informasi batch, expired date
- Harga beli dan qty per item
- Subtotal per item

## Filter Export (Coming Soon)
Untuk filter berdasarkan tanggal, tambahkan parameter:
```php
route('purchases.export', [
    'type' => 'summary',
    'start_date' => '2026-01-01',
    'end_date' => '2026-01-31'
])
```

## Verifikasi

### Test Export Ringkasan
```
✅ Kolom No Invoice terisi
✅ Kolom Tanggal terisi
✅ Kolom Supplier terisi
✅ Kolom Status terisi (POSTED/CONSIGNMENT)
✅ Kolom Jatuh Tempo terisi (atau "-")
✅ Kolom Subtotal terisi (dihitung dari items)
✅ Kolom Diskon terisi
✅ Kolom Total terisi
✅ Kolom Jumlah Item terisi
✅ Kolom Konsinyasi terisi (Ya/Tidak)
```

### Test Export Detail
```
✅ Setiap item produk muncul dalam baris terpisah
✅ SKU dan Nama Produk terisi
✅ Batch No terisi
✅ Expired Date terisi (atau "-")
✅ Qty dan Bonus terisi
✅ Harga Beli terisi
✅ Subtotal Item terisi (Qty × Harga Beli)
```

## Sample Data

### Export Ringkasan
```
No Invoice         | Tanggal    | Supplier      | Status | Jatuh Tempo | Subtotal  | Diskon   | Total     | Jumlah Item | Konsinyasi
INV-SUP-001        | 2026-01-10 | PT Kimia Farm | POSTED | 2026-02-10  | 5000000   | 100000   | 4900000   | 5           | Tidak
INV-SUP-002        | 2026-01-12 | CV Medis      | POSTED | -           | 2500000   | 0        | 2500000   | 3           | Tidak
INV-KONSI-001      | 2026-01-13 | PT Pharma     | CONSIGNMENT | -      | 1500000   | 0        | 1500000   | 2           | Ya
```

### Export Detail
```
No Invoice    | Tanggal    | Supplier      | SKU      | Nama Produk       | Batch    | Expired    | Qty | Bonus | Harga Beli | Subtotal
INV-SUP-001   | 2026-01-10 | PT Kimia Farm | PAR500   | Paracetamol 500mg | B001     | 2027-01-10 | 100 | 10    | 5000       | 500000
INV-SUP-001   | 2026-01-10 | PT Kimia Farm | AMX500   | Amoxicillin 500mg | B002     | 2027-06-15 | 50  | 5     | 15000      | 750000
INV-SUP-001   | 2026-01-10 | PT Kimia Farm | VIT-C    | Vitamin C         | B003     | 2026-12-01 | 200 | 20    | 2500       | 500000
```

## Update: 15 Januari 2026
Hotfix berhasil diterapkan. Export penerimaan barang sekarang menampilkan semua field dengan benar dan tersedia dalam 2 format: Ringkasan dan Detail Item.
