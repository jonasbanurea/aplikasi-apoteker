# 🔒 Security Checklist - Akses Internet

## ⚠️ PENTING: Baca Sebelum Akses dari Internet!

Akses aplikasi dari internet memiliki risiko keamanan. Gunakan checklist ini untuk memastikan aplikasi Anda aman.

---

## 📋 Basic Security (WAJIB)

### ✅ 1. HTTPS/SSL Aktif
- [ ] Aplikasi menggunakan HTTPS (bukan HTTP)
- [ ] Certificate valid (tidak expired)
- [ ] Test SSL: https://www.ssllabs.com/ssltest/

**Status:**
- ✅ Ngrok: HTTPS otomatis
- ✅ Cloudflare Tunnel: HTTPS otomatis
- ❌ Port Forwarding: Perlu setup manual (Let's Encrypt)

### ✅ 2. Password Strong untuk Semua User
- [ ] Password minimal 12 karakter
- [ ] Kombinasi huruf besar, kecil, angka, simbol
- [ ] Tidak menggunakan password default

**Command untuk ganti password:**
```bash
# Via web
Login → Profile → Change Password

# Via database (emergency)
php artisan tinker
$user = User::where('email', 'owner@rotua.test')->first();
$user->password = Hash::make('NewStrongP@ssw0rd!2026');
$user->save();
```

**Contoh password kuat:**
```
❌ password
❌ 123456
❌ rotua2024
✅ R0tu@Ap0t3k#2026!Str0ng
✅ K@s1r#T0k0&Ob@t$2026
✅ Adm1nGud@ng!R0Tu@#99
```

### ✅ 3. Firewall Aktif
- [ ] Windows Firewall enabled
- [ ] Hanya port yang dibutuhkan dibuka
- [ ] Ufw/iptables configured (jika VPS Linux)

**Cek firewall:**
```powershell
# Windows
netsh advfirewall show allprofiles

# Hanya port 8000 dan 51820 (VPN) yang dibuka
```

### ✅ 4. Rate Limiting Enabled
- [ ] Login throttling aktif (max 5 attempts)
- [ ] API rate limiting configured
- [ ] Blocking IP setelah brute force

**Edit `app/Http/Kernel.php`:**
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
    ],
];
```

**Test rate limiting:**
```bash
# Coba login salah 6x berturut-turut
# Seharusnya blocked setelah 5x
```

### ✅ 5. .env File Tidak Terekspos
- [ ] `.env` tidak bisa diakses dari web
- [ ] `.gitignore` include `.env`
- [ ] Tidak ada backup `.env.backup` di public folder

**Test:**
```bash
# Seharusnya 404
curl http://your-domain.com/.env
```

---

## 🔐 Advanced Security (Recommended)

### ✅ 6. IP Whitelist (Optional)
- [ ] Hanya IP tertentu bisa akses admin
- [ ] IP kasir/gudang tidak bisa akses owner menu

**Create Middleware:**
```php
// app/Http/Middleware/IpWhitelist.php
public function handle($request, Closure $next)
{
    $allowedIps = [
        '192.168.1.0/24',  // Local network
        '103.147.8.123',   // Owner home
    ];
    
    if (!$this->isAllowed($request->ip(), $allowedIps)) {
        abort(403, 'Access denied from ' . $request->ip());
    }
    
    return $next($request);
}
```

### ✅ 7. Two-Factor Authentication (Optional)
- [ ] 2FA enabled untuk owner account
- [ ] Google Authenticator / Authy installed
- [ ] Backup codes saved

**Install package:**
```bash
composer require pragmarx/google2fa-laravel
```

### ✅ 8. Database Backup Rutin
- [ ] Auto backup setiap hari
- [ ] Backup uploaded ke cloud (Google Drive/Dropbox)
- [ ] Test restore backup berkala

**Setup cron:**
```bash
# Linux cron
0 2 * * * cd /path/to/app && php artisan backup:run

