# KARTU BANTUAN CEPAT - BACKUP DATABASE
## Toko Obat Ro Tua

---

## ❌ JIKA BACKUP GAGAL

### Error: "Can't create TCP/IP socket (10106)"

**Penyebab:** MySQL tidak berjalan

**Solusi Cepat (3 Langkah):**

1. **Buka XAMPP/Laragon Control Panel**
   - XAMPP: Icon di taskbar → XAMPP Control Panel
   - Laragon: Icon di taskbar → Show

2. **Cek Baris MySQL**
   ```
   ┌────────────────────────────┐
   │ MySQL  │      │  [Start]  │  ← Jika ada tombol Start
   └────────────────────────────┘
   ATAU
   ┌────────────────────────────┐
   │ MySQL  │ 1234 │  [Stop]   │  ← Jika ada tombol Stop (sudah jalan)
   └────────────────────────────┘
   ```

3. **Klik Start (jika belum jalan)**
   - Tunggu sampai status jadi HIJAU
   - Atau ada angka di kolom tengah
   - Coba backup lagi di aplikasi

---

## 🔍 TEST MYSQL BERJALAN

### Cara 1: Buka phpMyAdmin
1. Buka browser
2. Ketik: `http://localhost/phpmyadmin`
3. ✅ Berhasil = MySQL OK
4. ❌ Error = MySQL masih mati

### Cara 2: Jalankan Script
1. Double-click file: `cek_mysql_status.bat`
2. Lihat hasilnya
3. Ikuti instruksi yang muncul

---

## 💾 BACKUP MANUAL (ALTERNATIF)

### Via phpMyAdmin (Paling Mudah)

1. Buka: `http://localhost/phpmyadmin`
2. Klik database `apotek_rotua` (sidebar kiri)
3. Klik tab **"Export"** (atas)
4. Pilih:
   - Export method: **Quick**
   - Format: **SQL**
5. Klik **"Go"**
6. File `.sql` otomatis terdownload
7. Simpan di folder aman

**Lokasi simpan yang baik:**
- `C:\Users\[Nama]\Documents\Backup-Database\`
- USB/Hardisk eksternal
- Google Drive / OneDrive

---

## 📋 CHECKLIST SEBELUM BACKUP

- [ ] XAMPP/Laragon terbuka
- [ ] MySQL status **Running** (hijau)
- [ ] phpMyAdmin bisa dibuka
- [ ] Aplikasi bisa login
- [ ] Space hardisk > 500 MB

---

## 🆘 HUBUNGI IT SUPPORT

**Jika sudah coba semua cara tapi tetap gagal:**

Siapkan info ini:
1. Screenshot error dari aplikasi
2. Screenshot XAMPP/Laragon Control Panel
3. Hasil dari `cek_mysql_status.bat`

Hubungi:
- Email: [email IT support]
- Phone: [nomor IT support]
- WhatsApp: [nomor WA support]

---

## 🎯 TIPS PENCEGAHAN

✅ Selalu start XAMPP/Laragon saat buka aplikasi
✅ Backup minimal 1x seminggu
✅ Simpan backup di 3 tempat berbeda
✅ Test restore backup sekali-kali

---

**Cetak halaman ini dan tempel di meja kasir** 📌

Versi: 1.0 | Update: 8 Januari 2026
