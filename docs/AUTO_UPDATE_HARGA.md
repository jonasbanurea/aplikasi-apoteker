# 🎯 Fitur Auto-Update Harga Produk

## Masalah yang Diselesaikan

**SEBELUM:**
```
❌ Batch#1: Harga Beli Rp 1.000 → Harga Jual Rp 1.200 (margin 20%)
❌ Batch#2: Harga Beli Rp 1.100 → Harga Jual tetap Rp 1.200 (margin 9% ❌)
❌ Harus update manual harga jual setiap kali harga beli berubah
```

**SESUDAH (dengan fitur ini):**
```
✅ Batch#1: Harga Beli Rp 1.000 → Harga Jual Rp 1.200 (margin 20%)
✅ Batch#2: Harga Beli Rp 1.100 → Harga Jual otomatis Rp 1.300 (margin 20% ✅)
✅ Sistem otomatis update harga jual dengan margin yang konsisten
```

---

## 🚀 Cara Kerja

Ketika Anda **menerima barang** dengan **harga beli berbeda** dari master produk:

1. **Sistem cek:** Apakah selisih harga beli > threshold (default 2%)
2. **Sistem hitung:** Harga jual baru = Harga Beli Baru × (1 + Margin)
3. **Sistem update:** Master produk (harga_beli & harga_jual) otomatis
4. **Sistem bulatkan:** Harga jual dibulatkan ke ratusan (Rp 1.250 → Rp 1.300)

**Contoh:**
```
Produk: Paracetamol 500mg
Master saat ini: Harga Beli Rp 10.000, Harga Jual Rp 12.000
Golongan: RESEP (margin 20%)

Terima batch baru dengan Harga Beli Rp 11.000:
→ Sistem cek: (11.000 - 10.000) / 10.000 = 10% > 2% threshold ✅
→ Sistem hitung: 11.000 × 1.20 = Rp 13.200
→ Sistem bulatkan: Rp 13.200 → Rp 13.200
→ Master update: Harga Beli Rp 11.000, Harga Jual Rp 13.200
```

---

## ⚙️ Pengaturan (config/pricing.php)

### 1. Aktifkan/Nonaktifkan Fitur

```php
// .env
AUTO_UPDATE_PRICE_ON_PURCHASE=true   # true = aktif, false = matikan
```

### 2. Margin Default

```php
// .env
DEFAULT_MARGIN_PERCENTAGE=0.20   # 0.20 = 20% margin
```

**Atau edit langsung di `config/pricing.php`:**
```php
'default_margin_percentage' => 0.20,  // 20%
```

### 3. Margin Per Golongan

Anda bisa set margin berbeda per golongan obat:

```php
// config/pricing.php
'margin_by_golongan' => [
    'OTC' => 0.25,              // 25% untuk obat bebas
    'BEBAS_TERBATAS' => 0.23,   // 23% 
    'RESEP' => 0.20,            // 20% untuk obat keras
    'PSIKOTROPIKA' => 0.18,     // 18%
    'NARKOTIKA' => 0.18,        // 18%
],
```

**Cara mengubahnya:**
1. Buka file `config/pricing.php`
2. Edit nilai margin sesuai kebutuhan
3. Simpan file
4. Tidak perlu restart server (Laravel auto-reload config)

### 4. Threshold Update

Hanya update jika selisih harga beli cukup signifikan:

```php
// .env
PRICE_UPDATE_THRESHOLD=0.02   # 0.02 = 2%

// Contoh:
// Harga lama Rp 10.000, baru Rp 10.150 → selisih 1.5% < 2% → SKIP
// Harga lama Rp 10.000, baru Rp 10.300 → selisih 3% > 2% → UPDATE ✅
```

**Rekomendasi:**
- `0.02` (2%) = update hanya jika selisih lumayan
- `0.05` (5%) = update hanya jika selisih besar
- `0` = update meskipun beda Rp 1

### 5. Pembulatan Harga Jual

```php
// .env
ROUND_SELLING_PRICE_TO=100   # Bulatkan ke ratusan

// Contoh:
// 100  = Rp 12.250 → Rp 12.300 (ratusan)
// 1000 = Rp 12.800 → Rp 13.000 (ribuan)
// 1    = Rp 12.250 (tidak dibulatkan)
```

