# Panduan Penjualan Eceran (Per Kapsul/Tablet)

## Masalah yang Diselesaikan
Saat kasir menjual obat seperti **Sangobion Kapsul**, harga yang muncul adalah per box/strip (Rp 26.000), padahal **pasien hanya ingin beli 5 kapsul** saja.

## Solusi: Fitur Penjualan Eceran

Aplikasi sekarang mendukung **penjualan eceran** dengan sistem unit satuan:
- **Unit Kemasan**: Box, Strip, Botol (harga normal)
- **Unit Terkecil**: Kapsul, Tablet, ML (harga eceran)

---

## 🔧 Cara Setting Produk untuk Penjualan Eceran

### Langkah 1: Edit Produk
1. Login sebagai **Owner** atau **Admin Gudang**
2. Buka menu **Master Data** → **Produk**
3. Klik **Edit** pada produk yang ingin dijual eceran

### Langkah 2: Isi Data Unit Satuan
Tambahkan informasi berikut:

| Field | Contoh Sangobion | Keterangan |
|-------|-----------------|------------|
| **Unit Kemasan** | STRIP | Unit kemasan besar (BOX, STRIP, BOTOL) |
| **Unit Terkecil** | KAPSUL | Unit satuan terkecil (KAPSUL, TABLET, ML) |
| **Isi Per Kemasan** | 10 | Berapa unit terkecil dalam 1 kemasan |
| **Jual Eceran** | ✅ Centang | Aktifkan penjualan eceran |

### Contoh Data Lengkap Sangobion:
```
Nama Dagang: Sangobion Kapsul
Harga Jual: Rp 26.000 (per strip)
Unit Kemasan: STRIP
Unit Terkecil: KAPSUL
Isi Per Kemasan: 10
Jual Eceran: ✅ (centang)
```

**Harga Eceran Otomatis Dihitung:**
- Harga per kapsul = Rp 26.000 ÷ 10 = **Rp 2.600 per kapsul**

### Contoh Produk Lain:

#### Paracetamol Tablet Strip 10
```
Harga Jual: Rp 5.000 (per strip)
Unit Kemasan: STRIP
Unit Terkecil: TABLET
Isi Per Kemasan: 10
Harga Eceran: Rp 500 per tablet
```

#### Amoxicillin Box 100 Kapsul
```
Harga Jual: Rp 100.000 (per box)
Unit Kemasan: BOX
Unit Terkecil: KAPSUL
Isi Per Kemasan: 100
Harga Eceran: Rp 1.000 per kapsul
```

#### OBH Combi Sirup 60ml
```
Harga Jual: Rp 15.000 (per botol)
Unit Kemasan: BOTOL
Unit Terkecil: ML
Isi Per Kemasan: 60
Harga Eceran: Rp 250 per ml
```

---

## 💰 Cara Menjual Eceran di POS

### Skenario: Pasien Beli 5 Kapsul Sangobion

1. **Buka Transaksi POS**
   - Kasir login dan buka menu **POS Penjualan**

2. **Tambah Produk**
   - Pilih **Sangobion Kapsul** dari dropdown
   - Akan muncul info: `(STRIP / KAPSUL)`

3. **Pilih Unit Satuan**
   - Dropdown **Unit** akan menampilkan:
     - ✅ **STRIP - Rp 26.000** (per strip)
     - ✅ **KAPSUL - Rp 2.600** (per kapsul)
   
4. **Pilih KAPSUL dan Isi Qty**
   - Pilih unit: **KAPSUL**
   - Isi qty: **5**
   - Harga otomatis: **Rp 2.600**

5. **Total Otomatis Dihitung**
   - Subtotal: 5 × Rp 2.600 = **Rp 13.000**

6. **Proses Pembayaran**
   - Pasien bayar Rp 15.000
   - Kembalian: Rp 2.000

---

## 📊 Contoh Transaksi Campuran

Pasien beli:
- 2 strip Sangobion = 2 × Rp 26.000 = Rp 52.000
- 5 kapsul Sangobion = 5 × Rp 2.600 = Rp 13.000
- **Total: Rp 65.000**

Cara Input:
1. Tambah baris pertama:
   - Produk: Sangobion
   - Unit: **STRIP**
   - Qty: **2**

2. Tambah baris kedua:
   - Produk: Sangobion (pilih lagi)
   - Unit: **KAPSUL**
   - Qty: **5**

3. Total otomatis dihitung: **Rp 65.000**

---

## 🔍 Logika Stok (Penting!)

**Semua stok dihitung dalam unit kemasan** (untuk konsistensi batch tracking):

### Contoh: Jual 5 Kapsul Sangobion
- Stok batch berkurang: **0.5 strip** (5 kapsul ÷ 10 = 0.5)
- Sistem otomatis konversi dari unit terkecil ke kemasan

### Contoh: Jual 15 Tablet Paracetamol (1 strip = 10 tablet)
- Stok batch berkurang: **1.5 strip** (15 tablet ÷ 10 = 1.5)

---

## ✅ Checklist Setting Produk

Untuk setiap produk yang dijual eceran:

- [ ] Isi **Unit Kemasan** (STRIP, BOX, BOTOL, dll)
- [ ] Isi **Unit Terkecil** (KAPSUL, TABLET, ML, dll)
- [ ] Isi **Isi Per Kemasan** (angka konversi)
- [ ] Centang **Jual Eceran**
- [ ] Pastikan **Harga Jual** sudah diisi (harga per kemasan)

---

## ⚠️ Catatan Penting

### 1. Harga Eceran Otomatis
Sistem otomatis menghitung harga eceran:
```
Harga Eceran = Harga Jual ÷ Isi Per Kemasan
```

### 2. Stok Tetap Per Kemasan
- Stok disimpan dalam unit **kemasan** (untuk tracking batch/expired)
- Saat jual eceran, sistem otomatis konversi ke kemasan
- Contoh: Jual 3 kapsul dari strip 10 = kurangi 0.3 strip

### 3. Produk Tanpa Setting Eceran
Jika produk **tidak dicentang Jual Eceran**:
- Hanya bisa dijual per kemasan
- Dropdown unit hanya ada 1 pilihan

---

## 🎯 Tips Penggunaan

### Untuk Produk yang Sering Dijual Eceran:
- Sangobion, Vitamin C, Paracetamol
- **Wajib setting** unit kemasan dan terkecil

### Untuk Produk yang Tidak Dijual Eceran:
- Sirup dalam botol (jual utuh)
- Salep dalam tube (jual utuh)
- **Tidak perlu** setting jual eceran

### Harga Pembulatan:
Jika harga eceran tidak bulat (contoh: Rp 2.633,33):
- Sistem tetap simpan desimal
- Atau bisa edit manual harga per unit di form POS

---

## 📞 Bantuan

Jika ada pertanyaan atau butuh setting produk khusus:
1. Hubungi admin sistem
2. Cek dokumentasi `USER_GUIDE.md`
3. Review file `TROUBLESHOOTING.md`

---

**Dibuat untuk Toko Obat Ro Tua**  
**Update: Januari 2026**
