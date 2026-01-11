# ❓ FAQ - Akses Aplikasi dari Internet

## 🌐 Pertanyaan Umum tentang Akses Internet

### Q1: Apakah aman mengakses aplikasi dari internet?
**A:** Tergantung metode yang digunakan:
- ⭐⭐⭐⭐⭐ **VPN**: Paling aman, seperti akses lokal
- ⭐⭐⭐⭐⭐ **Cloudflare Tunnel**: Sangat aman dengan DDoS protection
- ⭐⭐⭐ **Ngrok**: Aman untuk demo/testing, jangan untuk production
- ⭐⭐ **Port Forwarding**: Tidak direkomendasikan tanpa keamanan tambahan

**Tips Keamanan:**
- Selalu gunakan HTTPS
- Ganti password default
- Enable rate limiting
- Monitor access logs
- Backup rutin

### Q2: Berapa biaya untuk akses dari internet?
**A:** Bervariasi:
- **Gratis**: VPN, Cloudflare Tunnel, Ngrok (basic)
- **Paid**: Ngrok Pro ($8/bln), VPS ($4-10/bln)
- **Biaya Internet**: +10-20GB/bulan jika banyak akses

### Q3: Apakah laptop harus hidup terus 24/7?
**A:** 
- **Ya**, jika aplikasi di laptop (untuk VPN/Ngrok/Cloudflare Tunnel)
- **Tidak**, jika deploy ke VPS/Cloud Hosting

**Tips untuk laptop 24/7:**
- Gunakan UPS (agar tidak mati jika listrik padam)
- Setting power: tidak sleep, tidak hibernate
- Gunakan cooling pad
- Regular maintenance & cleaning

### Q4: URL Ngrok berubah terus, bagaimana solusinya?
**A:**
- **Gratis**: Terima URL baru setiap restart
- **Paid** ($8/bln): Custom domain tetap `rotua.ngrok.io`
- **Alternatif**: Gunakan Cloudflare Tunnel (gratis, URL tetap)

### Q5: Apakah bisa akses tanpa port forwarding?
**A:** Ya, gunakan:
- ✅ Ngrok (tidak perlu port forwarding)
- ✅ Cloudflare Tunnel (tidak perlu port forwarding)
- ✅ VPN via Ngrok/Cloudflare (advanced)

### Q6: Bagaimana jika IP public berubah-ubah (dynamic)?
**A:** Gunakan Dynamic DNS:
- **No-IP** (gratis): Domain seperti `rotua.ddns.net`
- **DuckDNS** (gratis): Domain seperti `rotua.duckdns.org`
- **Cloudflare**: Gunakan tunnel, tidak perlu khawatir IP berubah

### Q7: Berapa kecepatan internet yang dibutuhkan?
**A:**
- **Minimal**: 5 Mbps upload (untuk 2-3 user)
- **Recommended**: 10 Mbps upload (untuk 5-10 user)
- **Production**: 20+ Mbps upload (untuk 10+ user)

