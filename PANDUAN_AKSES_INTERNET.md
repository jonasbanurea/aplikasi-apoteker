# Panduan Akses Aplikasi dari Internet

## ⚠️ PENTING - Baca Dulu!

**Akses dari internet memiliki RISIKO KEAMANAN yang perlu dipertimbangkan:**
- Data sensitif (obat, harga, transaksi) bisa terekspos
- Serangan hacker/malware jika tidak diamankan dengan baik
- Biaya internet bisa meningkat
- Performa bisa lebih lambat

**Rekomendasi:**
- ✅ **Akses Lokal (WiFi)**: Aman, cepat, gratis → Untuk operasional sehari-hari
- ⚠️ **Akses Internet**: Hanya jika benar-benar dibutuhkan → Untuk monitoring owner dari rumah

---

## 📊 Perbandingan Metode Akses Internet

| Metode | Keamanan | Kemudahan | Biaya | Kecepatan | Cocok Untuk |
|--------|----------|-----------|-------|-----------|-------------|
| **Port Forwarding** | ⭐⭐ | ⭐⭐ | Gratis | ⭐⭐⭐⭐ | Development/Test |
| **Ngrok** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | Gratis/Paid | ⭐⭐⭐ | Development/Demo |
| **Cloudflare Tunnel** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | Gratis | ⭐⭐⭐⭐ | **Production** ✅ |
| **VPN (WireGuard)** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | Gratis | ⭐⭐⭐⭐ | Akses Owner |
| **Cloud Hosting** | ⭐⭐⭐⭐ | ⭐⭐ | Paid | ⭐⭐⭐⭐⭐ | Production |

---

## 🔧 Opsi 1: Port Forwarding (Router)

### Kelebihan
✅ Gratis  
✅ Akses langsung ke aplikasi  
✅ Performa baik

### Kekurangan
❌ Butuh akses ke router  
❌ IP Public berubah-ubah (butuh Dynamic DNS)  
❌ Risiko keamanan tinggi  
❌ Tidak cocok untuk production

### Langkah-langkah

#### A. Cek IP Public Anda
```bash
# Buka browser, kunjungi:
https://whatismyipaddress.com/
# Atau via PowerShell:
(Invoke-WebRequest -Uri "https://api.ipify.org").Content
```

#### B. Setup Port Forwarding di Router
1. **Login ke Router**
   - Buka browser: `http://192.168.1.1` atau `http://192.168.0.1`
   - Username/Password: Cek stiker di router atau manual
   - Default biasanya: admin/admin, admin/password

2. **Cari Menu Port Forwarding**
   - Nama menu bisa berbeda: "Port Forwarding", "Virtual Server", "NAT"
   - Lokasi: Advanced Settings → NAT → Port Forwarding

3. **Tambah Rule Baru**
   ```
   Service Name: Laravel App
   External Port: 8000
   Internal IP: 192.168.1.100 (IP laptop server)
   Internal Port: 8000
   Protocol: TCP
   Status: Enabled
   ```

4. **Save & Reboot Router**

#### C. Test Akses
```
http://[IP-PUBLIC-ANDA]:8000

Contoh:
http://103.147.8.123:8000
```

#### D. Setup Dynamic DNS (Agar IP tidak berubah)

