# Import & Export Produk - Implementation Summary

## Overview
Fitur untuk mengimport data produk dari file Excel dan export data produk ke Excel dengan format yang konsisten.

## Files Created/Modified

### 1. New Files Created
- `app/Exports/ProductsExport.php` - Export class untuk mengekspor produk ke Excel
- `database/seeders/ProductFromExcelSeeder.php` - Seeder untuk import produk dari Excel
- `config/excel.php` - Konfigurasi Laravel Excel (published)

### 2. Modified Files
- `app/Http/Controllers/ProductController.php` - Menambahkan method `export()`
- `resources/views/products/index.blade.php` - Menambahkan tombol Export Excel
- `routes/web.php` - Menambahkan route `products.export`
- `database/seeders/DatabaseSeeder.php` - Menambahkan ProductFromExcelSeeder
- `README.md` - Menambahkan dokumentasi Import & Export
- `composer.json` - Menambahkan dependency `maatwebsite/excel`

## Features

### Import dari Excel
**File:** `database/seeders/ProductFromExcelSeeder.php`

**Format Excel yang Diharapkan:**
| Column | Description | Example |
|--------|-------------|---------|
| NO | Nomor urut | 1, 2, 3 |
| NAMA BARANG | Nama produk | PARACETAMOL 500MG |
| SEDIAAN | Satuan/bentuk | TAB, KAPSUL, SIRUP |
| LOK BARANG | Lokasi rak | RAK A1, RAK B2 |
| STOK | Jumlah stok awal | 100 |
| KATEGORI | Kategori produk | PRODUK BEBAS, PRODUK KERAS |
| HRG BELI | Harga beli | 5000 |
| MARGIN | Margin (decimal) | 0.2 (=20%) |
| HRG JUAL | Harga jual | 6000 |
| EXP DATE | Tanggal expired | Excel date atau kosong |

**Mapping:**
- `KATEGORI` → `golongan`:
  - PRODUK BEBAS → OTC
  - PRODUK BEBAS TERBATAS → BEBAS_TERBATAS
  - PRODUK KERAS/RESEP → RESEP
  - PRODUK PSIKOTROPIKA → PSIKOTROPIKA
  - PRODUK NARKOTIKA → NARKOTIKA

- `SEDIAAN` → `bentuk`:
  - TAB/TABLET/KAPLET → TABLET
  - KAPSUL/CAPS → KAPSUL
  - SIRUP/SYR → SIRUP
  - SALEP/CREAM/GEL → SALEP/KRIM
  - BOTOL/BTL → CAIRAN
  - BTG/BATANG → BATANG
  - BKS/BOX → BOX/PACK
  - dll.

**Fitur:**
- Auto-generate SKU (format: OBT00001, OBT00002, dst)
- Skip produk yang sudah ada (berdasarkan nama_dagang)
- Membuat stock batch awal jika STOK > 0
- Membuat stock movement record untuk audit
- Konversi Excel date ke format yang benar
- Set minimal_stok otomatis (setengah dari stok awal)

**Cara Penggunaan:**
```bash
php artisan db:seed --class=ProductFromExcelSeeder
```

### Export ke Excel
**File:** `app/Exports/ProductsExport.php`

**Fitur:**
- Export semua produk dengan stok terkini
- Format Excel yang konsisten dengan format import
- Hitung total stok dari semua batch aktif
- Ambil tanggal expired terdekat
- Hitung margin secara otomatis
- Styling header (background biru, teks putih, bold)
- Column width otomatis untuk readability
- Urut berdasarkan lokasi_rak dan nama_dagang

**Output Columns:**
- NO (ID produk)
- NAMA BARANG (nama_dagang)
- SEDIAAN (satuan)
- LOK BARANG (lokasi_rak)
- STOK (total dari semua batch)
- KATEGORI (mapped dari golongan)
- HRG BELI (harga_beli)
- MARGIN (calculated)
- HRG JUAL (harga_jual)
- EXP DATE (earliest expiry)

**Cara Penggunaan:**
1. Login sebagai user yang punya akses ke halaman Produk
2. Buka menu Produk
3. Klik tombol "Export Excel"
4. File akan didownload dengan nama: `daftar-obat-YYYY-MM-DD-HHMMSS.xlsx`

## UI Updates

### Products Index Page
- Menambahkan tombol "Export Excel" dengan icon Bootstrap Icons
- Tombol berwarna hijau (success) untuk membedakan dari tombol lain
- Posisi di sebelah tombol "Tambah Produk"
- Accessible untuk semua role yang bisa akses halaman produk

## Dependencies

### Laravel Excel (maatwebsite/excel)
```bash
composer require maatwebsite/excel
```

**Required PHP Extensions:**
- `ext-gd` - untuk manipulasi gambar
- `ext-zip` - untuk membuat file Excel

Kedua extension sudah dienable di `php.ini` XAMPP.

## Database Impact

### Stock Batches
Setiap produk yang diimport dengan STOK > 0 akan otomatis membuat:
- 1 record di `stock_batches` dengan batch_no: `INIT-YYYYMMDD-{product_id}`
- qty_on_hand sesuai STOK dari Excel
- expired_date dari kolom EXP DATE (atau +2 tahun jika kosong)

### Stock Movements
Setiap stock batch yang dibuat akan mencatat:
- type: 'IN'
- ref_type: 'initial_stock'
- qty: sesuai STOK
- user_id: 1 (Owner)
- notes: "Stok awal dari data Excel - Batch: ..."

## Testing

### Import Test
1. Pastikan file Excel ada di `docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx`
2. Run: `php artisan db:seed --class=ProductFromExcelSeeder`
3. Verify di halaman Produk dan Stock Batches
4. Check stock_movements untuk audit trail

### Export Test
1. Login ke aplikasi
2. Buka halaman Produk
3. Klik tombol "Export Excel"
4. Buka file yang didownload
5. Verify format dan data sesuai dengan database

## Notes

### Skipped Features
- Update existing products (hanya create baru)
- Delete products via import
- Bulk update harga via import
- Multiple sheet support

### Future Enhancements
- Import via upload form (tidak perlu edit seeder)
- Template download untuk format yang benar
- Preview sebelum import
- Error handling yang lebih detail dengan laporan
- Support untuk update existing products
- Bulk operations (update harga, update rak, dll)
- Import supplier data
- Export dengan filter (by kategori, by rak, dll)

## Troubleshooting

### Error: Column not found in stock_batches
Solution: Database schema sudah disesuaikan dengan yang ada di migrations.

### Error: PHP extension not enabled
Solution: Edit `D:\xampp\php\php.ini`:
```ini
extension=gd
extension=zip
```
Restart Apache.

### Import gagal: Excel file not found
Solution: Pastikan file Excel ada di folder `docs/` di root project.

### Export menghasilkan file kosong
Solution: Pastikan ada data produk di database.

## Success Metrics
✅ 40+ produk berhasil diimport dari Excel
✅ Stock batches dan movements tercatat dengan benar
✅ Export menghasilkan format yang konsisten
✅ UI terintegrasi dengan baik
✅ Dokumentasi lengkap di README

## Completed: January 4, 2026
