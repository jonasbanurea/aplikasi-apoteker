# Troubleshooting Guide - Toko Obat Ro Tua

## Error: Maximum Execution Time Exceeded

### Gejala:
```
Symfony\Component\ErrorHandler\Error\FatalError
Maximum execution time of 60 seconds exceeded
```

### Penyebab:
- Terjadi saat import data produk dalam jumlah besar dari Excel
- Script membutuhkan waktu lebih dari 60 detik (batas default PHP)

### Solusi yang Sudah Diterapkan:

✅ **Update ProductFromExcelSeeder.php**
- Waktu eksekusi ditingkatkan menjadi 300 detik (5 menit)
- Memory limit ditingkatkan menjadi 512MB

### Jika Masih Terjadi Error:

#### Opsi 1: Update php.ini (Permanen)
1. Cari file `php.ini` (biasanya di `C:\xampp\php\php.ini` atau `C:\laragon\bin\php\php8.x\php.ini`)
2. Edit nilai berikut:
   ```ini
   max_execution_time = 300
   memory_limit = 512M
   ```
3. Restart Apache/Server

#### Opsi 2: Jalankan via Command Line
```bash
# Dari folder aplikasi
php artisan db:seed --class=ProductFromExcelSeeder
```

Command line biasanya tidak memiliki batasan waktu eksekusi yang ketat.

#### Opsi 3: Import Bertahap
Jika file Excel terlalu besar:
1. Pisahkan data ke beberapa file Excel lebih kecil
2. Import satu per satu

### Tips Mencegah Error:
- Pastikan file Excel tidak terlalu besar (< 5000 baris per sheet)
- Tutup aplikasi lain saat import data
- Gunakan command line untuk import data besar
- Pastikan koneksi database stabil

### Informasi Tambahan:
- **Maksimal waktu default**: 60 detik
- **Waktu setelah update**: 300 detik (5 menit)
- **Memory setelah update**: 512MB

---

## Error Lainnya

### Koneksi Database Gagal
**Gejala**: `SQLSTATE[HY000] [2002] Connection refused`

**Solusi**:
1. Pastikan MySQL/MariaDB sudah berjalan
2. Cek konfigurasi di `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=apotek_rotua
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Class Not Found
**Gejala**: `Class "Spatie\Permission\..." not found`

**Solusi**:
```bash
composer install
php artisan optimize:clear
```

### Storage Link Missing
**Gejala**: Gambar/file tidak muncul

**Solusi**:
```bash
php artisan storage:link
```

---

## Kontak Support
Jika masih mengalami masalah, dokumentasikan:
1. Screenshot error lengkap
2. File `.env` (sensor password)
3. Versi PHP (`php -v`)
4. Langkah yang sudah dicoba
