# FAQ - Backup Database Error

## ❓ "MySQL sudah running kok backup tetap error?"

### Jawaban:
Ada **2 kemungkinan** masalah:

#### 1. Timing Issue (MySQL belum fully ready)
```
[0 detik]  Klik Start di XAMPP/Laragon
           ↓
[5 detik]  Status berubah jadi "Running" (hijau)
           ↓ ⚠️ TAPI MYSQL BELUM SIAP!
           ↓
[10-30 detik]  MySQL masih initialize internal processes
           ↓
[30+ detik]  MySQL FULLY READY untuk backup
```

**Solusi:** Tunggu 30-60 detik setelah start, coba lagi.

#### 2. MySQL Tidak Listen di TCP/IP Port (UMUM DI CLIENT!)
Beberapa instalasi MySQL (terutama XAMPP) **tidak enable TCP/IP**, hanya pakai **named pipe**.
Aplikasi PHP bisa konek via named pipe, tapi **mysqldump gagal**.

**Test:** Buka phpMyAdmin
- ✅ Jika phpMyAdmin **BISA DIBUKA** tapi backup gagal = **INI MASALAHNYA**
- ❌ Jika phpMyAdmin **TIDAK bisa** = MySQL belum ready (masalah #1)

**Solusi TERBAIK:** 
**GUNAKAN BACKUP MANUAL VIA phpMyAdmin** - ini lebih reliable!

```
1. Buka http://localhost/phpmyadmin
2. Klik database apotek_rotua (sidebar kiri)
3. Tab "Export" (atas)
4. Pilih: Quick + SQL
5. Klik "Go"
6. File .sql otomatis download
7. Simpan di 3 tempat (lokal, USB, cloud)
```

**Kelebihan backup manual:**
- ✅ Selalu work (tidak ada masalah TCP/IP)
- ✅ User-friendly (GUI, visual)
- ✅ Bisa preview data sebelum backup
- ✅ Tidak tergantung mysqldump binary

**Kekurangan:**
- ❌ Tidak backup file asset (gambar/upload)
- ❌ Manual process (tidak otomatis)

**Untuk backup file asset:**
Copy manual folder `storage/app/public` dan `public` ke tempat backup.

---

## ❓ "Bagaimana tahu MySQL sudah fully ready?"

### Test 1: Buka phpMyAdmin
```
1. Buka browser
2. Ketik: http://localhost/phpmyadmin
3. Jika muncul halaman phpMyAdmin = ✅ Ready
4. Jika error "Cannot connect" = ❌ Belum ready, tunggu lagi
```

### Test 2: Coba buka halaman Produk
```
1. Login ke aplikasi
2. Buka menu "Data Obat" atau halaman lain
3. Jika data muncul = ✅ Ready
4. Jika error koneksi = ❌ Belum ready
```

### Test 3: Pakai Script Checker
```
1. Double-click: cek_mysql_status.bat
2. Lihat hasil check
3. Jika semua ✅ = Ready untuk backup
```

---

## ❓ "Kenapa tidak auto-wait di aplikasi?"

### Jawaban:
Karena **tidak ada cara reliable** untuk tahu kapan MySQL benar-benar ready.

#### Yang sudah dicoba:
1. ❌ Cek port 3306 terbuka → **tidak reliable**
   - Port bisa terbuka tapi MySQL belum ready
   
2. ❌ Cek PDO connection → **false positive/negative**
   - Laravel connection pool bisa cache koneksi lama
   - Atau timeout terlalu cepat
   
3. ❌ Ping MySQL server → **inconsistent**
   - Bisa respond tapi belum ready process request

#### Solusi terbaik:
**User menunggu manual** 15-30 detik + test dengan phpMyAdmin

---

## ❓ "Error lain yang mungkin terjadi?"

### Error: "Access denied for user 'root'@'localhost'"

**Penyebab:** Password MySQL salah

**Solusi:**
```bash
1. Cek file .env → DB_PASSWORD
2. Sesuaikan dengan password MySQL Anda
3. Jalankan: php artisan config:clear
4. Coba backup lagi
```

### Error: "Unknown database 'toko_obat_rotua'"

**Penyebab:** Database tidak ada

**Solusi:**
```bash
1. Buka phpMyAdmin
2. Cek apakah database ada
3. Jika tidak ada:
   - Buat database baru: toko_obat_rotua
   - Atau sesuaikan DB_DATABASE di .env
4. Jalankan: php artisan config:clear
```

### Error: "mysqldump: command not found"

**Penyebab:** Path mysqldump tidak ditemukan

**Solusi:**
```bash
1. Edit file .env
2. Tambahkan:
   # Untuk XAMPP
   MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe
   
   # Untuk Laragon (sesuaikan versi)
   MYSQLDUMP_PATH=C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqldump.exe
   
3. Save file
4. Jalankan: php artisan config:clear
5. Coba backup lagi
```

---

## ❓ "Best practice untuk backup?"

### Workflow Recommended:

#### Morning Routine (Buka aplikasi):
```
1. Start XAMPP/Laragon
2. TUNGGU 30 detik
3. Test buka phpMyAdmin
4. Baru buka aplikasi
5. Sekarang aman untuk backup kapan saja
```

#### Backup Routine:
```
1. Pilih waktu tenang (sebelum/sesudah jam operasional)
2. Pastikan tidak ada transaksi sedang berjalan
3. Klik Backup di aplikasi
4. Tunggu proses selesai (1-5 menit)
5. Verifikasi file ZIP berhasil dibuat
6. Copy ke 3 lokasi:
   - Lokal (Documents)
   - USB/Hardisk
   - Cloud storage
```

#### Sebelum Update Aplikasi:
```
1. BACKUP dulu!
2. Test restore di komputer lain (optional)
3. Baru jalankan update
```

---

## ❓ "Berapa sering harus backup?"

### Rekomendasi:

#### Minimal:
- **1x seminggu** (setiap Jumat sore)

#### Recommended:
- **Setiap hari** (sebelum tutup toko)
- Atau **setiap selesai transaksi besar**

#### Critical:
- **Sebelum update aplikasi**
- **Sebelum import data besar**
- **Setelah input data penting**

### Retention Policy:
```
Simpan backup:
- Harian: 7 hari terakhir
- Mingguan: 4 minggu terakhir
- Bulanan: 12 bulan terakhir
- Tahunan: permanent
```

---

## ❓ "Backup gagal terus, apa alternatifnya?"

### Alternatif 1: Manual via phpMyAdmin (RECOMMENDED)
```
1. Buka: http://localhost/phpmyadmin
2. Pilih database: apotek_rotua
3. Tab Export → Quick → SQL → Go
4. Save file .sql yang didownload
5. Simpan di 3 tempat berbeda
```

**Kelebihan:**
- ✅ Paling mudah
- ✅ Selalu work
- ✅ User-friendly
- ✅ Visual

**Kekurangan:**
- ❌ Tidak backup file asset (gambar/upload)
- ❌ Manual process

### Alternatif 2: Command Line
```powershell
# Untuk XAMPP
C:\xampp\mysql\bin\mysqldump.exe -u root -p apotek_rotua > backup.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqldump.exe -u root -p apotek_rotua > backup.sql
```

**Kelebihan:**
- ✅ Cepat
- ✅ Bisa di-automate via script

**Kekurangan:**
- ❌ Perlu command line skill
- ❌ Tidak backup file asset

### Alternatif 3: Software Third-Party
- HeidiSQL (export database)
- MySQL Workbench
- Navicat
- DBeaver

---

## ❓ "File backup terlalu besar?"

### Ukuran Normal:
```
Database saja (SQL): 1-50 MB
Dengan asset: 50-500 MB
Dengan banyak foto produk: 500 MB - 2 GB
```

### Jika terlalu besar:
1. **Backup database saja** (via phpMyAdmin)
2. **Compress dengan 7-Zip** (bisa reduce 50-80%)
3. **Cleanup data lama:**
   - Hapus audit log > 6 bulan
   - Hapus data test/sampel
4. **Backup terpisah:**
   - Database: setiap hari
   - Asset: setiap minggu

---

## ❓ "Cara restore backup?"

### Via phpMyAdmin (Termudah):
```
1. Buka: http://localhost/phpmyadmin
2. Pilih database: apotek_rotua
3. Tab "Import"
4. Choose File → pilih backup.sql
5. Klik "Go"
6. Tunggu sampai selesai
7. Refresh aplikasi
```

### Via Command Line:
```powershell
# Untuk XAMPP
C:\xampp\mysql\bin\mysql.exe -u root -p apotek_rotua < backup.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.0.30\bin\mysql.exe -u root -p apotek_rotua < backup.sql
```

---

## ❓ "Backup corrupt, tidak bisa restore?"

### Prevention:
1. ✅ **Test backup setelah dibuat:**
   - Extract ZIP
   - Cek ukuran file SQL (harus > 1 KB)
   - Buka dengan text editor, lihat isi

2. ✅ **Simpan multiple backup:**
   - Jangan overwrite backup lama
   - Simpan minimal 3-7 backup terakhir

3. ✅ **Test restore sekali-kali:**
   - Setup test environment
   - Restore backup ke sana
   - Pastikan data lengkap

### Recovery:
Jika backup corrupt:
1. Coba backup yang lebih lama
2. Hubungi IT support (mungkin ada auto-backup)
3. Restore dari cloud backup (jika ada)
4. Last resort: manual input ulang data hilang

---

## 📞 Kontak Support

**Jika masih ada masalah:**
- Baca panduan lengkap: TROUBLESHOOTING_BACKUP.md
- Email: [email support]
- Phone: [nomor support]
- Remote assistance available

---

*Update: 8 Januari 2026*  
*Versi: 1.1*
