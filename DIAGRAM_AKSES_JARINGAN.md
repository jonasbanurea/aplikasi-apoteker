# 📊 Diagram Akses Jaringan - Toko Obat Ro Tua

## Arsitektur Jaringan

```
┌─────────────────────────────────────────────────────────────┐
│                    INTERNET                                  │
│                 (Optional - Via Port Forwarding/Ngrok)       │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           │
                    ┌──────▼──────┐
                    │   ROUTER     │
                    │   WiFi       │
                    │ 192.168.1.1  │
                    └──────┬───────┘
                           │
         ┌─────────────────┼─────────────────┐
         │                 │                 │
         │                 │                 │
┌────────▼─────────┐  ┌────▼────────┐  ┌────▼────────┐
│  LAPTOP SERVER   │  │   HP/PHONE  │  │   TABLET    │
│  (Windows 11)    │  │             │  │             │
│                  │  │  Browser:   │  │  Browser:   │
│ 192.168.1.100    │  │  Chrome     │  │  Safari     │
│ Port: 8000       │  │             │  │             │
│                  │  └─────────────┘  └─────────────┘
│ Laravel App      │
│ + MySQL          │
│ + XAMPP          │
└──────────────────┘

Akses URL dari device lain:
http://192.168.1.100:8000
```

---

## Flow Diagram - Setup & Akses

```
┌─────────────────────────────────────────────────────────────┐
│                 SETUP AWAL (Sekali saja)                     │
└─────────────────────────────────────────────────────────────┘

  [1] Set IP Static Windows 11
      Settings → Network → Properties → Manual IP
      ↓
  [2] Setup Firewall
      Run: setup_firewall.bat (as Administrator)
      atau: setup_network_full.bat
      ↓
  [3] Setup Power Management (Optional)
      Run: setup_power.bat (as Administrator)
      Agar laptop tidak sleep
      ↓
  [4] Test Akses
      - Jalankan: start_aplikasi.bat
      - Catat IP yang ditampilkan
      - Coba akses dari HP

┌─────────────────────────────────────────────────────────────┐
│              AKSES HARIAN (Setiap hari)                      │
└─────────────────────────────────────────────────────────────┘

  [1] Laptop Server
      - Pastikan laptop hidup & terhubung WiFi
      - Jalankan: start_aplikasi.bat
      - Biarkan aplikasi berjalan
      ↓
  [2] Device Lain (HP/Tablet)
      - Hubungkan ke WiFi yang SAMA
      - Buka browser
      - Akses: http://192.168.1.100:8000
      - Login dengan akun kasir/gudang/owner
```

---

## Alur Request dari HP ke Server

```
┌──────────────┐
│   HP/TABLET  │
│              │
│  User buka   │
│  browser &   │
│  ketik URL   │
└──────┬───────┘
       │
       │ http://192.168.1.100:8000
       │
       ▼
┌──────────────┐
│   ROUTER     │  ← WiFi signal (pastikan sama!)
│              │
│  Forward ke  │
│  192.168.1.  │
│  100:8000    │
└──────┬───────┘
       │
       │ TCP/IP packet
       │
       ▼
┌──────────────┐
│   FIREWALL   │  ← Port 8000 harus TERBUKA
│  Windows 11  │
│              │
│  Allow 8000? │
│  ✓ YES       │
└──────┬───────┘
       │
       │ Request diterima
       │
       ▼
┌──────────────┐
│   LARAVEL    │
│   SERVER     │
│              │
│  Process     │
│  request &   │
│  return HTML │
└──────┬───────┘
       │
       │ Response
       │
       ▼
    [HP/TABLET]
    Tampilkan halaman login
```

---

## Troubleshooting Flow

