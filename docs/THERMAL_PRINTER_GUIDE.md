# Panduan Print Struk Thermal 58mm

## Overview
Aplikasi sekarang mendukung 2 mode print:
1. **HTML Print** - Print via browser (Chrome/Firefox)
2. **ESC/POS Raw** - File thermal khusus untuk printer thermal 58mm

---

## 🖥️ SETUP DI LAPTOP CLIENT (WAJIB BACA!)

### ⚙️ Langkah 1: Install Driver Printer Thermal

#### A. Printer USB (Paling Umum)
1. Colokkan printer thermal ke USB laptop
2. Windows akan otomatis detect
3. Install driver dari CD yang disertakan, atau download dari:
   - **Xprinter**: https://www.xprinter.net/download
   - **Epson TM**: https://epson.com/support
   - **Rongta**: https://www.rongtatech.com/download
   - **Generic/China**: Coba driver "USB Printing Support" dari Windows Update

4. Setelah install, cek di **Devices and Printers**:
   - Tekan `Win + R` → ketik `control printers`
   - Pastikan printer muncul (misal: "XP-58", "Thermal Printer")

#### B. Printer Bluetooth
1. Nyalakan printer, aktifkan pairing mode
2. Windows Settings → Bluetooth & devices → Add device
3. Pilih printer, pair (PIN biasanya: 0000 atau 1234)
4. Install driver jika diminta
5. Tambahkan sebagai printer di Windows

#### C. Printer Serial/COM Port
1. Colokkan kabel serial atau USB-to-Serial adapter
2. Install driver adapter (jika pakai USB-to-Serial)
3. Cek port di Device Manager → Ports (COM & LPT)
4. Catat nomor COM port (misal: COM3)

---

### 🔍 Langkah 2: Cek Port Printer

**PENTING:** Anda harus tahu port printer untuk bisa print ESC/POS raw!

#### Cara 1: Via Device Manager
1. Tekan `Win + X` → pilih **Device Manager**
2. Expand **Ports (COM & LPT)**
3. Lihat port printer, contoh:
   - `USB Serial Port (COM3)`
   - `Prolific USB-to-Serial (COM5)`

#### Cara 2: Via Devices and Printers
1. Tekan `Win + R` → ketik `control printers` → Enter
2. Klik kanan printer thermal → **Printer properties**
3. Tab **Ports** → lihat yang ter-checklist:
   - `COM1`, `COM3`, `USB001`, `LPT1`, dll

#### Cara 3: Via Command Line
```cmd
mode
```
Akan tampil semua COM ports yang tersedia.

---

### 🧪 Langkah 3: Test Print Manual

Sebelum pakai aplikasi, test print dulu secara manual:

```cmd
# 1. Buat file test sederhana
echo Test Print Thermal > test.txt

# 2. Kirim ke printer (ganti COM3 sesuai port Anda)
copy /b test.txt \\.\COM3

# Atau jika USB:
copy /b test.txt \\.\USB001

# Atau jika LPT (paralel):
copy /b test.txt LPT1
```

**Hasil yang diharapkan:**
- ✅ Printer bunyi beep
- ✅ Kertas keluar dengan tulisan "Test Print Thermal"
- ✅ Auto cut (jika printer support)

**Jika gagal:**
- ❌ Cek port benar (COM3, COM1, USB001?)
- ❌ Pastikan printer nyala dan ada kertas
- ❌ Coba port lain jika tidak yakin

---

### 📥 Langkah 4: Setup Auto-Print (Opsional)

Agar file `.txt` dari aplikasi langsung print tanpa manual command.

#### Opsi A: Buat Script Auto-Print

1. **Buat file `print_thermal.bat`** di folder Downloads:
   ```batch
   @echo off
   REM Ganti COM3 dengan port printer Anda
   copy /b "%~1" \\.\COM3
   
   REM Hapus file setelah print (opsional)
   timeout /t 2 /nobreak >nul
   del "%~1"
   
   exit
   ```

2. **Set file `.txt` otomatis buka dengan script ini:**
   - Klik kanan file `.txt` → **Open with** → **Choose another app**
   - **More apps** → scroll bawah → **Look for another app on this PC**
   - Pilih `print_thermal.bat`
   - Checklist **Always use this app**

3. **Test:** Download struk dari aplikasi, akan langsung print!

#### Opsi B: Gunakan Software Print Manager

Download salah satu tools ini (GRATIS):

1. **RawPrint** (Windows)
   - Download: https://sourceforge.net/projects/rawprint/
   - Bisa monitor folder, auto-print file baru

