# Solusi Masalah Harga Tidak Update Otomatis

## 🎯 Masalah Anda

**Scenario:**
1. Batch#1: Harga Beli Rp 1.000 → Harga Jual Rp 1.200 ✅
2. Batch#2: Harga Beli Rp 1.100 → Harga Jual tetap Rp 1.200 ❌ (harusnya Rp 1.300)

**Penyebab:**
Sistem lama tidak otomatis update harga jual saat harga beli berubah.

---

## ✅ Solusi: Fitur Auto-Update Harga (SUDAH DIIMPLEMENTASI)

Sekarang sistem akan **otomatis update harga jual** dengan margin tetap 20% (atau sesuai setting) setiap kali Anda terima barang dengan harga beli berbeda.

---

## 🚀 Cara Mengaktifkan

### Langkah 1: Copy File Config

```bash
# Di terminal/PowerShell
cd D:\PROJECT\APOTEKER\Aplikasi
copy config\pricing.php config\pricing.php
```

### Langkah 2: Setting Environment

Buka file `.env` dan tambahkan/edit:

```env
# Auto-update harga otomatis
AUTO_UPDATE_PRICE_ON_PURCHASE=true

# Margin default 20%
DEFAULT_MARGIN_PERCENTAGE=0.20

# Update jika selisih > 2%
PRICE_UPDATE_THRESHOLD=0.02

# Bulatkan ke ratusan
ROUND_SELLING_PRICE_TO=100
```

### Langkah 3: Restart Server (jika pakai `php artisan serve`)

```bash
# Stop server (Ctrl+C)
# Start ulang
php artisan serve
```

---

## 📖 Cara Pakai

Setelah aktif, **tidak perlu lakukan apa-apa**. Sistem otomatis:

### Contoh:

**Terima Batch Baru:**
```
Produk: Paracetamol 500mg
Harga Beli Lama: Rp 1.000
Harga Jual Lama: Rp 1.200

Input Penerimaan Barang:
- Harga Beli Baru: Rp 1.100

Hasil Otomatis:
✅ Harga Beli: Rp 1.000 → Rp 1.100 (update)
✅ Harga Jual: Rp 1.200 → Rp 1.300 (update, margin 20%)
```

**Tidak perlu edit manual lagi!**

---

## ⚙️ Pengaturan Margin

### Default: Semua Produk 20%

Jika ingin **margin berbeda per golongan**, edit file `config/pricing.php`:

```php
'margin_by_golongan' => [
    'OTC' => 0.25,              // 25% untuk obat bebas
    'BEBAS_TERBATAS' => 0.23,   // 23%
    'RESEP' => 0.20,            // 20% untuk obat keras
    'PSIKOTROPIKA' => 0.18,     // 18%
    'NARKOTIKA' => 0.18,        // 18%
],
```

**Simpan file, tidak perlu restart.**

---

## 🧪 Testing

### Test Sederhana:

1. **Cek produk saat ini:**
   - Buka **Master Data → Produk**
   - Lihat harga beli & jual produk tertentu

2. **Buat penerimaan barang:**
   - **Penerimaan Barang → Penerimaan Baru**
   - Pilih produk
   - **Input harga beli berbeda** (misal +10%)
   - Simpan

3. **Cek produk lagi:**
   - Buka **Master Data → Produk**
   - **Harga beli & jual otomatis update!** ✅

---

## 🔍 Monitoring

Sistem mencatat setiap perubahan harga di **log file**:

**File:** `storage/logs/laravel.log`

**Isi Log:**
```
[2026-02-03 10:30:00] Auto-update harga produk
- SKU: OBT-0050
- Nama: Paracetamol
- Harga Beli Lama: 1000
- Harga Beli Baru: 1100
- Harga Jual Baru: 1300
- Margin: 20%
```

---

## ⚠️ Catatan Penting

### 1. Update Hanya Jika Selisih Signifikan

Sistem hanya update jika selisih > 2% (default):
- Lama Rp 1.000, Baru Rp 1.015 → **SKIP** (selisih 1.5%)
- Lama Rp 1.000, Baru Rp 1.100 → **UPDATE** (selisih 10%)

**Kenapa?** Supaya tidak update terus-menerus karena beda kecil.

### 2. Harga Batch Tidak Berubah

- **Harga di batch:** Historis, tidak berubah
- **Harga di produk:** Master, otomatis update

**Contoh:**
```
Batch#1: Cost Rp 1.000 (tetap)
Batch#2: Cost Rp 1.100 (tetap)
Master Produk: Harga Beli Rp 1.100 (update ke yang terbaru)
```

### 3. Bisa Update Manual Kapan Saja

Jika mau override harga:
- Buka **Master Data → Produk → Edit**
- Ubah harga manual
- Simpan

Update manual **tidak ditimpa** kecuali ada batch baru dengan selisih > threshold.

---

## 🛠️ Troubleshooting

### Harga Tidak Update

**Cek:**
1. `.env` → `AUTO_UPDATE_PRICE_ON_PURCHASE=true` ✅
2. Selisih harga > 2%? (atau turunkan threshold)
3. Cek log: `storage/logs/laravel.log`

### Margin Tidak Sesuai

**Solusi:**
- Edit margin di `config/pricing.php`
- Atau update manual harga jual

---

## 📞 Butuh Bantuan?

**Dokumentasi Lengkap:** `docs/AUTO_UPDATE_HARGA.md`

**Test dulu dengan data dummy sebelum pakai di produksi!**

---

## ✅ Checklist

- [ ] Copy file `config/pricing.php`
- [ ] Edit `.env` tambahkan config pricing
- [ ] Restart server
- [ ] Test dengan 1 produk dummy
- [ ] Cek hasilnya di master produk
- [ ] Cek log di `storage/logs/laravel.log`
- [ ] Deploy ke produksi

---

## 🎉 Selesai!

Masalah harga tidak update otomatis sudah teratasi. **Margin akan selalu konsisten!** 🚀