---

## 📖 Contoh Penggunaan

### Scenario 1: Harga Naik

**Data Awal:**
- Produk: Sangobion Strip
- Golongan: OTC (margin 25%)
- Harga Beli: Rp 20.000
- Harga Jual: Rp 25.000

**Terima Batch Baru:**
- Batch#2 dengan Harga Beli: Rp 22.000

**Hasil Otomatis:**
```
Harga Beli: Rp 20.000 → Rp 22.000 ✅ update
Harga Jual: Rp 25.000 → Rp 27.500 ✅ update
Margin tetap: 25%
```

### Scenario 2: Harga Turun

**Data Awal:**
- Produk: Amoxicillin 500mg
- Golongan: RESEP (margin 20%)
- Harga Beli: Rp 15.000
- Harga Jual: Rp 18.000

**Terima Batch Baru:**
- Batch#3 dengan Harga Beli: Rp 13.000

**Hasil Otomatis:**
```
Harga Beli: Rp 15.000 → Rp 13.000 ✅ update
Harga Jual: Rp 18.000 → Rp 15.600 ✅ update
Margin tetap: 20%
```

### Scenario 3: Selisih Kecil (Skip)

**Data Awal:**
- Produk: Panadol
- Harga Beli: Rp 10.000

**Terima Batch Baru:**
- Batch#2 dengan Harga Beli: Rp 10.100 (selisih 1% < 2% threshold)

**Hasil:**
```
❌ TIDAK UPDATE (selisih terlalu kecil)
Harga tetap: Rp 10.000
```

---

## 🔍 Monitoring & Log

Setiap kali sistem update harga, akan tercatat di **log file**:

**File Log:** `storage/logs/laravel.log`

**Contoh Log:**
```
[2026-02-03 10:30:00] local.INFO: Auto-update harga produk
{
    "product_id": 123,
    "sku": "OBT-0050",
    "nama": "Sangobion Strip",
    "old_cost": 20000,
    "new_cost": 22000,
    "new_selling": 27500,
    "margin": "25%"
}
```

**Cara Cek Log:**
1. Buka folder `storage/logs/`
2. Buka file `laravel.log`
3. Cari kata kunci: `Auto-update harga produk`

---

## ⚠️ Catatan Penting

### 1. Harga di Batch vs Harga di Produk

- **Batch:** Menyimpan harga beli **historis** (tidak berubah)
- **Produk:** Menyimpan harga **master** (bisa berubah otomatis)

**Contoh:**
```
Produk: Paracetamol
Master Produk: Harga Beli Rp 11.000 (terbaru)

Batch#1: Cost Price Rp 10.000 (historis, tidak berubah)
Batch#2: Cost Price Rp 11.000 (historis, tidak berubah)
```

### 2. Penjualan Menggunakan Harga Batch

Saat **penjualan**, sistem akan:
- Ambil stok dari batch (FEFO/FIFO)
- **Harga jual** diambil dari **master produk** (bukan dari batch)
- Cost (HPP) diambil dari batch untuk perhitungan margin

### 3. Update Manual Tetap Bisa

Jika Anda ingin **override** harga manual:
1. Buka **Master Data → Produk**
2. Edit produk
3. Ubah harga sesuai keinginan
4. Simpan

**Update manual tidak akan ditimpa otomatis** kecuali ada batch baru dengan selisih > threshold.

### 4. Konsinyasi

Untuk produk **konsinyasi**, Anda bisa:
- **Opsi A:** Matikan auto-update untuk produk tertentu (manual di kode)
- **Opsi B:** Set margin konsinyasi berbeda di config

---

## 🛠️ Troubleshooting

### Harga Tidak Update Otomatis

**Kemungkinan Penyebab:**

1. **Fitur dimatikan**
   ```php
   // Cek .env
   AUTO_UPDATE_PRICE_ON_PURCHASE=true  ✅
   ```

2. **Selisih < Threshold**
   ```php
   // Turunkan threshold di .env
   PRICE_UPDATE_THRESHOLD=0.01  # 1%
   ```

3. **Harga batch sama dengan master**
   ```
   Batch baru: Rp 10.000
   Master: Rp 10.000
   → Tidak ada perubahan, tidak update
   ```

### Harga Jual Terlalu Tinggi/Rendah