# Windows Task Scheduler
# Create task yang run backup.bat setiap jam 2 pagi
```

### ✅ 9. Audit Logging
- [ ] Login/logout logged
- [ ] Critical actions logged (delete, edit price)
- [ ] Failed login attempts logged

**Check logs:**
```bash
# Via web
Menu: Reports → Audit Log

# Via file
cat storage/logs/laravel.log | grep LOGIN
```

### ✅ 10. Security Headers
- [ ] X-Frame-Options: DENY
- [ ] X-Content-Type-Options: nosniff
- [ ] X-XSS-Protection: 1; mode=block

**Add to `.htaccess` or Nginx config:**
```apache
# Apache
Header set X-Frame-Options "DENY"
Header set X-Content-Type-Options "nosniff"
Header set X-XSS-Protection "1; mode=block"
```

---

## 🛡️ VPS/Cloud Security (Jika Deploy ke Server)

### ✅ 11. SSH Key Authentication
- [ ] Password login disabled
- [ ] SSH key-based authentication
- [ ] SSH port changed (bukan 22)

### ✅ 12. Fail2Ban Installed
- [ ] Fail2ban aktif
- [ ] Ban setelah 5 failed SSH attempts
- [ ] Ban setelah 10 failed login web

**Install:**
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### ✅ 13. Regular Updates
- [ ] OS updates rutin
- [ ] PHP/Laravel dependencies updated
- [ ] Security patches applied

**Command:**
```bash
# Update Laravel
composer update --with-dependencies

# Update OS (Ubuntu)
sudo apt update && sudo apt upgrade
```

### ✅ 14. Separate Database Server (Optional)
- [ ] Database tidak di server yang sama
- [ ] Database private network only
- [ ] Strong database password

### ✅ 15. WAF (Web Application Firewall)
- [ ] Cloudflare WAF enabled
- [ ] Security rules configured
- [ ] Rate limiting per IP

---

## 🔍 Monitoring & Detection

### ✅ 16. Uptime Monitoring
- [ ] UptimeRobot / Pingdom configured
- [ ] Alert via email jika down
- [ ] Check every 5 minutes

**Setup:**
1. Daftar di https://uptimerobot.com (gratis)
2. Add monitor: URL aplikasi
3. Set alert email

### ✅ 17. Error Tracking
- [ ] Sentry / Bugsnag installed
- [ ] Error alerts configured
- [ ] Stack traces captured

**Install Sentry:**
```bash
composer require sentry/sentry-laravel
```

### ✅ 18. Access Logs Review
- [ ] Review logs weekly
- [ ] Check for suspicious IPs
- [ ] Alert on unusual patterns

**Check suspicious:**
```bash
# Failed logins
grep "LOGIN_FAILED" storage/logs/laravel.log

# Unusual IPs
awk '{print $1}' /var/log/nginx/access.log | sort | uniq -c | sort -nr
```

### ✅ 19. Performance Monitoring
- [ ] Page load time monitored
- [ ] Database query optimization
- [ ] Memory usage monitored

**Tools:**
- Laravel Telescope (development)
- New Relic (production)
- Laravel Debugbar

### ✅ 20. Vulnerability Scanning
- [ ] Regular security audit
- [ ] Dependencies vulnerability check
- [ ] Penetration testing (optional)

**Command:**
```bash
# Check dependencies
composer audit

