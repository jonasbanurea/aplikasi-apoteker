# 🔄 Panduan Update Aplikasi untuk Customer

**Update Terbaru:** 11 Januari 2026  
**Untuk:** Toko Obat Ro Tua

---

## 🎯 CARA PALING MUDAH (Recommended untuk Customer)

### **Langkah Singkat:**

1. **Buka Command Prompt / PowerShell**
   - Klik kanan Start Menu → Windows Terminal atau Command Prompt

2. **Masuk ke folder aplikasi:**
   ```bash
   cd C:\projects\toko-obat
   ```
   *(Sesuaikan dengan lokasi folder aplikasi Anda)*

3. **Pull update dari GitHub:**
   ```bash
   git pull origin main
   ```

4. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

5. **Selesai! ✅**
   - Refresh browser
   - Login kembali
   - Fitur baru siap digunakan

---

## 🆕 Update Terbaru (11 Januari 2026)

### Fitur Baru yang Ditambahkan:

#### 1️⃣ **Edit Expired Date Langsung**
   - Bisa edit tanggal expired obat tanpa edit penerimaan
   - Menu: **Stok** → **Stok per Batch** → Klik **Edit**
   - Bisa edit: Expired Date, Batch No, Qty, Cost Price

#### 2️⃣ **Filter Stok per Batch**
   - Filter berdasarkan produk tertentu
   - Filter obat yang mendekati expired (30 hari)

---

## ⚠️ Jika Ada Error "Your local changes..."

Artinya ada file yang diubah di laptop customer. Solusi:

```bash
# Simpan perubahan lokal dulu
git stash

# Pull update
git pull origin main

# Kembalikan perubahan lokal (jika perlu)
git stash pop
```

**Atau reset total (HATI-HATI!):**
```bash
git reset --hard origin/main
git pull origin main
```

---

## 📱 Alternatif: Update Manual (Tanpa Git)

**Jika git error atau tidak bisa pull:**

### 1. **Download file ZIP dari GitHub:**
   - Buka: https://github.com/jonasbanurea/aplikasi-apoteker
   - Klik tombol **Code** (hijau) → **Download ZIP**

### 2. **Backup file penting:**
   - Copy file `.env` ke tempat aman
   - Backup database via phpMyAdmin

### 3. **Extract dan replace folder:**
   - Extract ZIP ke folder baru
   - Copy file `.env` dari backup ke folder baru
   - Install composer:
     ```bash
     composer install
     ```

### 4. **Test aplikasi:**
   - Jalankan: `start_aplikasi.bat`
   - Login dan test fitur

---

## ✅ Cara Cek Update Berhasil

### **Cek Fitur Edit Expired Date:**
1. Login sebagai Owner/Admin Gudang
2. Buka menu **Stok** → **Stok per Batch**
3. Harus ada tombol **Edit** (ikon pensil) di setiap baris
4. Klik Edit → Ubah Expired Date → Simpan
5. Jika berhasil = Update berhasil! ✅

### **Cek via Command:**
```bash
cd C:\projects\toko-obat
git log --oneline -5
```
Output terakhir harus:
```
0330a2f feat: Tambah fitur edit stock batch untuk update expired date
```

---

## 🔄 Update History

| Tanggal | Update | Fitur Utama |
|---------|--------|-------------|
| 11 Jan 2026 | Stock Batch Edit | Edit expired date, batch info |
| 8 Jan 2026 | Excel Import | Import produk dari Excel multi-sheet |
| 5 Jan 2026 | Export Features | Export sales, purchases, stocks |

---

## 🐛 Troubleshooting

### **Problem: "git command not found"**
**Solusi:** Install Git atau gunakan cara Manual (Download ZIP)

### **Problem: "composer not found"**
**Solusi:** 
```bash
# Install via Chocolatey
choco install composer

# Atau download: https://getcomposer.org/download/
```

### **Problem: Aplikasi error setelah update**
**Solusi:**
```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Restart server
# Tekan Ctrl+C di terminal yang running php artisan serve
# Jalankan lagi: php artisan serve
```

### **Problem: Database error**
**Solusi:**
```bash
# Jika ada perubahan struktur database
php artisan migrate

# HATI-HATI! Ini akan reset semua data:
# php artisan migrate:fresh --seed
```

---

## 📞 Kontak Support

**Jika ada masalah saat update:**
- Hubungi developer
- Kirim screenshot error
- Lampirkan file: `storage/logs/laravel.log`

---

## 📝 Catatan Penting

1. ✅ **Selalu backup database** sebelum update besar
2. ✅ **Test di jam non-operasional** (malam/pagi)
3. ✅ **Jangan update saat kasir sedang transaksi**
4. ✅ **Catat nomor transaksi terakhir** sebelum update
5. ✅ **Test login semua user** setelah update

---

## 🎯 Checklist Update

```
[ ] Backup database
[ ] Backup file .env
[ ] Tutup aplikasi (kasir tidak transaksi)
[ ] Pull/Download update
[ ] Clear cache
[ ] Test login Owner
[ ] Test login Kasir
[ ] Test login Admin Gudang
[ ] Test fitur baru (Edit Expired Date)
[ ] Test transaksi kasir
[ ] Dokumentasikan update
```

---

**Terakhir diupdate:** 11 Januari 2026  
**Developer:** Jonas Banurea  
**Aplikasi:** Toko Obat Ro Tua - Sistem Apotek