2. **PrintNode** (Trial 14 hari)
   - https://www.printnode.com/
   - Support auto-print via web

3. **RawBT** (Android - jika printer Bluetooth)
   - Download dari Play Store
   - Transfer file dari PC ke Android via share

---

### 🎯 Langkah 5: Print dari Aplikasi

Sekarang siap digunakan:

1. **Login ke aplikasi**: http://localhost:8000
2. **Buka Penjualan** → pilih transaksi
3. **Klik tombol "Print Thermal 58mm"**
4. File `.txt` akan terdownload ke folder Downloads
5. **Print file:**

   **Jika sudah setup auto-print (Langkah 4):**
   - Double-click file → langsung print ✅

   **Jika manual:**
   ```cmd
   cd C:\Users\YourName\Downloads
   copy /b struk-POS-20260108120000-123.txt \\.\COM3
   ```

---

### ✅ Checklist Verifikasi Client

Pastikan semua ini sudah OK:

- [ ] Driver printer thermal sudah terinstall
- [ ] Printer muncul di "Devices and Printers"
- [ ] Test print manual berhasil
- [ ] Sudah tahu port printer (COM3/USB001/dll)
- [ ] Script auto-print sudah dibuat (opsional)
- [ ] Browser bisa download file .txt
- [ ] File .txt bisa di-print via command line

---

## Mode 1: HTML Print (Browser)
### Kapan Digunakan:
- Print ke printer biasa (A4/A5)
- Testing/preview di layar
- Tidak punya printer thermal

### Cara Pakai:
1. Klik tombol **"Cetak HTML"** di halaman detail penjualan
2. Akan buka tab baru
3. Tekan Ctrl+P atau klik tombol "Cetak Struk"
4. Pilih printer
5. Print

## Mode 2: ESC/POS Thermal (Recommended untuk Thermal Printer)
### Kapan Digunakan:
- Printer thermal 58mm
- Printer POS thermal
- Ingin hasil optimal dan cepat

### Cara Pakai:

#### A. Print Langsung (Windows)
1. Klik tombol **"Print Thermal 58mm"**
2. File `.txt` akan terdownload
3. Buka Command Prompt
4. Print dengan command:
   ```cmd
   copy /b "C:\path\to\struk-POS-xxx.txt" \\.\COM1
   ```
   atau jika USB:
   ```cmd
   copy /b "C:\path\to\struk-POS-xxx.txt" \\.\USB001
   ```

#### B. Print via RawBT (Android Bluetooth Printer)
1. Klik tombol **"Print Thermal 58mm"**
2. Share file ke Android via Bluetooth/Email/USB
3. Buka app RawBT
4. Load file
5. Print

#### C. Auto Print (Advanced)
Buat file batch `print_thermal.bat`:
```batch
@echo off
copy /b "%~1" \\.\COM1
del "%~1"
```

Set di browser agar file `.txt` otomatis buka dengan script ini.

## Cek Port Printer
### Windows:
1. Buka Device Manager
2. Cari "Ports (COM & LPT)"
3. Lihat port printer (COM1, COM3, USB001, dll)

### Test Print Manual:
```cmd
echo Test Print > test.txt
copy /b test.txt \\.\COM1
```

## Troubleshooting

### Print terpotong/tidak lengkap
✅ Pastikan kertas thermal masih cukup
✅ Pastikan port printer benar
✅ Coba restart printer

### Karakter aneh/kotak-kotak
✅ Pastikan encoding file UTF-8
✅ Pastikan printer support ESC/POS
✅ Update firmware printer

### File tidak bisa print
✅ Cek printer connected
✅ Cek port benar (COM1/USB001)
✅ Coba print test page printer

## Format ESC/POS Commands

File thermal menggunakan ESC/POS standard commands:
- `ESC @` - Initialize printer
- `ESC a 1` - Center align
- `ESC E 1` - Bold ON
- `GS V 1` - Partial cut

Kompatibel dengan printer:
- Epson TM-T58
- Xprinter XP-58
- Rongta RPP02
- Generic 58mm thermal printers

## Tips & Tricks

### Kertas 58mm
- Lebar efektif: 32 karakter
- Margin kiri/kanan: 2mm
- Font: Courier New monospace

### Best Practices
1. Test printer sebelum transaksi ramai
2. Siapkan stok kertas thermal
3. Backup dengan print HTML jika thermal error
4. Setting default printer di Windows untuk auto-print

### Auto-Print Setup (Chrome)
1. Chrome Settings > Advanced > Downloads
2. Set download location
3. Set untuk auto-open `.txt` files
4. File akan langsung ke printer