Cek kecepatan: [speedtest.net](https://speedtest.net)

### Q8: Apakah bisa akses dari HP tanpa install aplikasi?
**A:** Ya! Akses via browser:
- Chrome (Android)
- Safari (iOS)
- Firefox (Android/iOS)

Tidak perlu install aplikasi khusus, kecuali untuk VPN (butuh WireGuard app).

### Q9: Bagaimana cara monitoring siapa yang akses?
**A:** Laravel sudah punya audit log:
- Cek file: `storage/logs/laravel.log`
- Atau buat dashboard monitoring
- Install Laravel Telescope (development)

**View audit log:**
```bash
# Via browser
Menu: Reports → Audit Log

# Via file
notepad storage/logs/laravel.log
```

### Q10: Aplikasi lambat saat diakses dari internet, kenapa?
**A:** Beberapa penyebab:
1. **Internet lambat**: Upgrade paket internet
2. **Server overload**: Upgrade RAM laptop atau pindah ke VPS
3. **Database tidak optimal**: Tambah index, optimize queries
4. **Tidak ada cache**: Enable Laravel cache

**Quick fix:**
```bash
# Enable cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔧 Pertanyaan Teknis

### Q11: Bagaimana cara setup custom domain (apotek.com)?
**A:**
1. Beli domain di Niagahoster/Cloudflare ($1-15/tahun)
2. Tambah ke Cloudflare (gratis)
3. Setup Cloudflare Tunnel
4. Point DNS ke tunnel

**Lihat:** `PANDUAN_AKSES_INTERNET.md` (Opsi 3)

### Q12: Apakah bisa akses dari 2 metode sekaligus?
**A:** Ya! Kombinasi yang umum:
- Lokal (WiFi) + VPN (owner dari rumah)
- Lokal (WiFi) + Ngrok (demo sesekali)
- Lokal (WiFi) + Cloudflare Tunnel (cabang lain)

### Q13: Bagaimana cara backup jika akses dari internet?
**A:** Backup otomatis:
1. Setup scheduled backup di Laravel
2. Upload ke cloud storage (Google Drive/Dropbox)
3. Atau gunakan VPS dengan auto-backup

**Command:**
```bash
# Manual backup
Menu Owner → Backup

# Auto backup (setup cron)
php artisan schedule:work
```

### Q14: SSL/HTTPS wajib atau tidak?
**A:**
- ❌ **Lokal (WiFi)**: Tidak wajib (akses private)
- ✅ **Internet**: WAJIB (data lewat publik)

**Cara dapat SSL gratis:**
- Ngrok: HTTPS otomatis
- Cloudflare Tunnel: HTTPS otomatis
- VPS: Let's Encrypt (gratis)

### Q15: Bagaimana cara cek apakah aplikasi sudah bisa diakses internet?
**A:**
1. **Test dari HP** (matikan WiFi, pakai data seluler):
   ```
   Buka browser
   Akses URL aplikasi
   Coba login
   ```

2. **Test dengan online tools**:
   - https://www.canyouseeme.org/
   - https://check-host.net/

3. **Test dengan curl**:
   ```bash
   curl -I http://your-public-ip:8000
   ```

---

## 🔒 Pertanyaan Keamanan

### Q16: Bagaimana mencegah brute force attack?
**A:**
1. **Rate limiting** (sudah built-in Laravel)
2. **Captcha** pada login (optional)
3. **Fail2ban** jika di VPS
4. **IP whitelist** untuk admin

**Edit `.env`:**
```env
LOGIN_THROTTLE=5,1  # 5 attempts per 1 minute
```

### Q17: Apakah data aman di Ngrok/Cloudflare?
**A:**
- **Ngrok**: Data lewat server Ngrok (encrypted)
- **Cloudflare**: Data lewat Cloudflare CDN (encrypted + DDoS protection)

Kedua aman untuk data bisnis, tapi:
- Jangan kirim data super sensitive (KTP, CC) via Ngrok free
- Cloudflare lebih aman untuk production

### Q18: Bagaimana jika laptop dicuri/hilang?
**A:**
1. **Ganti password** semua akun immediately
2. **Revoke** Ngrok/Cloudflare token
3. **Change** database password
4. **Restore** dari backup di laptop baru

**Prevention:**
- Encrypt hard disk (BitLocker)
- Backup rutin ke cloud
- Jangan simpan password di sticky notes

### Q19: Apakah perlu antivirus di server?
**A:** Ya, recommended:
- **Windows Defender** (built-in, gratis)
- **Malwarebytes** (scan berkala)
- **Firewall** aktif
- **Updates** Windows rutin

### Q20: Bagaimana cara monitoring jika ada hacker?
**A:**
1. **Check logs** berkala:
   ```bash
   # Laravel logs
   cat storage/logs/laravel.log
   
   # Nginx/Apache logs
   cat /var/log/nginx/access.log
   ```

2. **Install monitoring**:
   - Laravel Telescope (development)
   - Sentry (error tracking)
   - UptimeRobot (uptime monitoring)

3. **Alert setup**:
   - Email jika ada login failed >5x
   - Email jika server down
   - SMS untuk critical errors

---

## 💰 Pertanyaan Biaya

### Q21: Apa opsi termurah untuk akses internet?
**A:**
1. **Gratis**: VPN, Cloudflare Tunnel, Ngrok (basic)
2. **Termurah Paid**: VPS Vultr ($2.5/bulan)
3. **Best Value**: Cloudflare Tunnel (gratis unlimited!)

### Q22: Berapa biaya internet bulanan jika banyak akses?
**A:** Estimasi:
- **5 user, 8 jam/hari**: ~10 GB/bulan
- **10 user, 8 jam/hari**: ~20 GB/bulan
- **20 user, 8 jam/hari**: ~40 GB/bulan

**Tips hemat:**
- Gunakan WiFi di lokasi (gratis)
- Compress images/assets
- Enable caching
- Lazy load data

### Q23: Apakah ada biaya hidden?
**A:** Perhatikan:
- **Domain**: $1-15/tahun (jika mau custom domain)
- **SSL**: Gratis (Let's Encrypt/Cloudflare)
- **VPS**: $4-10/bulan (jika butuh uptime 24/7)
- **Backup storage**: Gratis (Google Drive 15GB)
- **Internet**: Unlimited recommended

---

## 🎯 Pertanyaan Spesifik Use Case

### Q24: Saya owner, mau monitoring dari rumah. Pakai apa?
**A:** Gunakan **VPN (WireGuard)**:
- Paling aman
- Akses seperti di toko
- Gratis
- Setup 20 menit

**Lihat:** `PANDUAN_AKSES_INTERNET.md` (Opsi 4)

### Q25: Mau demo ke investor, pakai apa?
**A:** Gunakan **Ngrok**:
- Setup 5 menit
- Share URL langsung
- HTTPS otomatis
- Gratis

**Quick start:**
```batch
download_ngrok.bat
start_with_ngrok.bat
```

### Q26: Mau buka cabang di kota lain, pakai apa?
**A:** Gunakan **Cloudflare Tunnel**:
- Gratis unlimited
- Custom domain
- HTTPS gratis
- Production ready

**Lihat:** `PANDUAN_AKSES_INTERNET.md` (Opsi 3)

### Q27: Mau franchise 10+ outlet, pakai apa?
**A:** Gunakan **VPS/Cloud Hosting**:
- Uptime 99.9%
- Scalable
- Professional
- $10/bulan

Platform: DigitalOcean, AWS, Vultr

### Q28: Kasir mau akses dari rumah (WFH), bisa?
**A:** Bisa dengan 2 cara:
1. **VPN**: Kasir install WireGuard, connect, akses
2. **Cloudflare Tunnel**: Kasir akses via URL public

**Recommended**: VPN (lebih aman)

---

## 🛠️ Troubleshooting

### Q29: Sudah setup Ngrok tapi tidak bisa akses
**A:** Cek:
1. Ngrok running? Lihat terminal
2. Laravel running? `netstat -ano | findstr :8000`
3. Copy URL yang benar dari Ngrok
4. Coba akses via incognito mode
5. Cek authtoken sudah setup?

### Q30: Port forwarding tidak jalan
**A:** Cek:
1. Rule di router sudah benar?
2. IP laptop static (tidak berubah)?
3. Firewall Windows allow port 8000?
4. ISP tidak block port? (test port lain)
5. Router support port forwarding?

**Test:**
```bash
# Dari laptop lain/HP (luar jaringan)
curl http://YOUR_PUBLIC_IP:8000
```

### Q31: Cloudflare Tunnel error "tunnel credentials"
**A:**
1. Cek file `config.yml` path benar
2. Cek credentials file `.cloudflared/xxx.json` ada
3. Jalankan `cloudflared tunnel login` lagi
4. Restart tunnel

### Q32: VPN connect tapi tidak bisa akses aplikasi
**A:** Cek:
1. VPN connected? Cek icon WireGuard
2. AllowedIPs di config benar? (192.168.1.0/24)
3. Laptop server firewall allow VPN?
4. Ping ke IP laptop: `ping 192.168.1.100`

---

## 📚 Referensi & Resources

### Q33: Dimana dokumentasi lengkapnya?
**A:**
- **Akses Lokal**: `PANDUAN_AKSES_JARINGAN.md`
- **Akses Internet**: `PANDUAN_AKSES_INTERNET.md`
- **Perbandingan**: `PERBANDINGAN_AKSES.md`
- **Decision Tree**: `DECISION_TREE_AKSES.md`
- **Quick Ref**: `QUICK_REF_AKSES_JARINGAN.md`

### Q34: Ada video tutorial?
**A:** Saat ini belum ada video, tapi dokumentasi lengkap dengan:
- Step-by-step screenshots
- Command examples
- Troubleshooting guide
- Real-world use cases

### Q35: Dimana bisa minta bantuan?
**A:**
- **Dokumentasi**: Baca file MD di folder project
- **Logs**: Cek `storage/logs/laravel.log`
- **Test**: Jalankan `test_network.bat`
- **Community**: Laravel Indonesia Telegram

---

## 📞 Kontak & Support

Ada pertanyaan lain? 
- Baca dokumentasi lengkap di folder project
- Cek troubleshooting di `TROUBLESHOOTING.md`
- Test dengan script yang tersedia

---

**Toko Obat Ro Tua**  
*FAQ Akses Internet - Januari 2026*
