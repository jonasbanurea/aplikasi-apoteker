# Masalah TCP/IP Socket - Backup Database

## 🔴 Error yang Muncul

```
Backup gagal: Can't create TCP/IP socket (10106)
```

**DAN** phpMyAdmin bisa dibuka normal!

---

## 🔍 Root Cause

### Kenapa Aplikasi Bisa Konek tapi Mysqldump Tidak?

#### PHP Application (Laravel):
```
PHP → PDO → MySQL Client Library → Named Pipe/Socket → MySQL Server
      ✅ Bisa pakai Named Pipe (Windows)
      ✅ Bisa pakai TCP/IP
      ✅ Otomatis pilih yang available
```

#### Mysqldump Command:
```
mysqldump → MySQL Client → TCP/IP ONLY (by default) → MySQL Server
            ❌ Gagal jika MySQL tidak listen di TCP/IP
```

### Konfigurasi MySQL yang Menyebabkan Masalah:

**File:** `C:\xampp\mysql\bin\my.ini` (XAMPP) atau `C:\laragon\data\mysql\my.ini` (Laragon)

```ini
# Jika ada baris ini:
skip-networking

# Atau:
bind-address = localhost
# Tapi tidak ada:
port = 3306
```

**Efek:**
- MySQL **HANYA** listen di named pipe
- Tidak listen di TCP/IP port 3306
- PHP/phpMyAdmin bisa konek (pakai named pipe)
- Mysqldump gagal (default pakai TCP/IP)

---

## ✅ Solusi

### Opsi 1: Backup Manual via phpMyAdmin (RECOMMENDED ⭐)

**Kelebihan:**
- ✅ Selalu work di semua environment
- ✅ User-friendly (GUI)
- ✅ Tidak perlu konfigurasi tambahan
- ✅ Bisa preview dan filter data

**Cara:**
```
1. Buka: http://localhost/phpmyadmin
2. Sidebar kiri: Klik database "apotek_rotua"
3. Tab atas: Klik "Export"
4. Export method: Quick
5. Format: SQL
6. Klik tombol "Go"
7. File .sql akan otomatis terdownload
8. Simpan di:
   - Documents/Backup-Database/
   - USB/Hardisk eksternal
   - Cloud storage (Google Drive, OneDrive)
```

**Untuk backup file asset:**
Copy manual folder:
- `d:\PROJECT\APOTEKER\Aplikasi\storage\app\public`
- `d:\PROJECT\APOTEKER\Aplikasi\public` (jika ada upload file)

---

### Opsi 2: Enable TCP/IP di MySQL

**⚠️ PERLU AKSES ADMIN & RESTART MySQL**

#### Langkah:

**1. Edit Konfigurasi MySQL**

XAMPP:
```
File: C:\xampp\mysql\bin\my.ini
```

Laragon:
```
File: C:\laragon\bin\mysql\mysql-8.x\my.ini
```

**2. Tambahkan/Pastikan Ada:**
```ini
[mysqld]
port = 3306
bind-address = 127.0.0.1

# Pastikan TIDAK ada:
# skip-networking
```

**3. Restart MySQL**
- Stop MySQL di XAMPP/Laragon
- Tunggu 5 detik
- Start MySQL
- Tunggu 30 detik (fully ready)

**4. Test:**
```powershell
netstat -ano | findstr :3306
```

Harus ada output (port 3306 listening)

**5. Test Backup:**
Coba backup di aplikasi - seharusnya berhasil.

---

### Opsi 3: Force Mysqldump Pakai Named Pipe

**⚠️ Sudah diimplementasi di kode, retry otomatis**

BackupController sekarang sudah retry dengan 3 cara:
1. Try dengan `--protocol=TCP`
2. Jika gagal, retry tanpa protocol (default)
3. Jika masih gagal, retry tanpa host/port (pakai socket default)

Jika semua gagal → kasih error yang jelas → arahkan ke backup manual.

---

### Opsi 4: Set MYSQLDUMP_PATH dengan Socket

**Edit `.env`:**
```env
MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe
MYSQL_SOCKET=/tmp/mysql.sock
```

(Jarang berhasil di Windows)

---

## 🎯 Rekomendasi untuk Production

### Setup Ideal:

