# Panduan Akses Aplikasi dari Device Lain (Jaringan Lokal)

## Overview
Panduan ini menjelaskan cara mengkonfigurasi laptop Windows 11 agar aplikasi dapat diakses dari device lain (HP, tablet, laptop lain) dalam jaringan yang sama.

---

## 🔧 Langkah 1: Set IP Static di Windows 11

### A. Cek IP Address Saat Ini
1. Buka **Command Prompt** atau **PowerShell**
2. Ketik: `ipconfig`
3. Catat informasi berikut:
   - **IPv4 Address**: Contoh `192.168.1.100`
   - **Subnet Mask**: Contoh `255.255.255.0`
   - **Default Gateway**: Contoh `192.168.1.1`
   - **DNS Server**: Biasanya sama dengan Gateway

### B. Setting IP Static
1. Tekan `Windows + I` untuk membuka **Settings**
2. Pilih **Network & Internet**
3. Klik koneksi aktif Anda (Wi-Fi atau Ethernet)
4. Scroll ke bawah, klik **Properties**
5. Di bagian **IP assignment**, klik **Edit**
6. Pilih **Manual**
7. Aktifkan toggle **IPv4**
8. Isi dengan data yang sudah dicatat:
   ```
   IP address: 192.168.1.100 (atau sesuai kebutuhan)
   Subnet prefix length: 24 (untuk 255.255.255.0)
   Gateway: 192.168.1.1
   Preferred DNS: 192.168.1.1 (atau 8.8.8.8)
   Alternate DNS: 8.8.8.8 (Google DNS)
   ```
9. Klik **Save**

### C. Verifikasi
```bash
ipconfig
```
Pastikan IP Address sudah sesuai dengan yang Anda set.

---

## 🚀 Langkah 2: Jalankan Laravel dengan Host Network

### A. Edit File `start_aplikasi.bat`
Ubah file `start_aplikasi.bat` untuk menggunakan IP yang dapat diakses dari jaringan:

```batch
@echo off
echo ========================================
echo  TOKO OBAT RO TUA - Starting Application
echo ========================================
echo.

REM Dapatkan IP Address otomatis
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set IP_ADDRESS=%%a
    goto :found
)
:found
set IP_ADDRESS=%IP_ADDRESS:~1%

echo Starting Laravel Server...
echo Access from this computer: http://localhost:8000
echo Access from other devices: http://%IP_ADDRESS%:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

REM Start Laravel server dengan host 0.0.0.0 agar bisa diakses dari jaringan
php artisan serve --host=0.0.0.0 --port=8000

pause
```

### B. Alternatif: Jalankan Manual
Jika ingin menjalankan manual, gunakan command:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Atau dengan IP spesifik:
```bash
php artisan serve --host=192.168.1.100 --port=8000
```

---

## 🔥 Langkah 3: Konfigurasi Firewall Windows

### A. Buka Port di Windows Firewall
1. Tekan `Windows + R`, ketik `wf.msc`, Enter
2. Klik **Inbound Rules** di panel kiri
3. Klik **New Rule** di panel kanan
4. Pilih **Port**, klik **Next**
5. Pilih **TCP**, isi **Specific local ports**: `8000`
6. Klik **Next**
7. Pilih **Allow the connection**, klik **Next**
8. Centang semua (Domain, Private, Public), klik **Next**
9. Beri nama: `Laravel App - Port 8000`
10. Klik **Finish**

### B. Atau Gunakan Command PowerShell (Run as Administrator)
```powershell
New-NetFirewallRule -DisplayName "Laravel App Port 8000" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
```

---

## 📱 Langkah 4: Akses dari Device Lain

### A. Dari HP/Tablet/Laptop Lain
1. Pastikan device terhubung ke **jaringan Wi-Fi yang sama**
2. Buka browser (Chrome, Safari, Firefox, dll)
3. Akses URL:
   ```
   http://192.168.1.100:8000
   ```
   *(Ganti dengan IP laptop Anda)*

### B. Login Credentials
Gunakan akun berikut untuk login:
- **Owner**: owner@rotua.test / password
- **Kasir**: kasir@rotua.test / password
- **Admin Gudang**: gudang@rotua.test / password

---

## 🔍 Troubleshooting