**Menggunakan No-IP (Gratis):**
1. Daftar di [www.noip.com](https://www.noip.com)
2. Buat hostname: `rotua.ddns.net`
3. Download No-IP DUC client
4. Install dan login
5. Pilih hostname yang dibuat
6. Akses menggunakan: `http://rotua.ddns.net:8000`

---

## 🚀 Opsi 2: Ngrok (Recommended untuk Development)

### Kelebihan
✅ Sangat mudah digunakan  
✅ HTTPS otomatis  
✅ Tidak butuh konfigurasi router  
✅ Cocok untuk demo/testing

### Kekurangan
❌ URL berubah setiap restart (versi gratis)  
❌ Performa tergantung server Ngrok  
❌ Free tier ada batasan

### Langkah-langkah

#### A. Install Ngrok
1. Download dari [ngrok.com/download](https://ngrok.com/download)
2. Ekstrak file `ngrok.exe` ke folder aplikasi
3. Daftar akun di ngrok.com (gratis)
4. Dapatkan authtoken

#### B. Setup Ngrok
```powershell
# Jalankan di PowerShell (di folder aplikasi)
.\ngrok.exe authtoken YOUR_AUTH_TOKEN

# Start tunnel ke port 8000
.\ngrok.exe http 8000
```

#### C. Akses Aplikasi
```
Ngrok akan memberikan URL seperti:
https://abcd-1234-5678.ngrok-free.app

Copy URL tersebut dan akses dari browser mana saja
```

#### D. Buat Script Otomatis

**File: `start_with_ngrok.bat`**
```batch
@echo off
echo Starting Laravel with Ngrok...
echo.

REM Start Laravel di background
start "" cmd /c "php artisan serve --host=0.0.0.0 --port=8000"

REM Tunggu Laravel siap
timeout /t 5 /nobreak >nul

REM Start Ngrok
echo Starting Ngrok tunnel...
.\ngrok.exe http 8000

pause
```

### E. Ngrok Paid Features (Opsional)
- Custom domain: `rotua.ngrok.io`
- URL tetap (tidak berubah)
- Lebih banyak concurrent connections
- Harga: $8-10/bulan

---

## ☁️ Opsi 3: Cloudflare Tunnel (Recommended untuk Production)

### Kelebihan
✅ **GRATIS** tanpa batasan  
✅ Sangat aman (DDoS protection)  
✅ HTTPS otomatis  
✅ Domain custom gratis  
✅ Tidak butuh port forwarding  
✅ **RECOMMENDED** ⭐

### Kekurangan
❌ Setup awal agak rumit  
❌ Butuh domain sendiri (bisa gratis via Freenom/Cloudflare)

### Langkah-langkah Lengkap

#### A. Persiapan
1. **Buat Akun Cloudflare**
   - Daftar di [dash.cloudflare.com](https://dash.cloudflare.com/sign-up)
   - Verifikasi email

2. **Tambah Domain**
   - Jika belum punya domain:
     - Beli domain murah (Rp 15.000/tahun) di Niagahoster/Domainesia
     - Atau gratis di [Freenom](https://www.freenom.com): `.tk`, `.ml`, `.ga`
   - Tambah domain ke Cloudflare
   - Update nameserver di registrar

#### B. Install Cloudflared
```powershell
# Download cloudflared
Invoke-WebRequest -Uri "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe" -OutFile "cloudflared.exe"

# Login
.\cloudflared.exe tunnel login
# Browser akan terbuka, pilih domain Anda
```

#### C. Buat Tunnel
```powershell
# Buat tunnel baru
.\cloudflared.exe tunnel create rotua-apotek

# Catat Tunnel ID yang muncul
# Contoh: d4f2c8a7-8f9e-4c8b-9e5a-3c4d5e6f7g8h
```

#### D. Konfigurasi Tunnel

**Buat file: `config.yml`**
```yaml
tunnel: d4f2c8a7-8f9e-4c8b-9e5a-3c4d5e6f7g8h
credentials-file: C:\Users\YOUR_USER\.cloudflared\d4f2c8a7-8f9e-4c8b-9e5a-3c4d5e6f7g8h.json

ingress:
  - hostname: apotek.yourdomain.com
    service: http://localhost:8000
  - service: http_status:404
```

#### E. Setup DNS di Cloudflare
```powershell
# Route DNS ke tunnel
.\cloudflared.exe tunnel route dns rotua-apotek apotek.yourdomain.com
```

#### F. Jalankan Tunnel
```powershell
# Test dulu
.\cloudflared.exe tunnel run rotua-apotek

# Jika berhasil, install sebagai service
.\cloudflared.exe service install
```

#### G. Akses Aplikasi
```
https://apotek.yourdomain.com
```

#### H. Buat Script Otomatis

**File: `start_with_cloudflare.bat`**
```batch
@echo off
echo Starting Laravel with Cloudflare Tunnel...
echo.

REM Start Laravel
start "" cmd /c "php artisan serve --host=127.0.0.1 --port=8000"

REM Tunggu Laravel siap
timeout /t 5 /nobreak >nul

REM Start Cloudflare Tunnel
echo Starting Cloudflare Tunnel...
echo Your app will be available at: https://apotek.yourdomain.com
echo.
.\cloudflared.exe tunnel run rotua-apotek

pause
```

---

## 🔐 Opsi 4: VPN dengan WireGuard (Paling Aman)

### Kelebihan
✅ **PALING AMAN** 🔒  
✅ Seperti akses lokal  
✅ Gratis  
✅ Cocok untuk owner akses dari rumah

### Kekurangan
❌ Butuh install VPN client di HP  
❌ Setup agak kompleks  
❌ Hanya untuk beberapa user

### Langkah-langkah

#### A. Install WireGuard di Laptop Server
1. Download dari [wireguard.com/install](https://www.wireguard.com/install/)
2. Install WireGuard

#### B. Generate Keys
```powershell
# Buat private dan public key untuk server
wg genkey | tee server_private.key | wg pubkey > server_public.key

# Buat private dan public key untuk client (HP owner)
wg genkey | tee client_private.key | wg pubkey > client_public.key
```

#### C. Konfigurasi Server

**File: `wg0.conf`** (di laptop server)
```ini
[Interface]
PrivateKey = [SERVER_PRIVATE_KEY]
Address = 10.0.0.1/24
ListenPort = 51820

[Peer]
PublicKey = [CLIENT_PUBLIC_KEY]
AllowedIPs = 10.0.0.2/32
```

#### D. Konfigurasi Client

**File: `client.conf`** (untuk HP owner)
```ini
[Interface]
PrivateKey = [CLIENT_PRIVATE_KEY]
Address = 10.0.0.2/24
DNS = 8.8.8.8

[Peer]
PublicKey = [SERVER_PUBLIC_KEY]
Endpoint = [IP_PUBLIC_ROUTER]:51820
AllowedIPs = 192.168.1.0/24
PersistentKeepalive = 25
```

#### E. Port Forwarding untuk WireGuard
```
External Port: 51820 (UDP)
Internal IP: 192.168.1.100
Internal Port: 51820 (UDP)
Protocol: UDP
```

#### F. Install WireGuard di HP
1. Install app WireGuard dari Play Store/App Store
2. Import `client.conf` (via QR code atau file)
3. Aktifkan VPN
4. Akses: `http://192.168.1.100:8000`

---

## 🌐 Opsi 5: Cloud Hosting (Production Grade)

### Platform Recommended
1. **DigitalOcean** - $4/bulan (512MB RAM)
2. **Vultr** - $2.5/bulan
3. **AWS Lightsail** - $3.5/bulan
4. **Heroku** - Gratis tier (limited)

### Kelebihan
✅ Uptime tinggi (99.9%)  
✅ Backup otomatis  
✅ Scalable  
✅ Support 24/7

### Kekurangan
❌ Biaya bulanan  
❌ Butuh maintenance  
❌ Setup awal kompleks

### Setup Singkat
1. Buat VPS/Droplet
2. Install LAMP stack
3. Deploy aplikasi Laravel
4. Setup SSL dengan Let's Encrypt
5. Point domain ke IP VPS

---

## 🔒 Keamanan WAJIB untuk Akses Internet

### 1. HTTPS/SSL Wajib
```bash
# Jangan pernah akses via HTTP dari internet!
❌ http://yourapp.com
✅ https://yourapp.com
```

### 2. Strong Password
```php
// Update password default di database
❌ password
✅ R0Tu@Ap0t3k#2026!Str0ng
```

### 3. Rate Limiting
**Edit: `app/Http/Kernel.php`**
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
    ],
];
```

### 4. IP Whitelist (Optional)
**Edit: `.env`**
```env
ALLOWED_IPS=192.168.1.0/24,103.147.8.123
```

**Create Middleware: `CheckAllowedIp.php`**
```php
public function handle($request, Closure $next)
{
    $allowedIps = explode(',', env('ALLOWED_IPS', ''));
    
    if (!in_array($request->ip(), $allowedIps)) {
        abort(403, 'Access denied');
    }
    
    return $next($request);
}
```

### 5. Firewall (UFW di Server)
```bash
# Jika deploy ke VPS Linux
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### 6. Fail2Ban (Anti Brute Force)
```bash
# Install fail2ban di VPS
sudo apt install fail2ban
```

### 7. Regular Updates
```bash
# Update dependencies
composer update
npm update

# Update Laravel
php artisan migrate
php artisan cache:clear
```

---

## 📋 Checklist Sebelum Akses dari Internet

### Keamanan
- [ ] Password semua user sudah diganti yang kuat
- [ ] HTTPS/SSL sudah aktif
- [ ] Rate limiting sudah diaktifkan
- [ ] Firewall sudah dikonfigurasi
- [ ] Backup database rutin
- [ ] `.env` file tidak terekspos

### Performa
- [ ] Cache sudah dioptimasi
- [ ] Database sudah diindeks
- [ ] Image/assets sudah dikompress
- [ ] CDN dipertimbangkan (optional)

### Monitoring
- [ ] Logging error aktif
- [ ] Monitoring uptime (UptimeRobot)
- [ ] Alert jika ada issue
- [ ] Google Analytics (optional)

### Legal & Compliance
- [ ] Privacy policy
- [ ] Terms of service
- [ ] GDPR compliance (jika ke EU)
- [ ] Backup & disaster recovery plan

---

## 🎯 Rekomendasi Berdasarkan Kebutuhan

### Scenario 1: Owner ingin monitoring dari rumah
**Solusi:** VPN (WireGuard) ⭐⭐⭐⭐⭐
- Paling aman
- Akses seperti di toko
- Gratis

### Scenario 2: Demo ke investor/klien
**Solusi:** Ngrok ⭐⭐⭐⭐
- Cepat setup
- URL bisa dishare
- Temporary

### Scenario 3: Cabang baru di kota lain
**Solusi:** Cloudflare Tunnel ⭐⭐⭐⭐⭐
- Gratis
- Aman
- Custom domain
- Production ready

### Scenario 4: Franchise dengan banyak outlet
**Solusi:** Cloud Hosting (DigitalOcean) ⭐⭐⭐⭐⭐
- Scalable
- High availability
- Professional
- Support 24/7

---

## ⚠️ Pertimbangan Penting

### 1. Biaya Internet
- Akses dari internet akan menambah traffic
- Pastikan paket internet unlimited
- Bandwidth minimal 10 Mbps upload

### 2. Performa
- Akses dari internet lebih lambat dari lokal
- Latensi tergantung koneksi
- Optimasi database & caching penting

### 3. Legalitas
- Pastikan comply dengan regulasi
- Data pasien/transaksi harus aman
- Konsultasi dengan legal jika perlu

### 4. Backup
- Backup database HARIAN
- Backup offsite (cloud storage)
- Test restore secara berkala

### 5. Support
- Siapkan tim IT atau kontrak vendor
- Dokumentasi lengkap
- Kontak emergency

---

## 🛠️ Troubleshooting Internet Access

### Problem: Tidak bisa akses dari internet
**Solusi:**
1. Cek port forwarding di router
2. Cek firewall Windows
3. Test dengan `curl http://YOUR_PUBLIC_IP:8000`
4. Cek ISP tidak block port 8000

### Problem: Lambat saat diakses
**Solusi:**
1. Upgrade bandwidth internet
2. Optimasi database queries
3. Enable caching
4. Gunakan CDN untuk assets

### Problem: Sering disconnect
**Solusi:**
1. Cek koneksi internet stabil
2. Gunakan UPS untuk laptop
3. Setting auto-restart jika crash
4. Monitor uptime

---

## 📞 Support & Bantuan

### Resources
- **Ngrok Docs:** https://ngrok.com/docs
- **Cloudflare Docs:** https://developers.cloudflare.com/
- **WireGuard Docs:** https://www.wireguard.com/quickstart/
- **Laravel Security:** https://laravel.com/docs/security

### Community
- Laravel Indonesia: https://t.me/laravel_id
- Cloudflare Community: https://community.cloudflare.com/

---

## 🎓 Training & Best Practices

### Untuk Owner
1. Gunakan VPN untuk akses aman
2. Aktifkan 2FA jika tersedia
3. Jangan share credentials
4. Monitor activity logs

### Untuk Admin
1. Regular security audit
2. Update dependencies rutin
3. Backup sebelum perubahan
4. Test di staging dulu

---

**Toko Obat Ro Tua**  
*Panduan Akses Internet - Januari 2026*

---

## 📌 Quick Command Reference

```powershell
# Port Forwarding Test
curl http://YOUR_PUBLIC_IP:8000

# Ngrok
.\ngrok.exe http 8000

# Cloudflare Tunnel
.\cloudflared.exe tunnel run rotua-apotek

# Check IP Public
(Invoke-WebRequest -Uri "https://api.ipify.org").Content

# Test SSL
curl -I https://apotek.yourdomain.com
```
