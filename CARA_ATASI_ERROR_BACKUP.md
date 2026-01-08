# CARA MENGATASI ERROR BACKUP - PANDUAN SINGKAT

## ❌ Error yang Muncul:
```
Backup gagal: ... Can't create TCP/IP socket (10106) ...
```

---

## ✅ SOLUSI CEPAT (3 LANGKAH):

### 1️⃣ Buka XAMPP Control Panel
   - Cari icon XAMPP di pojok kanan bawah taskbar
   - Atau di desktop
   - Double-click untuk membuka

### 2️⃣ Cek Baris MySQL
   ```
   ┌──────────────────────────────────┐
   │ Module │ Status │ Action         │
   ├──────────────────────────────────┤
   │ MySQL  │ ⚫     │ [Start] ← INI! │
   └──────────────────────────────────┘
   ```
   
   **Jika ada tombol [Start]** = MySQL belum jalan
   **Jika ada tombol [Stop]** = MySQL sudah jalan

### 3️⃣ Klik Tombol Start
   - Klik tombol **[Start]** di baris MySQL
   - Tunggu 5-10 detik
   - Status akan berubah jadi **🟢 hijau**
   - **⚠️ PENTING: TUNGGU 15-30 DETIK lagi!**
   - MySQL butuh waktu untuk fully ready
   - Coba backup lagi di aplikasi

---

## 🔍 TEST: Apakah MySQL Sudah Jalan?

### Cara 1: Buka phpMyAdmin
1. Buka browser
2. Ketik di address bar: `http://localhost/phpmyadmin`
3. **Jika muncul halaman phpMyAdmin** ✅ = MySQL OK
4. **Jika error "Cannot connect"** ❌ = MySQL masih mati

### Cara 2: Pakai Script Checker
1. Di folder aplikasi, cari file: `cek_mysql_status.bat`
2. Double-click file tersebut
3. Akan muncul jendela hitam dengan hasil check
4. Ikuti instruksi yang muncul

---

## 💾 BACKUP ALTERNATIF (Jika Tetap Gagal)

### Via phpMyAdmin (Cara Manual):
1. Buka browser → `http://localhost/phpmyadmin`
2. Klik database **"apotek_rotua"** (di sidebar kiri)
3. Klik tab **"Export"** (di atas)
4. Pilih:
   - ☑️ Quick
   - ☑️ SQL
5. Klik tombol **"Go"**
6. File `.sql` akan otomatis terdownload
7. Simpan file ini di folder aman:
   - Documents
   - USB/Hardisk
   - Google Drive

---

## 📞 MASIH BERMASALAH?

### "MySQL sudah running tapi tetap error!"

**Kemungkinan penyebab:**
1. **MySQL baru saja start, belum fully ready**
   - Solusi: Tunggu 30 detik, coba lagi
   - Test buka phpMyAdmin dulu untuk memastikan ready

2. **Password MySQL salah di .env**
   - Cek file `.env` → DB_PASSWORD
   - Sesuaikan dengan password MySQL Anda
   - Jalankan: `php artisan config:clear`
   - Coba backup lagi

3. **Database tidak ada**
   - Cek di phpMyAdmin apakah database ada
   - Sesuaikan DB_DATABASE di .env

### Coba Ini Dulu:
- [ ] Restart XAMPP (Stop All → Start All)
- [ ] Restart laptop
- [ ] Cek antivirus tidak block MySQL
- [ ] Cek space hardisk masih cukup (> 500 MB)

### Hubungi IT Support:
**Siapkan info ini:**
1. Screenshot error dari aplikasi
2. Screenshot XAMPP Control Panel
3. Apakah phpMyAdmin bisa dibuka?

---

## 🎯 TIPS AGAR TIDAK ERROR LAGI

✅ **Selalu start XAMPP sebelum buka aplikasi**
   - Biasakan cek MySQL sudah hijau dulu
   
✅ **Backup rutin 1x seminggu**
   - Pilih hari tertentu (misal: setiap Jumat)
   
✅ **Simpan backup di 3 tempat**
   - Laptop lokal
   - USB/Hardisk eksternal  
   - Cloud (Google Drive/OneDrive)

---

## 📄 PANDUAN LENGKAP

Untuk troubleshooting detail, baca file:
- **TROUBLESHOOTING_BACKUP.md** (panduan lengkap)
- **KARTU_BANTUAN_BACKUP.md** (kartu bantuan bisa dicetak)

---

**INGAT:** Error ini 99% karena MySQL tidak berjalan.  
**Solusi:** Start MySQL di XAMPP Control Panel!

---

🔗 **Shortcut Penting:**
- XAMPP: Start → All Programs → XAMPP → XAMPP Control Panel
- phpMyAdmin: http://localhost/phpmyadmin
- Aplikasi: http://localhost:8000

---

*Versi: 1.0 | Update: 8 Januari 2026*
