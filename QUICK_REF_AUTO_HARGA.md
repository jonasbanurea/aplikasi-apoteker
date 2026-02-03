# ⚡ Quick Reference: Auto-Update Harga

## Setup (5 Menit)

### 1. Edit File .env
```env
AUTO_UPDATE_PRICE_ON_PURCHASE=true
DEFAULT_MARGIN_PERCENTAGE=0.20
PRICE_UPDATE_THRESHOLD=0.02
ROUND_SELLING_PRICE_TO=100
```

### 2. Restart Server (jika pakai artisan serve)
```bash
php artisan serve
```

---

## Cara Kerja

**Otomatis saat terima barang:**
```
Harga Beli Baru → Cek Selisih → Hitung Margin → Update Master
```

**Contoh:**
- Batch#1: Beli Rp 1.000 → Jual Rp 1.200
- Batch#2: Beli Rp 1.100 → Jual **otomatis** Rp 1.300 ✅

---

## Setting Margin per Golongan

Edit `config/pricing.php`:

```php
'margin_by_golongan' => [
    'OTC' => 0.25,              // 25%
    'BEBAS_TERBATAS' => 0.23,   // 23%
    'RESEP' => 0.20,            // 20%
    'PSIKOTROPIKA' => 0.18,     // 18%
    'NARKOTIKA' => 0.18,        // 18%
],
```

---

## Testing

1. Cek harga produk sekarang
2. Buat penerimaan barang dengan harga beda
3. Cek lagi → harga otomatis update! ✅

---

## Log

Cek `storage/logs/laravel.log` untuk melihat history update harga.

---

## Matikan Fitur

```env
AUTO_UPDATE_PRICE_ON_PURCHASE=false
```

---

**Dokumentasi Lengkap:** `docs/AUTO_UPDATE_HARGA.md`