# Check for known vulnerabilities
php artisan route:list | grep -i "without" # Check routes without auth
```

---

## 📊 Security Score

Hitung score Anda:
- Basic Security (1-5): **5 points each** = 25 points
- Advanced Security (6-10): **3 points each** = 15 points
- VPS Security (11-15): **2 points each** = 10 points (jika pakai VPS)
- Monitoring (16-20): **2 points each** = 10 points

**Total maksimal: 60 points**

### Score Interpretation:
- **50-60**: Excellent! Sangat aman ✅
- **40-49**: Good! Aman untuk production ⚠️
- **30-39**: Fair! Perlu improvement 🔶
- **Below 30**: Poor! Tidak recommended untuk production ❌

---

## 🚨 Incident Response Plan

### Jika Terjadi Security Breach:

#### 1. Immediate Actions (0-1 jam)
- [ ] Matikan aplikasi immediately
- [ ] Disconnect dari internet
- [ ] Backup data current state
- [ ] Dokumentasikan apa yang terjadi

#### 2. Investigation (1-4 jam)
- [ ] Check logs untuk entry point
- [ ] Identify compromised accounts
- [ ] List affected data
- [ ] Capture evidence (screenshots, logs)

#### 3. Containment (4-8 jam)
- [ ] Change ALL passwords
- [ ] Revoke ALL API tokens
- [ ] Block suspicious IPs
- [ ] Patch vulnerability

#### 4. Recovery (8-24 jam)
- [ ] Restore from clean backup
- [ ] Apply security patches
- [ ] Test application thoroughly
- [ ] Gradual rollout

#### 5. Post-Incident (1-7 hari)
- [ ] Root cause analysis
- [ ] Update security procedures
- [ ] Train staff on new procedures
- [ ] Monitor closely for re-attack

### Emergency Contacts:
```
IT Admin: [Phone Number]
Owner: [Phone Number]
Security Expert: [Konsultan jika ada]
Hosting Support: [Jika pakai VPS]
```

---

## 📝 Security Audit Log

### Monthly Security Checklist:
```
[ ] Check for failed login attempts
[ ] Review user access logs
[ ] Verify backup integrity
[ ] Test backup restore
[ ] Update dependencies
[ ] Review firewall rules
[ ] Check for suspicious activity
[ ] Verify SSL certificate expiry
[ ] Review error logs
[ ] Test disaster recovery plan

Last Audit: [Date]
Next Audit: [Date]
Audited By: [Name]
```

---

## 🎓 Security Training

### Untuk Owner:
- [ ] Cara detect phishing
- [ ] Strong password practices
- [ ] 2FA usage
- [ ] Incident response

### Untuk Admin:
- [ ] Security best practices
- [ ] Log monitoring
- [ ] Backup & restore
- [ ] Update procedures

### Untuk Kasir/Staff:
- [ ] Password security
- [ ] Logout after use
- [ ] Report suspicious activity
- [ ] No sharing credentials

---

## 📞 Resources & Help

### Security Tools:
- **SSL Test**: https://www.ssllabs.com/ssltest/
- **Security Headers**: https://securityheaders.com/
- **Penetration Test**: https://pentest-tools.com/

### Documentation:
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- Laravel Security: https://laravel.com/docs/security
- Cloudflare Security: https://www.cloudflare.com/learning/security/

### Communities:
- Laravel Security: https://laravelsecurity.com/
- PHP Security: https://www.php.net/manual/en/security.php

---

## ✅ Pre-Production Checklist

Sebelum go-live ke internet, pastikan:

### Environment
- [ ] `.env` APP_DEBUG=false
- [ ] `.env` APP_ENV=production
- [ ] `.env` LOG_LEVEL=error

### Security
- [ ] All passwords changed
- [ ] HTTPS enabled
- [ ] Firewall configured
- [ ] Rate limiting active

### Performance
- [ ] Cache enabled
- [ ] Database optimized
- [ ] CDN configured (optional)

### Backup
- [ ] Auto backup configured
- [ ] Cloud backup enabled
- [ ] Restore tested

### Monitoring
- [ ] Uptime monitor active
- [ ] Error tracking enabled
- [ ] Alerts configured

### Documentation
- [ ] Credentials documented (secure location)
- [ ] Incident response plan ready
- [ ] Emergency contacts listed

---

**REMEMBER:** Keamanan adalah proses berkelanjutan, bukan satu kali setup. Review dan update security measures secara berkala!

---

**Toko Obat Ro Tua**  
*Security Checklist - Januari 2026*