**1. Enable TCP/IP di semua client**
- Edit `my.ini` seperti Opsi 2
- Standardisasi konfigurasi

**2. Dokumentasikan untuk client:**
```
"Jika backup gagal tapi phpMyAdmin bisa dibuka:
 → Gunakan backup manual via phpMyAdmin
 → Ini normal dan AMAN"
```

**3. Training:**
- Ajarkan client cara backup manual
- Lebih reliable dan user-friendly
- Simpan di 3 tempat

**4. Schedule:**
- Backup manual: setiap hari (5 menit)
- Backup otomatis: jika berhasil, bagus (bonus)

---

## 📊 Perbandingan Metode

| Metode | Pros | Cons | Reliability |
|--------|------|------|-------------|
| **Backup Manual (phpMyAdmin)** | ✅ Selalu work<br>✅ User-friendly<br>✅ Visual | ❌ Manual<br>❌ Tidak backup asset | ⭐⭐⭐⭐⭐ |
| **Backup Otomatis (App)** | ✅ Otomatis<br>✅ Backup asset juga<br>✅ ZIP lengkap | ❌ TCP/IP issue<br>❌ Setup dependent | ⭐⭐⭐ |
| **Command Line** | ✅ Cepat<br>✅ Bisa script | ❌ Perlu skill<br>❌ TCP/IP issue juga | ⭐⭐⭐ |

---

## 🔧 Troubleshooting Lanjutan

### Test: Apakah MySQL Listen di TCP/IP?

**PowerShell:**
```powershell
netstat -ano | findstr :3306
```

**Expected output jika OK:**
```
TCP    0.0.0.0:3306           0.0.0.0:0              LISTENING       1234
TCP    127.0.0.1:3306         0.0.0.0:0              LISTENING       1234
```

**Jika TIDAK ada output:**
MySQL tidak listen di port 3306 → perlu enable TCP/IP (Opsi 2)

### Test: Coba Mysqldump Manual

**PowerShell:**
```powershell
# Untuk XAMPP
C:\xampp\mysql\bin\mysqldump.exe -u root apotek_rotua > test_backup.sql

# Untuk Laragon
C:\laragon\bin\mysql\mysql-8.x\bin\mysqldump.exe -u root apotek_rotua > test_backup.sql
```

**Jika berhasil:**
- File `test_backup.sql` terbuat
- Ukuran > 1 KB
→ Masalah ada di aplikasi (cek password di .env)

**Jika error "Can't create TCP/IP socket":**
→ Konfirmasi masalah TCP/IP → pakai backup manual

---

## 📞 Support Script

**Untuk IT Support:**

Buat script checker:

```powershell
# check_mysql_tcp.ps1

Write-Host "Checking MySQL TCP/IP status..." -ForegroundColor Yellow
Write-Host ""

$port3306 = netstat -ano | findstr ":3306"
if ($port3306) {
    Write-Host "✅ MySQL listening on port 3306" -ForegroundColor Green
    Write-Host $port3306
} else {
    Write-Host "❌ MySQL NOT listening on port 3306" -ForegroundColor Red
    Write-Host "   → Backup otomatis akan gagal" -ForegroundColor Yellow
    Write-Host "   → Gunakan backup manual via phpMyAdmin" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Testing phpMyAdmin connection..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost/phpmyadmin" -TimeoutSec 3 -ErrorAction Stop
    Write-Host "✅ phpMyAdmin accessible (Status: $($response.StatusCode))" -ForegroundColor Green
    Write-Host "   → Backup manual AVAILABLE" -ForegroundColor Green
} catch {
    Write-Host "❌ phpMyAdmin not accessible" -ForegroundColor Red
    Write-Host "   → MySQL mungkin tidak running" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "RECOMMENDATION:" -ForegroundColor Cyan
if (!$port3306) {
    Write-Host "Use manual backup via phpMyAdmin for this client" -ForegroundColor Yellow
} else {
    Write-Host "Automatic backup should work" -ForegroundColor Green
}

pause
```

---

## 📚 Referensi

- MySQL Documentation: Named Pipes vs TCP/IP
- phpMyAdmin Export Documentation
- Windows Socket Programming (WSA Error 10106)

---

**Update:** 8 Januari 2026  
**Status:** Documented & Workaround Available  
**Action:** Train users on manual backup method
