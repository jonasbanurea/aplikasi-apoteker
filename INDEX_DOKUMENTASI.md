# 📚 INDEX - Dokumentasi Akses Aplikasi

## 🎯 Mulai dari Mana?

### 👤 Saya adalah...

#### 🧑‍💼 **Kasir / Admin Gudang** (Akses dari Toko)
- **Goal**: Akses aplikasi dari HP/tablet di toko
- **Metode**: Akses Lokal (WiFi)
- **Baca**: [PANDUAN_AKSES_JARINGAN.md](PANDUAN_AKSES_JARINGAN.md)
- **Quick Start**: 
  ```batch
  setup_network_full.bat  # Run as Admin
  start_aplikasi.bat
  ```

#### 👨‍💼 **Owner** (Monitoring dari Rumah)
- **Goal**: Akses aplikasi dari rumah/perjalanan
- **Metode**: VPN atau Cloudflare Tunnel
- **Baca**: [PANDUAN_AKSES_INTERNET.md](PANDUAN_AKSES_INTERNET.md) - Opsi 4 atau 3
- **Atau lihat**: [DECISION_TREE_AKSES.md](DECISION_TREE_AKSES.md)

#### 💻 **IT Admin / Developer**
- **Goal**: Setup & maintenance aplikasi
- **Metode**: Semua opsi tersedia
- **Baca**: Semua dokumentasi di bawah

#### 🎓 **Demo / Presentasi**
- **Goal**: Demo aplikasi ke investor/client
- **Metode**: Ngrok (termudah)
- **Quick Start**:
  ```batch
  download_ngrok.bat
  start_with_ngrok.bat
  ```

---

## 📖 Dokumentasi by Category

### 🚀 Getting Started (Wajib Baca)

1. **[README.md](README.md)**
   - Overview aplikasi
   - Fitur utama
   - Instalasi cepat
   - User default

2. **[QUICK_START.md](QUICK_START.md)**
   - Panduan instalasi step-by-step
   - Setup database
   - Jalankan aplikasi

### 📱 Akses Lokal (WiFi Same Network)

3. **[PANDUAN_AKSES_JARINGAN.md](PANDUAN_AKSES_JARINGAN.md)** ⭐ RECOMMENDED
   - Set IP static Windows 11
   - Konfigurasi firewall
   - Akses dari HP/tablet di toko
   - Troubleshooting lengkap
   - **Use Case**: Operasional harian di toko

4. **[QUICK_REF_AKSES_JARINGAN.md](QUICK_REF_AKSES_JARINGAN.md)**
   - Quick reference untuk kasir/staff
   - Credentials login
   - Troubleshooting cepat
   - **Cetak & tempel di kasir** 📄

5. **[DIAGRAM_AKSES_JARINGAN.md](DIAGRAM_AKSES_JARINGAN.md)**
   - Visual diagram arsitektur
   - Flow setup & akses
   - Troubleshooting flowchart
   - Skenario penggunaan

### 🌐 Akses Internet (Dari Mana Saja)

6. **[PANDUAN_AKSES_INTERNET.md](PANDUAN_AKSES_INTERNET.md)** ⭐ LENGKAP
   - Port Forwarding
   - Ngrok (termudah)
   - Cloudflare Tunnel (production)
   - VPN WireGuard (paling aman)
   - Cloud Hosting (VPS)
   - **Use Case**: Akses dari luar toko

7. **[PERBANDINGAN_AKSES.md](PERBANDINGAN_AKSES.md)**
   - Lokal vs Internet
   - Kelebihan & kekurangan
   - Biaya & keamanan
   - Quick start commands

8. **[DECISION_TREE_AKSES.md](DECISION_TREE_AKSES.md)** ⭐ PILIH METODE
   - Decision tree interaktif
   - Solusi berdasarkan use case
   - Comparison matrix
   - Real-world examples

9. **[FAQ_AKSES_INTERNET.md](FAQ_AKSES_INTERNET.md)**
   - 35+ pertanyaan umum
   - Troubleshooting
   - Tips & tricks
   - Resources & tools

### 🔒 Security (Jika Akses Internet)

10. **[SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md)** ⚠️ PENTING
    - Basic security (wajib)
    - Advanced security
    - VPS security
    - Monitoring & detection
    - Incident response plan

### 🛠️ Troubleshooting & Maintenance

