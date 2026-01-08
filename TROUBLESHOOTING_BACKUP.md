# Troubleshooting Backup - Toko Obat Ro Tua

## Error: "Can't create TCP/IP socket (10106)"

### 🔴 Penyebab Utama
MySQL service **TIDAK BERJALAN** atau **BARU SAJA START** (belum fully ready)

### ⚠️ PENTING: MySQL Butuh Waktu Ready!
Setelah klik Start di XAMPP/Laragon, MySQL **TIDAK langsung siap**.
- Proses start memakan waktu **10-30 detik**
- Jika langsung backup setelah start = **AKAN ERROR**
- **Solusi**: Tunggu 15-30 detik setelah MySQL start, baru backup

### ✅ Solusi Lengkap

#### Langkah 1: Buka Control Panel XAMPP/Laragon

**Untuk XAMPP:**
1. Cari icon XAMPP di taskbar (pojok kanan bawah) atau desktop
2. Double-click untuk membuka XAMPP Control Panel
3. Lihat baris **MySQL** atau **MariaDB**

**Untuk Laragon:**
1. Cari icon Laragon di taskbar atau desktop
2. Klik kanan → "Show"
3. Jendela Laragon akan terbuka

#### Langkah 2: Start MySQL Service

**Di XAMPP Control Panel:**
```
┌─────────────────────────────────────┐
│ Module    │ PID(s) │ Port(s) │ Actions │
├─────────────────────────────────────┤
│ Apache    │ 1234   │ 80, 443 │ [Stop]  │
│ MySQL     │ ─────  │ ───     │ [Start] │ ← KLIK INI!
└─────────────────────────────────────┘
```

1. Jika kolom PID(s) MySQL **KOSONG** = MySQL tidak berjalan
2. Klik tombol **[Start]** di baris MySQL
3. Tunggu sampai muncul angka di kolom PID(s)
4. Status bar di bawah akan menampilkan "MySQL started"
5. **⚠️ PENTING: TUNGGU 15-30 DETIK sebelum backup!**
6. Test buka http://localhost/phpmyadmin untuk memastikan MySQL ready

**Di Laragon:**
```
┌─────────────────────────────────────┐
│  MySQL: ⚪ Stopped                   │ ← Jika merah/kosong
│  [Start All]                        │ ← KLIK INI!
└─────────────────────────────────────┘
```

1. Jika MySQL status **Stopped** atau **⚪** = t
4. **⚠️ PENTING: TUNGGU 15-30 DETIK sebelum backup!**
5. Test buka http://localhost/phpmyadmin untuk memastikan MySQL readyidak berjalan
2. Klik tombol **[Start All]** atau **[Start]** untuk MySQL
3. Tunggu sampai berubah menjadi **🟢 Running**

#### Langkah 3: Verifikasi MySQL Berjalan

**Test 1: Cek di Control Panel**
- XAMPP: MySQL harus ada angka di kolom PID(s) dan tombol berubah jadi [Stop]
- Laragon: MySQL harus status **🟢 Running**

**Test 2: Buka phpMyAdmin**
1. Buka browser
2. Ketik: `http://localhost/phpmyadmin`
3. Jika muncul halaman login/dashboard phpMyAdmin = **MySQL OK** ✅
4. Jika error "Cannot connect" = MySQL masih belum jalan ❌

**Test 3: Test di Aplikasi**
1. Buka aplikasi Toko Obat Ro Tua
2. Login
3. Coba buka halaman **Data Obat** atau halaman lain
4. Jika data muncul = **Koneksi database OK** ✅
5. Jika error = ada masalah koneksi ❌

#### Langkah 4: Coba Backup Lagi

1. Di aplikasi, klik menu **Owner → Backup Database**
2. Klik tombol **Backup Sekarang**
3. Tunggu proses selesai
4. Jika berhasil, akan muncul pesan sukses dengan lokasi file backup

---

## Error Lainnya

### Error: "mysqldump.exe tidak ditemukan"

**Penyebab**: Path mysqldump tidak terdeteksi otomatis

**Solusi 1: Set Path Manual di .env**

1. Buka file `.env` di root project
2. Tambahkan baris:

```env
# Untuk XAMPP
MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe

# Untuk Laragon (sesuaikan versi MySQL)
MYSQLDUMP_PATH=C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqldump.exe
```

3. Save file
4. Restart aplikasi (tutup browser, buka lagi)
5. Coba backup lagi

**Solusi 2: Cari Path mysqldump**

1. Buka File Explorer
2. Ketik di address bar:
   - XAMPP: `C:\xampp\mysql\bin`
   - Laragon: `C:\laragon\bin\mysql`
3. Cari folder versi MySQL (misal: mysql-8.0.30)
4. Masuk ke folder `bin`
5. Cari file `mysqldump.exe`
6. Copy full path-nya (Shift + klik kanan → Copy as path)
7. Paste ke file `.env` seperti di atas

### Error: "Port 3306 sudah digunakan"

**Penyebab**: Ada aplikasi lain yang pakai port 3306

**Solusi:**

1. **Cek aplikasi yang pakai port 3306:**
   ```powershell
   netstat -ano | findstr :3306
   ```

2. **Stop aplikasi MySQL lain** (jika ada):
   - MySQL Workbench
   - WAMP
   - MAMP
   - HeidiSQL
   - DBeaver