```
❌ HP tidak bisa akses aplikasi
   │
   ├─→ Cek 1: Apakah laptop server hidup?
   │          ❌ NO → Hidupkan laptop & jalankan aplikasi
   │          ✓ YES → Next
   │
   ├─→ Cek 2: Apakah aplikasi berjalan?
   │          ❌ NO → Jalankan start_aplikasi.bat
   │          ✓ YES → Next
   │
   ├─→ Cek 3: Apakah HP & laptop di WiFi yang sama?
   │          ❌ NO → Hubungkan ke WiFi yang sama
   │          ✓ YES → Next
   │
   ├─→ Cek 4: Apakah IP address benar?
   │          ❌ NO → Cek IP di layar laptop saat start
   │          ✓ YES → Next
   │
   ├─→ Cek 5: Apakah firewall sudah dibuka?
   │          ❌ NO → Jalankan setup_firewall.bat
   │          ✓ YES → Next
   │
   ├─→ Cek 6: Ping test dari HP
   │          Buka terminal/cmd di HP
   │          ketik: ping 192.168.1.100
   │          ❌ Request timeout → Masalah network/firewall
   │          ✓ Reply → Firewall OK, cek aplikasi
   │
   └─→ Masih gagal? → Restart router & laptop → Coba lagi
```

---

## Keamanan & Best Practices

```
┌─────────────────────────────────────────────────────────────┐
│              KEAMANAN JARINGAN LOKAL                         │
└─────────────────────────────────────────────────────────────┘

✓ AMAN:
  - Akses hanya dari jaringan lokal (WiFi yang sama)
  - Tidak terekspos ke internet
  - Data tidak keluar dari jaringan toko

⚠️ PERHATIAN:
  - Semua yang terhubung WiFi sama bisa akses
  - Gunakan password WiFi yang kuat
  - Jangan share password WiFi sembarangan

❌ TIDAK DIREKOMENDASIKAN:
  - Akses dari WiFi publik/warnet
  - Port forwarding tanpa VPN/security
  - Tidak ada password di akun user


┌─────────────────────────────────────────────────────────────┐
│              PERFORMANCE TIPS                                │
└─────────────────────────────────────────────────────────────┘

🚀 OPTIMAL:
  - Laptop dedicated untuk server (tidak tutup)
  - RAM minimal 8GB (16GB lebih baik)
  - Router WiFi 5GHz (lebih cepat dari 2.4GHz)
  - Jarak HP ke router < 10 meter

⚡ JIKA LAMBAT:
  - Dekatkan router ke area kasir
  - Upgrade router jika perlu
  - Batasi device yang terhubung
  - Clear cache browser HP berkala
```

---

## Checklist Instalasi

```
SETUP AWAL (Sekali saja):
□ Set IP Static di Windows 11
□ Jalankan setup_network_full.bat (as Admin)
□ Atau jalankan setup_firewall.bat + setup_power.bat
□ Test akses dari HP
□ Bookmark URL di browser HP
□ (Optional) Generate & print QR Code

VERIFIKASI:
□ IP Address: 192.168.1.xxx (sesuai setting)
□ Firewall Port 8000: Terbuka
□ Power Settings: Tidak sleep saat colokan
□ Akses dari HP: Berhasil
□ Login kasir: Berhasil
□ Performance: Cepat & responsif

DOKUMENTASI:
□ Catat IP Address di sticky note/kartu
□ Print quick reference card
□ Simpan backup .env file
□ Dokumentasikan password WiFi

TRAINING STAFF:
□ Cara akses dari HP
□ Cara login (username/password)
□ Cara logout setelah selesai
□ Apa yang dilakukan jika error
□ Kontak IT/Owner untuk bantuan
```

---

## Skenario Penggunaan

### Skenario 1: Toko dengan 1 Laptop + 2 HP Kasir
```
Laptop (Server)  ←──WiFi──→  HP Kasir 1
                 ↓
                WiFi
                 ↓
             HP Kasir 2

Setup:
- 1 laptop sebagai server (taruh di office/back room)
- 2 HP untuk kasir (di counter)
- Semua connect ke WiFi toko
- HP akses via browser

Keuntungan:
- Data terpusat di laptop
- Kasir mobile dengan HP
- Hemat biaya hardware
```

### Skenario 2: Toko dengan Laptop + Tablet Admin Gudang
```
Laptop (Server)  ←──WiFi──→  Tablet Admin Gudang

Setup:
- Laptop untuk owner (kasir manual jika perlu)
- Tablet untuk admin gudang (stock opname, terima barang)
- Admin bisa keliling gudang sambil input data

Keuntungan:
- Stock opname lebih mudah
- Admin tidak terikat di meja
- Real-time update stok
```

---

**Toko Obat Ro Tua**  
*Dokumentasi Teknis - Januari 2026*