11. **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)**
    - General troubleshooting
    - Error umum & solusi
    - Performance optimization

12. **[TROUBLESHOOTING_BACKUP.md](TROUBLESHOOTING_BACKUP.md)**
    - Backup issues
    - MySQL connection errors
    - mysqldump problems

### 📊 Data Management

13. **[import-export-products.md](docs/import-export-products.md)**
    - Import produk dari Excel
    - Export produk ke Excel
    - Format file

14. **[PANDUAN_SKU_PRODUK.md](docs/PANDUAN_SKU_PRODUK.md)**
    - Format SKU
    - Contoh penamaan
    - Best practices

### 👥 User Guides

15. **[user-guide.md](docs/user-guide.md)**
    - Panduan untuk kasir
    - Panduan untuk admin gudang
    - Panduan untuk owner

---

## 🔧 Scripts & Utilities

### Setup & Konfigurasi
```
setup_network_full.bat     - Setup lengkap (firewall + power) [RUN AS ADMIN]
setup_firewall.bat         - Setup firewall saja [RUN AS ADMIN]
setup_power.bat           - Setup power management [RUN AS ADMIN]
setup.bat                 - Setup awal aplikasi
```

### Jalankan Aplikasi
```
start_aplikasi.bat        - Start aplikasi (akses lokal)
start_mysql_and_app.bat   - Auto-start MySQL + aplikasi
start_with_ngrok.bat      - Start dengan Ngrok (akses internet)
```

### Testing & Diagnostic
```
test_network.bat          - Test koneksi & verifikasi setup
cek_mysql_status.bat      - Diagnosa MySQL & database
```

### Utilities
```
download_ngrok.bat        - Download Ngrok otomatis
generate_qr_code.bat      - Generate QR Code untuk akses mudah
update_produk.bat         - Update data produk dari Excel
```

---

## 📋 Quick Reference by Scenario

### Scenario 1: Setup Awal Toko
```
1. Baca: README.md
2. Setup: QUICK_START.md
3. Akses lokal: PANDUAN_AKSES_JARINGAN.md
4. Script: setup_network_full.bat + start_aplikasi.bat
```

### Scenario 2: Owner Mau Monitoring dari Rumah
```
1. Baca: DECISION_TREE_AKSES.md
2. Pilih: VPN (paling aman)
3. Setup: PANDUAN_AKSES_INTERNET.md (Opsi 4)
4. Security: SECURITY_CHECKLIST.md
```

### Scenario 3: Demo ke Investor
```
1. Baca: PERBANDINGAN_AKSES.md (Metode Ngrok)
2. Download: download_ngrok.bat
3. Setup authtoken di ngrok.com
4. Jalankan: start_with_ngrok.bat
5. Share URL yang muncul
```

### Scenario 4: Buka Cabang Baru di Kota Lain
```
1. Baca: DECISION_TREE_AKSES.md
2. Pilih: Cloudflare Tunnel atau VPS
3. Setup: PANDUAN_AKSES_INTERNET.md (Opsi 3 atau 5)
4. Security: SECURITY_CHECKLIST.md (WAJIB!)
```

### Scenario 5: Troubleshooting
```
1. Cek: test_network.bat
2. Baca: TROUBLESHOOTING.md
3. FAQ: FAQ_AKSES_INTERNET.md
4. Logs: storage/logs/laravel.log
```

---

## 🎯 Recommended Reading Path

### Path 1: Kasir / Staff (Basic User)
```
1. README.md (overview)
2. QUICK_REF_AKSES_JARINGAN.md (quick ref)
3. Done! ✅
```

### Path 2: Owner (Advanced User)
```
1. README.md
2. PANDUAN_AKSES_JARINGAN.md
3. DECISION_TREE_AKSES.md
4. PANDUAN_AKSES_INTERNET.md (jika perlu akses internet)
5. SECURITY_CHECKLIST.md (jika akses internet)
```

### Path 3: IT Admin (Technical User)
```
1. README.md
2. QUICK_START.md
3. PANDUAN_AKSES_JARINGAN.md
4. PANDUAN_AKSES_INTERNET.md
5. SECURITY_CHECKLIST.md
6. TROUBLESHOOTING.md
7. All other docs as needed
```

---

## 🔍 Search by Topic