**Solusi:**
1. Cek margin di `config/pricing.php`
2. Edit margin sesuai golongan
3. Atau update manual harga jual

### Pembulatan Tidak Sesuai

**Solusi:**
```php
// Edit di .env
ROUND_SELLING_PRICE_TO=100   # Ratusan
ROUND_SELLING_PRICE_TO=1000  # Ribuan
ROUND_SELLING_PRICE_TO=1     # Tidak dibulatkan
```

---

## 🧪 Testing

### Test Manual

1. **Cek harga produk saat ini:**
   ```sql
   SELECT sku, nama_dagang, harga_beli, harga_jual 
   FROM products 
   WHERE sku = 'OBT-0001';
   ```

2. **Buat penerimaan barang dengan harga berbeda:**
   - Buka **Penerimaan Barang → Penerimaan Baru**
   - Pilih produk OBT-0001
   - Input harga beli berbeda (misal Rp 15.000 jika sebelumnya Rp 10.000)
   - Simpan

3. **Cek harga setelah update:**
   ```sql
   SELECT sku, nama_dagang, harga_beli, harga_jual 
   FROM products 
   WHERE sku = 'OBT-0001';
   ```

4. **Cek log:**
   - Buka `storage/logs/laravel.log`
   - Cari: `Auto-update harga produk`

---

## 📋 Konfigurasi Rekomendasi

### Toko Retail Kecil
```php
'auto_update_price_on_purchase' => true,
'default_margin_percentage' => 0.25,  // 25%
'price_update_threshold_percentage' => 0.03,  // 3%
'round_selling_price_to' => 100,  // Ratusan
```

### Apotek
```php
'auto_update_price_on_purchase' => true,
'margin_by_golongan' => [
    'OTC' => 0.30,              // 30%
    'BEBAS_TERBATAS' => 0.25,   // 25%
    'RESEP' => 0.20,            // 20%
    'PSIKOTROPIKA' => 0.15,     // 15%
    'NARKOTIKA' => 0.15,        // 15%
],
'price_update_threshold_percentage' => 0.02,  // 2%
'round_selling_price_to' => 100,
```

### Grosir/Distributor
```php
'auto_update_price_on_purchase' => true,
'default_margin_percentage' => 0.10,  // 10%
'price_update_threshold_percentage' => 0.01,  // 1%
'round_selling_price_to' => 1000,  // Ribuan
```

---

## 🎓 FAQ

### Q: Apakah harga batch lama ikut berubah?
**A:** Tidak. Harga di batch adalah **historis** dan tidak berubah. Hanya harga di **master produk** yang update.

### Q: Bagaimana kalau saya tidak mau auto-update?
**A:** Set di `.env`: `AUTO_UPDATE_PRICE_ON_PURCHASE=false`

### Q: Bisa set margin berbeda per produk individual?
**A:** Fitur ini belum ada. Saat ini margin per golongan saja. Tapi Anda bisa update manual kapan saja.

### Q: Apakah batch yang sudah terjual terdampak?
**A:** Tidak. Transaksi penjualan yang sudah terjadi tidak berubah.

### Q: Bagaimana dengan harga eceran?
**A:** Harga eceran otomatis dihitung dari `harga_jual / isi_per_kemasan`. Jadi jika harga jual update, harga eceran juga otomatis update.

---

## 📞 Support

Jika ada pertanyaan atau masalah, silakan:
1. Cek log di `storage/logs/laravel.log`
2. Cek konfigurasi di `config/pricing.php`
3. Test dengan data dummy dulu

---

## ✅ Checklist Implementasi

- [x] Config file `config/pricing.php` dibuat
- [x] Method `updateProductPrice()` ditambahkan di PurchaseController
- [x] Auto-update dipanggil saat `store()` penerimaan barang
- [x] Auto-update dipanggil saat `update()` penerimaan barang
- [x] Logging untuk monitoring
- [x] Dokumentasi lengkap

---

## 🎉 Selesai!

Fitur auto-update harga sudah aktif. Setiap kali terima barang dengan harga beli berbeda, sistem akan **otomatis menghitung dan update harga jual** dengan margin yang konsisten.

**Tidak perlu lagi khawatir margin menurun karena lupa update harga jual! 🚀**
