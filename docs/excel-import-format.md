# Format Excel untuk Import Produk

## Struktur File

File Excel harus memiliki kolom-kolom berikut pada baris pertama (header):

| Column | Wajib | Type | Contoh | Keterangan |
|--------|-------|------|--------|------------|
| NO | Ya | Number | 1, 2, 3 | Nomor urut |
| NAMA BARANG | Ya | Text | PARACETAMOL 500MG | Nama produk/obat |
| SEDIAAN | Ya | Text | TAB, KAPSUL, SIRUP | Bentuk sediaan |
| LOK BARANG | Tidak | Text | RAK A1, RAK B2 | Lokasi penyimpanan |
| STOK | Ya | Number | 100, 50, 0 | Stok awal (bisa 0) |
| KATEGORI | Ya | Text | PRODUK BEBAS | Kategori/golongan |
| HRG BELI | Ya | Number | 5000 | Harga beli per unit |
| MARGIN | Tidak | Decimal | 0.2, 0.15 | Margin keuntungan (0.2 = 20%) |
| HRG JUAL | Ya | Number | 6000 | Harga jual per unit |
| EXP DATE | Tidak | Date | 31/12/2025 | Tanggal kadaluarsa |

## Mapping Kategori

Kategori di Excel akan dimapping ke golongan produk:

| Kategori Excel | Golongan System |
|----------------|-----------------|
| PRODUK BEBAS | OTC |
| PRODUK BEBAS TERBATAS | BEBAS_TERBATAS |
| PRODUK KERAS | RESEP |
| PRODUK RESEP | RESEP |
| PRODUK PSIKOTROPIKA | PSIKOTROPIKA |
| PRODUK NARKOTIKA | NARKOTIKA |

## Mapping Sediaan

Sediaan di Excel akan dimapping ke bentuk sediaan standard:

| Sediaan Excel | Bentuk System |
|---------------|---------------|
| TAB, TABLET, KAPLET | TABLET |
| KAPSUL, CAPS, KPS | KAPSUL |
| SIRUP, SYR, SYRUP | SIRUP |
| SALEP, CREAM, GEL | SALEP/KRIM |
| BOTOL, BTL | CAIRAN |
| TUBE, TUB | SALEP/KRIM |
| SASET, SACHET | SERBUK |
| BTG, BATANG | BATANG |
| BKS, BUNGKUS, BOX | BOX/PACK |
| PCS | PCS |

## Contoh Data

```
NO | NAMA BARANG              | SEDIAAN | LOK BARANG | STOK | KATEGORI      | HRG BELI | MARGIN | HRG JUAL | EXP DATE
1  | PARACETAMOL 500MG       | TAB     | RAK A1     | 100  | PRODUK BEBAS  | 100      | 0.5    | 150      | 31/12/2025
2  | AMOXICILLIN 500MG       | KAPSUL  | RAK A2     | 50   | PRODUK KERAS  | 500      | 0.3    | 650      | 30/06/2026
3  | OBH COMBI SIRUP         | BOTOL   | RAK B1     | 25   | PRODUK BEBAS  | 8000     | 0.25   | 10000    |
4  | BETADINE SALEP 10G      | TUBE    | RAK C1     | 30   | PRODUK BEBAS  | 12000    | 0.2    | 14400    | 15/03/2026
```

## Aturan Import

### 1. Produk Baru
- Akan dibuat dengan SKU auto-generate: OBT00001, OBT00002, dst
- Nama generik diset sama dengan nama dagang
- Kekuatan dosis diset default: "-"
- Wajib resep otomatis TRUE jika golongan PSIKOTROPIKA atau NARKOTIKA
- Minimal stok diset setengah dari stok awal
- Konsinyasi default FALSE

### 2. Produk yang Sudah Ada
- Akan di-skip (tidak diupdate)
- Ditentukan berdasarkan nama_dagang yang sama
- Muncul warning di console

### 3. Stock Batch
Jika STOK > 0, akan dibuat:
- Batch number: INIT-YYYYMMDD-{product_id}
- Qty on hand: sesuai STOK
- Cost price: sesuai HRG BELI
- Expired date: dari EXP DATE atau +2 tahun dari sekarang
- Received at: tanggal import

### 4. Stock Movement
Untuk setiap batch yang dibuat:
- Type: IN
- Ref type: initial_stock
- Qty: sesuai STOK
- User: Owner (ID: 1)
- Notes: "Stok awal dari data Excel - Batch: {batch_number}"

## Tips

### 1. Persiapan File
- Pastikan baris pertama adalah header
- Tidak ada baris kosong di tengah data
- Gunakan format date yang konsisten
- Nama kolom harus PERSIS seperti di tabel (case sensitive)

### 2. Data Cleaning
- Hapus spasi berlebih di awal/akhir nama barang
- Pastikan angka tidak ada format currency (Rp, $, dll)
- Margin dalam bentuk decimal (0.2 bukan 20%)
- Stok harus angka bulat

### 3. Testing
- Import file sample terlebih dahulu
- Cek hasil di halaman Produk
- Cek Stock Batches untuk verifikasi stok
- Cek Stock Movements untuk audit trail

### 4. Backup
- SELALU backup database sebelum import
- Simpan file Excel original sebagai backup
- Export data existing sebelum import data baru

## Lokasi File

File Excel untuk import harus ditempatkan di:
```
docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx
```

Atau edit path di file seeder:
```php
// database/seeders/ProductFromExcelSeeder.php
$excelFile = database_path('../docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx');
```

## Command

```bash
# Import produk dari Excel
php artisan db:seed --class=ProductFromExcelSeeder

# Import dengan fresh database (reset semua data)
php artisan migrate:fresh --seed

# Hanya import produk (tanpa reset data lain)
php artisan db:seed --class=ProductFromExcelSeeder
```

## Error Handling

### "Excel file not found"
- Cek path file Excel sudah benar
- Pastikan file ada di folder `docs/`
- Cek nama file sesuai dengan yang di seeder

### "Column not found"
- Cek nama kolom di Excel sudah sesuai
- Pastikan tidak ada typo
- Kolom harus di baris pertama

### "Product already exists"
- Produk dengan nama yang sama sudah ada
- Import akan skip produk ini
- Untuk update, hapus dulu produk lama atau edit manual

### "SQLSTATE error"
- Database connection error
- Pastikan MySQL running
- Cek .env database config

## Support

Untuk bantuan lebih lanjut, hubungi developer atau lihat dokumentasi lengkap di:
`docs/import-export-products.md`