### Problem 1: "Tidak bisa akses dari device lain"
**Solusi:**
1. Pastikan firewall sudah dikonfigurasi (Langkah 3)
2. Cek apakah server Laravel berjalan dengan `--host=0.0.0.0`
3. Pastikan kedua device di jaringan yang sama
4. Coba ping dari device lain:
   ```bash
   ping 192.168.1.100
   ```

### Problem 2: "Connection Refused"
**Solusi:**
1. Pastikan Laravel server masih berjalan
2. Cek apakah port 8000 sudah dibuka di firewall
3. Coba restart aplikasi
4. Coba nonaktifkan antivirus sementara

### Problem 3: "IP Address berubah setelah restart"
**Solusi:**
- Pastikan Anda sudah set IP Static (Langkah 1)
- Periksa kembali setting di Network & Internet

### Problem 4: "Lambat saat diakses"
**Solusi:**
1. Pastikan koneksi Wi-Fi stabil
2. Gunakan router yang bagus dengan signal kuat
3. Batasi jumlah user yang akses bersamaan
4. Pertimbangkan upgrade RAM laptop jika banyak user

---

## 💡 Tips & Best Practices

### 1. **Gunakan IP Address yang Mudah Diingat**
- Pilih IP seperti `192.168.1.100` atau `192.168.1.200`
- Hindari IP yang sudah digunakan device lain

### 2. **Bookmark URL di Device Lain**
- Setelah berhasil akses, bookmark URL di browser HP/tablet
- Buat shortcut di home screen

### 3. **Create QR Code untuk URL** (Optional)
Buat QR Code untuk URL aplikasi agar mudah dibagikan:
- Gunakan website seperti qr-code-generator.com
- Input URL: `http://192.168.1.100:8000`
- Print dan tempel di kasir

### 4. **Jalankan Otomatis saat Startup** (Optional)
Agar aplikasi langsung jalan saat laptop hidup:
1. Tekan `Windows + R`, ketik `shell:startup`, Enter
2. Copy file `start_aplikasi.bat` ke folder yang terbuka
3. Aplikasi akan auto-start setiap kali laptop hidup

### 5. **Gunakan Laptop Dedicated**
Untuk performa optimal:
- Gunakan laptop khusus sebagai server
- Jangan tutup laptop (set ke "Do Nothing" saat lid closed)
- Setting Power Plan ke "High Performance"

---

## 📊 Setting Power Management (Agar Laptop Tidak Sleep)

### A. Power & Battery Settings
1. Tekan `Windows + I` → **System** → **Power**
2. Set **Screen and sleep**:
   - When plugged in, turn off screen after: **Never**
   - When plugged in, put device to sleep after: **Never**

### B. Lid Close Action
1. Search "Choose what closing the lid does"
2. Set **When I close the lid (plugged in)**: **Do nothing**

---

## 🌐 Akses dari Internet (Advanced)

Jika ingin diakses dari luar jaringan (internet):

### Option 1: Port Forwarding (Butuh akses router)
1. Login ke router (biasanya 192.168.1.1)
2. Cari menu **Port Forwarding** atau **Virtual Server**
3. Forward port eksternal 8000 ke IP laptop:8000
4. Akses menggunakan IP Public Anda

### Option 2: Ngrok (Temporary Solution)
```bash
# Install ngrok
# Download dari https://ngrok.com/download

# Jalankan ngrok
ngrok http 8000
```

### Option 3: Cloudflare Tunnel (Recommended untuk production)
Lebih aman dan reliable untuk akses internet.

---

## 📝 Checklist Setup

- [ ] IP Static sudah dikonfigurasi
- [ ] `start_aplikasi.bat` sudah diedit dengan `--host=0.0.0.0`
- [ ] Firewall Windows sudah dibuka untuk port 8000
- [ ] Sudah test akses dari HP/device lain
- [ ] URL sudah di-bookmark di device lain
- [ ] Power settings sudah dikonfigurasi (tidak sleep)
- [ ] Sudah test login dengan user kasir/admin

---

## 📞 Support

Jika ada masalah:
1. Cek file `TROUBLESHOOTING.md`
2. Cek log Laravel di `storage/logs/laravel.log`
3. Restart aplikasi dan router
4. Pastikan Windows Update tidak mengubah setting firewall

---

**Dibuat untuk Toko Obat Ro Tua**  
**Update terakhir: Januari 2026**
