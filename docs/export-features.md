# Export Data Features

## Overview
Aplikasi Toko Obat Ro Tua dilengkapi dengan fitur export data ke format Excel (.xlsx) untuk kemudahan analisis dan backup data.

## Available Exports

### 1. Export Produk
**Route:** `GET /products/export`  
**Access:** Owner, Kasir, Admin Gudang  
**File:** `ProductsExport.php`

**Data yang diexport:**
- SKU
- Nama Dagang
- Nama Generik
- Bentuk Sediaan
- Kekuatan & Dosis
- Satuan
- Golongan (OTC/Resep/dll)
- Wajib Resep (Ya/Tidak)
- Harga Beli
- Harga Jual
- Lokasi Rak (termasuk RAK A/B/D)
- Minimal Stok
- Konsinyasi (Ya/Tidak)
- Stok Total

**Cara akses:**
1. Buka menu **Produk / Obat**
2. Klik tombol **Export Excel** (hijau)
3. File akan otomatis terdownload: `products-YYYY-MM-DD.xlsx`

---

### 2. Export Penjualan
**Route:** `GET /sales/export`  
**Access:** Owner, Kasir, Admin Gudang  
**File:** `SalesExport.php`

**Data yang diexport:**
- No Invoice
- Tanggal & Waktu
- Kasir
- Shift
- Customer
- Payment Method (Cash/QRIS/Transfer/Debit)
- Subtotal
- Diskon
- PPN
- Total
- Bayar
- Kembalian
- Status
- Jumlah Item

**Filter tersedia:**
- `start_date`: Filter tanggal mulai (YYYY-MM-DD)
- `end_date`: Filter tanggal akhir (YYYY-MM-DD)

**Contoh:**
```
/sales/export?start_date=2026-01-01&end_date=2026-01-31
```

**Cara akses:**
1. Buka menu **Penjualan (POS)**
2. Klik tombol **Export Excel**
3. File akan terdownload: `sales-YYYY-MM-DD.xlsx`

---

### 3. Export Pembelian
**Route:** `GET /purchases/export`  
**Access:** Owner, Admin Gudang  
**File:** `PurchasesExport.php`

**Data yang diexport:**
- No PO (Purchase Order)
- Tanggal Pembelian
- Supplier
- Status (Lunas/Hutang/Konsinyasi)
- Subtotal
- Diskon
- PPN
- Total
- Dibuat Oleh (User)
- Catatan
- Jumlah Item

**Filter tersedia:**
- `start_date`: Filter tanggal mulai
- `end_date`: Filter tanggal akhir

**Cara akses:**
1. Buka menu **Penerimaan Barang**
2. Klik tombol **Export Excel**
3. File akan terdownload: `purchases-YYYY-MM-DD.xlsx`

---

### 4. Export Kartu Stok (Stock Movements)
**Route:** `GET /stock-movements/export`  
**Access:** Owner, Admin Gudang  
**File:** `StockMovementsExport.php`

**Data yang diexport:**
- Tanggal & Waktu
- SKU Produk
- Nama Produk
- Batch Number
- Tipe (IN/OUT/ADJUSTMENT/RETURN)
- Qty (Jumlah mutasi)
- Qty Sebelum
- Qty Sesudah
- Reference Type (Sale/Purchase/dll)
- Reference ID
- User
- Catatan

**Filter tersedia:**
- `start_date`: Filter tanggal mulai
- `end_date`: Filter tanggal akhir
- `product_id`: Filter by produk tertentu

**Contoh:**
```
/stock-movements/export?product_id=5&start_date=2026-01-01
```

**Cara akses:**
1. Buka menu **Kartu Stok**
2. Gunakan filter jika perlu (produk, tanggal)
3. Klik tombol **Export Excel**
4. File akan terdownload: `stock-movements-YYYY-MM-DD.xlsx`

---

### 5. Export Supplier
**Route:** `GET /suppliers/export`  
**Access:** Owner, Admin Gudang  
**File:** `SuppliersExport.php`