### Keamanan
- [SECURITY_CHECKLIST.md](SECURITY_CHECKLIST.md)
- [PANDUAN_AKSES_INTERNET.md](PANDUAN_AKSES_INTERNET.md) (section Keamanan)
- [FAQ_AKSES_INTERNET.md](FAQ_AKSES_INTERNET.md) (Q16-Q20)

### Performa
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) (section Performance)
- [FAQ_AKSES_INTERNET.md](FAQ_AKSES_INTERNET.md) (Q10)

### Biaya
- [PERBANDINGAN_AKSES.md](PERBANDINGAN_AKSES.md) (section Biaya)
- [FAQ_AKSES_INTERNET.md](FAQ_AKSES_INTERNET.md) (Q21-Q23)

### Backup
- [TROUBLESHOOTING_BACKUP.md](TROUBLESHOOTING_BACKUP.md)
- [README.md](README.md) (section Backup)

### Import/Export Data
- [import-export-products.md](docs/import-export-products.md)
- [PANDUAN_SKU_PRODUK.md](docs/PANDUAN_SKU_PRODUK.md)

---

## 💡 Tips Navigasi

### Untuk Dokumentasi Offline:
1. Buka folder project di VS Code
2. Tekan `Ctrl+P`
3. Ketik nama file (misal: `panduan`)
4. Markdown preview: `Ctrl+Shift+V`

### Untuk Quick Search:
1. Tekan `Ctrl+Shift+F` di VS Code
2. Ketik keyword (misal: "ngrok", "firewall")
3. Lihat hasil di semua file

### Untuk TOC (Table of Contents):
- Setiap file .md punya heading yang bisa di-navigate
- Di VS Code: Outline panel di sidebar

---

## 📞 Butuh Bantuan?

### Step 1: Cek Dokumentasi
Gunakan INDEX ini untuk cari dokumen yang sesuai

### Step 2: Cek FAQ
[FAQ_AKSES_INTERNET.md](FAQ_AKSES_INTERNET.md) punya 35+ Q&A

### Step 3: Run Diagnostic
```batch
test_network.bat         # Untuk akses lokal
cek_mysql_status.bat     # Untuk database
```

### Step 4: Check Logs
```bash
# Laravel logs
cat storage/logs/laravel.log

# Atau di Windows
notepad storage\logs\laravel.log
```

### Step 5: Troubleshooting Guide
[TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📊 Documentation Statistics

- **Total Dokumentasi**: 15+ files
- **Total Scripts**: 10+ batch files
- **Total Coverage**: 
  - ✅ Setup & Installation
  - ✅ Akses Lokal (WiFi)
  - ✅ Akses Internet (5 metode)
  - ✅ Security (lengkap)
  - ✅ Troubleshooting
  - ✅ FAQ (35+ pertanyaan)

---

## 🎓 Learning Resources

### Untuk Pemula:
1. Start: README.md
2. Next: QUICK_START.md
3. Practice: Setup aplikasi di laptop
4. Advanced: PANDUAN_AKSES_JARINGAN.md

### Untuk Advanced:
1. PANDUAN_AKSES_INTERNET.md (semua opsi)
2. SECURITY_CHECKLIST.md
3. Deploy ke production

### External Resources:
- Laravel Docs: https://laravel.com/docs
- Ngrok Docs: https://ngrok.com/docs
- Cloudflare Docs: https://developers.cloudflare.com/
- WireGuard Docs: https://www.wireguard.com/

---

## 🔄 Update History

- **Jan 2026**: Initial documentation (akses lokal + internet)
- **Jan 2026**: Added security checklist
- **Jan 2026**: Added FAQ & decision tree

---

## ✅ Checklist: Dokumentasi Yang Sudah Dibaca

Personal checklist untuk track progress:

### Basic
- [ ] README.md
- [ ] QUICK_START.md
- [ ] QUICK_REF_AKSES_JARINGAN.md

### Intermediate
- [ ] PANDUAN_AKSES_JARINGAN.md
- [ ] PERBANDINGAN_AKSES.md
- [ ] DECISION_TREE_AKSES.md

### Advanced
- [ ] PANDUAN_AKSES_INTERNET.md
- [ ] SECURITY_CHECKLIST.md
- [ ] FAQ_AKSES_INTERNET.md

### Maintenance
- [ ] TROUBLESHOOTING.md
- [ ] TROUBLESHOOTING_BACKUP.md

---

**Toko Obat Ro Tua - Complete Documentation Index**  
*Update: Januari 2026*

**Happy Reading! 📚**
