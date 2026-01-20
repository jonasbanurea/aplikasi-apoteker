# Panduan Format PDF Profesional - Toko Obat Ro Tua

## 📋 Ringkasan Perubahan

Laporan PDF dan struk thermal telah dipercantik dengan desain profesional yang mencakup:
- ✅ Logo toko
- ✅ Nama toko dan alamat lengkap
- ✅ Header dan footer yang konsisten
- ✅ Styling modern dan mudah dibaca

## 🏢 Informasi Toko

**Nama:** Toko Obat Ro Tua  
**Alamat:** Jl. Saribu Dolok, Pematang Pane, Kec. Panombean Panei, Kabupaten Simalungun, Sumatera Utara  
**Lokasi Logo:** `C:\Aplikasi-apoteker\logo.png`

## 📄 File yang Telah Diperbarui

### 1. Laporan PDF Apotek (`resources/views/reports/pdf.blade.php`)

**Fitur Baru:**
- Header profesional dengan logo dan informasi toko
- Desain gradient pada judul dan header tabel
- Summary box dengan highlight untuk data penting
- Footer dengan informasi toko
- Warna tema: Navy Blue (#2c3e50, #34495e) dan Sky Blue (#3498db)

**Tampilan:**
```
┌─────────────────────────────────────────┐
│ [LOGO]  TOKO OBAT RO TUA                │
│         Alamat lengkap toko...          │
├─────────────────────────────────────────┤
│        LAPORAN APOTEK                   │
├─────────────────────────────────────────┤
│ Periode: DD MMM YYYY - DD MMM YYYY      │
│ Tanggal Cetak: DD MMM YYYY HH:MM WIB    │
└─────────────────────────────────────────┘
```

### 2. Struk Thermal 58mm (`resources/views/sales/print.blade.php`)

**Fitur Baru:**
- Logo toko di bagian atas (responsive untuk thermal)
- Nama toko dan alamat yang jelas
- Double-line separator untuk section penting
- Footer dengan ucapan "Semoga Lekas Sembuh"
- Informasi kebijakan toko

**Tampilan:**
```
═══════════════════════════════════
        [LOGO TOKO]
   TOKO OBAT RO TUA
   Alamat lengkap...
═══════════════════════════════════
Invoice: INV-XXXX
Kasir: Nama Kasir
DD/MM/YYYY HH:MM
- - - - - - - - - - - - - - - - - 
Item 1
1 x Rp 10.000      Rp 10.000
- - - - - - - - - - - - - - - - - 
TOTAL              Rp 10.000
Bayar (CASH)       Rp 20.000
Kembali            Rp 10.000
═══════════════════════════════════
      TERIMA KASIH
   Semoga Lekas Sembuh
Barang yang sudah dibeli
tidak dapat ditukar/dikembalikan
```

## 🎨 Desain & Styling

### Color Palette
- **Primary:** #2c3e50 (Navy Blue)
- **Secondary:** #34495e (Dark Gray Blue)
- **Accent:** #3498db (Sky Blue)
- **Background:** #f8f9fa (Light Gray)
- **Text:** #333333 (Dark Gray)

### Typography
- **PDF Report:** DejaVu Sans (11-20px)
- **Thermal Receipt:** Courier New (8-13px)

### Layout Features
- Gradient backgrounds untuk header
- Box shadow pada tabel
- Hover effects pada baris tabel (untuk tampilan digital)
- Zebra striping (baris genap dengan background berbeda)
- Responsive spacing dan padding

## 📁 Lokasi Logo

Logo harus ditempatkan di:
```
C:\Aplikasi-apoteker\logo.png
```

**Rekomendasi Logo:**
- Format: PNG dengan background transparan
- Ukuran minimal: 300x300 px
- Ratio: Square atau landscape
- Untuk struk thermal: max height 15mm, width 35mm
- Untuk PDF report: 70x70 px

## 🔧 Cara Menggunakan

### Generate Laporan PDF
1. Login sebagai Owner atau Admin Gudang
2. Buka menu **Laporan**
3. Pilih filter periode yang diinginkan
4. Klik tombol **Generate PDF**
5. PDF akan otomatis terdownload dengan format profesional baru

### Print Struk Thermal
1. Setelah transaksi selesai
2. Pada halaman detail transaksi, klik **Print Thermal 58mm**
3. Akan muncul preview struk dengan desain baru
4. Klik **Cetak Struk** atau Ctrl+P
5. Pilih thermal printer 58mm Anda
6. Print!

## 🎯 Keuntungan Desain Baru

✅ **Profesional** - Meningkatkan brand image apotek  
✅ **Informatif** - Informasi lengkap dan mudah dibaca  
✅ **Konsisten** - Semua dokumen memiliki identitas visual yang sama  
✅ **Legal** - Mencantumkan alamat lengkap untuk keperluan administrasi  
✅ **User-Friendly** - Layout yang rapi dan mudah dipahami  

## 🔄 Maintenance

### Mengganti Logo
1. Siapkan file logo baru (PNG, 300x300px min)
2. Copy ke `C:\Aplikasi-apoteker\logo.png`
3. Restart aplikasi (opsional)
4. Logo akan otomatis muncul di semua PDF dan struk

### Mengubah Informasi Toko
Edit file berikut:
- **PDF Report:** `resources/views/reports/pdf.blade.php`
- **Thermal Receipt:** `resources/views/sales/print.blade.php`

Cari section dengan text "TOKO OBAT RO TUA" dan ubah sesuai kebutuhan.

## 📞 Support

Jika ada kendala atau ingin customisasi lebih lanjut:
- Hubungi developer
- Dokumentasi lengkap: `docs/` directory
- Backup file original sudah tersimpan di Git history

---

**Terakhir Update:** 20 Januari 2026  
**Versi:** 1.0  
**Status:** ✅ Production Ready
