# CARA MENGGUNAKAN FITUR PENJUALAN ECERAN

## ⚠️ LANGKAH WAJIB SEBELUM MENGGUNAKAN

### 1. Jalankan Migration Database
Buka terminal/command prompt di folder aplikasi, lalu jalankan:

```bash
# Start MySQL terlebih dahulu (XAMPP/Laragon)
# Kemudian jalankan:
php artisan migrate
```

Ini akan menambahkan kolom baru: `unit_kemasan`, `unit_terkecil`, `isi_per_kemasan`, `jual_eceran`

---

## 📝 SETTING PRODUK SANGOBION (CONTOH)

### Cara Manual via Form:
1. Login sebagai **Owner** atau **Admin Gudang**
2. Buka **Master Data** → **Produk** 
3. Cari dan klik **Edit** pada **Sangobion Kapsul**
4. Isi field baru:
   - **Unit Kemasan**: `STRIP`
   - **Unit Terkecil**: `KAPSUL`
   - **Isi Per Kemasan**: `10`
   - **Jual Eceran**: ✅ **Centang**
5. Klik **Simpan**

### Atau via SQL (Cepat):
Buka phpMyAdmin atau MySQL client, jalankan:

```sql
-- Update Sangobion jika sudah ada di database
UPDATE products 
SET 
    unit_kemasan = 'STRIP',
    unit_terkecil = 'KAPSUL',
    isi_per_kemasan = 10,
    jual_eceran = 1
WHERE nama_dagang LIKE '%Sangobion%' 
  AND bentuk LIKE '%Kapsul%';
```

---

## 💰 CARA JUAL 5 KAPSUL SANGOBION

### Di Form POS Penjualan:
1. Pilih produk **Sangobion Kapsul**
2. Pada dropdown **Unit**, pilih **KAPSUL - Rp 2.600**
3. Isi **Qty**: `5`
4. Total otomatis: **Rp 13.000** (5 × 2.600)

---

## 🔍 VERIFIKASI

Setelah migration dan setting produk:
1. Buka **POS Penjualan**
2. Pilih **Sangobion Kapsul**
3. Lihat dropdown **Unit** - harus ada 2 pilihan:
   - STRIP - Rp 26.000
   - KAPSUL - Rp 2.600

Jika hanya ada 1 pilihan, berarti:
- Belum jalankan migration, ATAU
- Belum centang "Jual Eceran" di produk

---

## 📚 Dokumentasi Lengkap

Baca: `docs/PANDUAN_PENJUALAN_ECERAN.md`