3. **Atau ubah port MySQL di .env:**
   ```env
   DB_PORT=3307
   ```
   (Tapi ini butuh konfigurasi ulang MySQL)

### MySQL Start Terus Gagal

**Gejala**: Klik Start di XAMPP/Laragon, tapi langsung berhenti lagi

**Solusi:**

**1. Cek Port Conflict**
```powershell
# Cek port 3306
netstat -ano | findstr :3306
```

**2. Cek Log Error MySQL**

XAMPP:
- Buka: `C:\xampp\mysql\data\`
- Cari file `*.err` atau `mysql_error.log`
- Buka dengan Notepad
- Lihat error di baris paling bawah

Laragon:
- Buka: `C:\laragon\data\mysql\`
- Cari file error log
- Baca error terakhir

**3. Perbaiki Corrupt MySQL**

Jika log error menyebut **"corrupt"** atau **"crashed"**:

```powershell
# Stop MySQL dulu
# Lalu jalankan repair (di folder mysql\bin):

cd C:\xampp\mysql\bin
# atau
cd C:\laragon\bin\mysql\mysql-8.0.30\bin

# Repair database
.\myisamchk.exe -r ..\..\data\*.MYI
```

**4. Reinstall MySQL (Last Resort)**

Jika semua gagal:
1. Backup dulu file database di `C:\xampp\mysql\data\` atau `C:\laragon\data\mysql\`
2. Uninstall XAMPP/Laragon
3. Install ulang
4. Restore database dari backup

---

## Backup Manual (Alternatif)

Jika fitur backup di aplikasi tetap tidak bisa, gunakan cara manual:

### Via phpMyAdmin (PALING MUDAH) ✅

1. Buka: `http://localhost/phpmyadmin`
2. Klik database `apotek_rotua` di sidebar kiri
3. Klik tab **"Export"** di atas
4. Pilih:
   - Export method: **Quick**
   - Format: **SQL**
5. Klik tombol **"Go"**
6. File `.sql` akan otomatis terdownload
7. Simpan di tempat aman (Documents, USB, cloud, dll)

### Via Command Line

1. Buka PowerShell atau Command Prompt
2. Jalankan:

```powershell
# Untuk XAMPP
C:\xampp\mysql\bin\mysqldump.exe -u root apotek_rotua > backup_manual.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.0.30\bin\mysqldump.exe -u root apotek_rotua > backup_manual.sql
```

3. File `backup_manual.sql` akan tersimpan di folder tempat Anda menjalankan command

---

## Restore Database dari Backup

### Via phpMyAdmin

1. Buka: `http://localhost/phpmyadmin`
2. Klik database `apotek_rotua`
3. Klik tab **"Import"**
4. Klik **"Choose File"**
5. Pilih file backup `.sql` Anda
6. Klik **"Go"**
7. Tunggu sampai selesai

### Via Command Line

```powershell
# Untuk XAMPP
C:\xampp\mysql\bin\mysql.exe -u root apotek_rotua < backup_manual.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.0.30\bin\mysql.exe -u root apotek_rotua < backup_manual.sql
```

---

## Checklist Sebelum Hubungi Support

Sebelum menghubungi IT support, coba checklist ini dulu:

- [ ] MySQL service sudah **Running** di XAMPP/Laragon?
- [ ] phpMyAdmin bisa dibuka (`http://localhost/phpmyadmin`)?
- [ ] Aplikasi bisa login dan buka halaman Data Obat?
- [ ] Sudah coba restart XAMPP/Laragon?
- [ ] Sudah coba restart laptop?
- [ ] File `.env` sudah diset `MYSQLDUMP_PATH` (jika perlu)?
- [ ] Port 3306 tidak dipakai aplikasi lain?
- [ ] Antivirus/Firewall tidak memblokir MySQL?

---

## Kontak Support

Jika sudah coba semua cara di atas tapi masih error:

**Siapkan informasi ini:**
1. Screenshot error lengkap dari aplikasi
2. Screenshot XAMPP/Laragon Control Panel (tunjukkan status MySQL)
3. Screenshot phpMyAdmin (bisa dibuka atau tidak?)
4. Copy-paste isi file log error MySQL (jika ada)
5. Versi XAMPP/Laragon yang digunakan
6. Versi Windows (Win 10/11)

**Lalu kirim ke:**
- Email: [email support Anda]
- WhatsApp: [nomor support]
- Ticket system: [link]

---

## Tips Pencegahan

Agar backup selalu lancar:

1. ✅ **Selalu start XAMPP/Laragon saat membuka aplikasi**
   - Set XAMPP/Laragon auto-start saat Windows boot
   - Atau biasakan start manual sebelum buka aplikasi

2. ✅ **Jadwalkan backup rutin**
   - Minimal 1x seminggu
   - Sebelum update aplikasi
   - Setelah input data penting

3. ✅ **Simpan backup di 3 tempat:**
   - Laptop lokal (Documents)
   - USB/Hardisk eksternal
   - Cloud storage (Google Drive, OneDrive, dll)

4. ✅ **Test restore backup sekali-kali**
   - Pastikan backup bisa di-restore
   - Jangan sampai pas butuh baru ketahuan backup corrupt

5. ✅ **Monitor kesehatan MySQL**
   - Jika sering crash, segera hubungi IT
   - Cek space hardisk (MySQL butuh space cukup)
   - Restart laptop minimal 1x seminggu

---

**Terakhir diupdate**: 8 Januari 2026
**Versi**: 1.0
