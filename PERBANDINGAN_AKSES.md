# 🌐 Quick Guide: Lokal vs Internet Access

## Perbandingan Cepat

### 📱 AKSES LOKAL (WiFi Same Network)
```
✅ RECOMMENDED untuk operasional sehari-hari

┌─────────────────────────────────────┐
│  LAPTOP SERVER (Toko)               │
│  192.168.1.100:8000                 │
│         │                           │
│         │ WiFi Router               │
│         │                           │
│    ┌────┴────┐                      │
│    │         │                      │
│  [HP 1]   [HP 2]  [Tablet]          │
│  Kasir    Kasir   Admin Gudang      │
└─────────────────────────────────────┘

Akses: http://192.168.1.100:8000
```

**Keunggulan:**
- ⚡ Sangat cepat (< 50ms)
- 🔒 Aman (hanya di toko)
- 💰 Gratis (tidak pakai kuota)
- ✅ Tidak perlu setup rumit

**Cara Setup:**
1. Jalankan: `setup_network_full.bat`
2. Start: `start_aplikasi.bat`
3. Akses dari HP dengan URL yang ditampilkan

---

### 🌍 AKSES INTERNET (Dari Mana Saja)
```
❗ HANYA jika benar-benar dibutuhkan

┌─────────────────────────────────────┐
│         INTERNET                    │
│            ▲                        │
│            │                        │
│      ┌─────┴──────┐                │
│      │            │                │
│   [HP Owner]  [Laptop Owner]        │
│   dari rumah  dari cafe             │
│              dari hotel             │
└─────────────────────────────────────┘

Akses: https://apotek.yourdomain.com
```

**Metode Tersedia:**

#### 1. 🚀 Ngrok (Termudah)
**Setup Time:** 5 menit  
**Biaya:** Gratis (basic)  
**Keamanan:** ⭐⭐⭐

```batch
# Download & Install
download_ngrok.bat

# Jalankan
start_with_ngrok.bat
```

**URL Contoh:** `https://abc123.ngrok-free.app`

---

#### 2. ☁️ Cloudflare Tunnel (Recommended)
**Setup Time:** 30 menit  
**Biaya:** Gratis  
**Keamanan:** ⭐⭐⭐⭐⭐

**Keunggulan:**
- Custom domain (apotek.rotua.com)
- HTTPS gratis
- DDoS protection
- Production ready

**Lihat:** `PANDUAN_AKSES_INTERNET.md`

---

#### 3. 🔐 VPN (Paling Aman)
**Setup Time:** 20 menit  
**Biaya:** Gratis  
**Keamanan:** ⭐⭐⭐⭐⭐

**Cocok untuk:** Owner akses dari rumah

**Cara Kerja:**
- Install VPN di HP owner
- Connect VPN
- Akses seperti di toko: `http://192.168.1.100:8000`

**Lihat:** `PANDUAN_AKSES_INTERNET.md` (Opsi 4)

---

## 🎯 Pilih Yang Mana?

### Use Case 1: Operasional Harian Toko
```
👥 Kasir + Admin Gudang di toko
🌐 Akses: LOKAL (WiFi)
📖 Panduan: PANDUAN_AKSES_JARINGAN.md
```

### Use Case 2: Owner Monitoring dari Rumah (Sesekali)
```
👤 Owner dari rumah/perjalanan
🌐 Akses: VPN atau Ngrok
📖 Panduan: PANDUAN_AKSES_INTERNET.md (Opsi 4 atau 2)
```

### Use Case 3: Demo ke Investor/Client
```
👥 Share ke orang lain temporary
🌐 Akses: Ngrok
📖 Panduan: PANDUAN_AKSES_INTERNET.md (Opsi 2)
```

### Use Case 4: Cabang di Kota Lain (Permanent)
```
🏢 Multiple toko berbeda lokasi
🌐 Akses: Cloud Hosting atau Cloudflare Tunnel
📖 Panduan: PANDUAN_AKSES_INTERNET.md (Opsi 3 atau 5)
```

---

## ⚠️ Pertimbangan Keamanan

### AKSES LOKAL
- ✅ Data tidak keluar dari toko
- ✅ Tidak bisa diakses hacker
- ✅ Tidak perlu SSL/HTTPS
- ✅ Password sederhana OK

### AKSES INTERNET
- ⚠️ Data lewat internet
- ⚠️ Rentan serangan jika tidak diamankan
- ⚠️ WAJIB gunakan HTTPS
- ⚠️ Password harus kuat
- ⚠️ Perlu monitoring & backup rutin

---

## 📊 Perbandingan Biaya

| Aspek | Lokal | Internet (Ngrok) | Internet (Cloud) |
|-------|-------|------------------|------------------|
| **Setup** | Gratis | Gratis | $4-10/bulan |
| **Internet** | Normal | +10-20GB/bulan | Unlimited |
| **Maintenance** | Minimal | Minimal | Regular updates |
| **SSL/HTTPS** | Tidak perlu | Otomatis | Setup manual |
| **Total/Bulan** | Rp 0 | Rp 0-150rb | Rp 50-150rb |

---

## 🚀 Quick Start Commands

### Untuk Akses Lokal
```batch
# Setup awal (sekali saja)
setup_network_full.bat

# Jalankan aplikasi
start_aplikasi.bat

# Test koneksi
test_network.bat
```

### Untuk Akses Internet (Ngrok)
```batch
# Download Ngrok
download_ngrok.bat

# Setup authtoken (sekali saja)
ngrok.exe config add-authtoken YOUR_TOKEN

# Jalankan dengan Ngrok
start_with_ngrok.bat
```

---

## 📞 Bantuan

### Akses Lokal Tidak Bisa
1. Cek: `test_network.bat`
2. Baca: `PANDUAN_AKSES_JARINGAN.md`
3. Troubleshooting: `TROUBLESHOOTING.md`

### Akses Internet Tidak Bisa
1. Cek internet connection
2. Baca: `PANDUAN_AKSES_INTERNET.md`
3. Test dengan curl/Postman

---

## 🎓 Best Practices

### ✅ DO:
- Gunakan akses lokal untuk operasional
- Gunakan internet hanya jika perlu
- Backup rutin jika akses internet
- Monitor logs secara berkala
- Update password secara berkala

### ❌ DON'T:
- Jangan expose ke internet tanpa keamanan
- Jangan gunakan password default
- Jangan share credentials sembarangan
- Jangan lupa backup data
- Jangan akses dari WiFi public tanpa VPN

---

**Toko Obat Ro Tua**  
*Quick Reference - Januari 2026*