**Data yang diexport:**
- Kode Supplier
- Nama Supplier
- Kontak Person
- Telepon
- Email
- Alamat
- Kota
- Provinsi
- Kode Pos
- NPWP
- Status Aktif (Aktif/Tidak Aktif)
- Payment Terms (hari)
- Catatan

**Cara akses:**
1. Buka menu **Supplier**
2. Klik tombol **Export Excel**
3. File akan terdownload: `suppliers-YYYY-MM-DD.xlsx`

---

## Format Excel

Semua file export memiliki format standar:
- **Header baris pertama:** Bold, background hijau muda (#E2EFDA), border
- **Column widths:** Otomatis disesuaikan dengan isi data
- **Number format:** Currency dan decimal sesuai
- **Date format:** YYYY-MM-DD atau YYYY-MM-DD HH:mm:ss

---

## Technical Details

### Dependencies
```json
"maatwebsite/excel": "^3.1"
```

### Export Classes Location
```
app/Exports/
├── ProductsExport.php
├── SalesExport.php
├── PurchasesExport.php
├── StockMovementsExport.php
└── SuppliersExport.php
```

### Implements Interfaces
Semua export class mengimplementasikan:
- `FromCollection` - Data source dari Eloquent Collection
- `WithHeadings` - Menambahkan header row
- `WithMapping` - Transform data sebelum export
- `WithStyles` - Styling header dan cells
- `WithColumnWidths` - Set lebar kolom otomatis

### Example Code
```php
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

// Export all sales
Excel::download(new SalesExport(), 'sales.xlsx');

// Export with date filter
$startDate = '2026-01-01';
$endDate = '2026-01-31';
Excel::download(new SalesExport($startDate, $endDate), 'sales-january.xlsx');
```

---

## Security & Access Control

Export routes dilindungi oleh:
1. **Authentication middleware** - User harus login
2. **Role-based access:**
   - Products: semua user authenticated
   - Sales: semua user authenticated
   - Purchases: owner & admin_gudang only
   - Stock Movements: owner & admin_gudang only
   - Suppliers: owner & admin_gudang only

---

## Testing

### Manual Test
1. Login sebagai owner/kasir/admin_gudang
2. Buka halaman index (Products/Sales/dll)
3. Klik tombol "Export Excel"
4. Verifikasi file terdownload
5. Buka file dengan Excel/LibreOffice
6. Cek format, data, dan styling

### Test dengan Filter
```bash
# Test dengan date range
curl "http://localhost:8000/sales/export?start_date=2026-01-01&end_date=2026-01-31" \
  -H "Authorization: Bearer TOKEN" \
  -o test-sales.xlsx

# Test dengan product filter
curl "http://localhost:8000/stock-movements/export?product_id=5" \
  -H "Authorization: Bearer TOKEN" \
  -o test-movements.xlsx
```

---

## Troubleshooting

### Problem: "Class not found"
**Solution:**
```bash
composer dump-autoload
php artisan config:clear
```

### Problem: "Extension zip not found"
**Solution:**
1. Edit `php.ini`
2. Uncomment: `extension=zip`
3. Restart Apache/PHP-FPM

### Problem: Empty Excel file
**Solution:**
- Cek apakah ada data di database
- Cek query filter (date range, product_id)
- Review log: `storage/logs/laravel.log`

### Problem: Styling tidak muncul
**Solution:**
- Update Laravel Excel: `composer update maatwebsite/excel`
- Clear cache: `php artisan cache:clear`

---

## Future Improvements

Rencana pengembangan fitur export:
- [ ] Export dengan custom columns (user pilih kolom)
- [ ] Export ke PDF
- [ ] Export ke CSV
- [ ] Scheduled exports (daily/weekly)
- [ ] Email export results
- [ ] Export dengan charts/graphs
- [ ] Multi-sheet exports (detail + summary)

---

**Last Updated:** January 6, 2026  
**Version:** 1.0  
**Author:** Development Team
