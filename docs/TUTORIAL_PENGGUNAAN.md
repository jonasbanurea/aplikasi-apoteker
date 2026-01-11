# Tutorial Penggunaan Aplikasi Toko Obat Ro Tua

Panduan lengkap menggunakan aplikasi apotek dari awal sampai transaksi penjualan.

---

## 📋 Daftar Isi
1. [Login & Dashboard](#1-login--dashboard)
2. [Tambah Supplier](#2-tambah-supplier)
3. [Tambah Produk](#3-tambah-produk)
4. [Penerimaan Barang](#4-penerimaan-barang)
5. [Penjualan (POS)](#5-penjualan-pos)
6. [Menu Stok](#6-menu-stok)
7. [Laporan](#7-laporan)
8. [Absensi Kasir](#8-absensi-kasir)
9. [Management User](#9-management-user)

---

## 1. Login & Dashboard

### Login
1. Buka browser, akses: `http://localhost:8000`
2. Masukkan email dan password:
   - **Owner**: owner@rotua.test / password
   - **Kasir**: kasir@rotua.test / password
   - **Admin Gudang**: gudang@rotua.test / password
3. Klik **Login**

### Dashboard
Setelah login, Anda akan melihat:

#### 📊 Statistik (Owner/Admin Gudang)
- **Total Produk**: Jumlah item di master data
- **Total Supplier**: Jumlah supplier terdaftar
- **Nilai Stok**: Total nilai inventory saat ini
- **Penjualan Hari Ini**: Omzet hari ini
- **Produk Stok Menipis**: Alert produk yang perlu restok

#### 💰 Kasir Dashboard
- **Total Penjualan Hari Ini**: Omzet shift kasir
- **Jumlah Transaksi**: Berapa transaksi yang sudah diproses
- **Metode Pembayaran**: Breakdown CASH vs NON-CASH
- **Quick Actions**: Tombol cepat ke POS dan daftar transaksi

---

## 2. Tambah Supplier

### Langkah-langkah:
1. Klik menu **Master Data** → **Supplier**
2. Klik tombol **Tambah Supplier** (hijau)
3. Isi form:
   - **Nama**: Nama perusahaan supplier (contoh: PT Kimia Farma)
   - **Kontak**: Nama contact person
   - **Telepon**: Nomor telepon supplier
   - **Alamat**: Alamat lengkap (opsional)
   - **Term Pembayaran**: Berapa hari jatuh tempo (contoh: 30 hari)
4. Klik **Simpan**

### Contoh Data:
```
Nama: PT Kimia Farma
Kontak: Ibu Sarah
Telepon: 021-12345678
Term Pembayaran: 30 hari
```

---

## 3. Tambah Produk

### Langkah-langkah:
1. Klik menu **Master Data** → **Produk**
2. Klik tombol **Tambah Produk**
3. Isi **Data Produk**:

#### A. Informasi Dasar
- **SKU**: Kode unik produk (contoh: `OBT-0001`, `SRP-0001`)
- **Nama Dagang**: Nama brand obat (contoh: Sangobion)
- **Nama Generik**: Nama zat aktif (contoh: Ferrous Gluconate)
- **Bentuk**: Jenis sediaan (contoh: Kapsul, Tablet, Sirup)
- **Kekuatan Dosis**: Dosis obat (contoh: 500mg, 10ml)
- **Satuan**: Unit dasar (contoh: Strip, Botol, Tube)

#### B. Regulasi
- **Golongan**: Pilih kategori
  - OTC (bebas)
  - BEBAS_TERBATAS (logo peringatan)
  - RESEP (butuh resep dokter)
  - PSIKOTROPIKA
  - NARKOTIKA
- **Wajib Resep**: Centang jika harus ada resep

#### C. Harga
- **Harga Beli**: Modal per unit (dari supplier)
- **Harga Jual**: Harga ke customer

#### D. Stok & Lokasi
- **Lokasi Rak**: Posisi di rak (contoh: A1-03)
- **Minimal Stok**: Alert jika stok kurang dari nilai ini
- **Konsinyasi**: Centang jika barang titipan (tidak bayar dulu)

#### E. Penjualan Eceran (Opsional)
Jika produk bisa dijual per kapsul/tablet:
1. Centang **Jual Eceran**
2. Isi:
   - **Unit Kemasan**: STRIP/BOX/BOTOL
   - **Unit Terkecil**: KAPSUL/TABLET/ML
   - **Isi Per Kemasan**: Jumlah konversi (contoh: 10)

**Contoh Sangobion Strip:**
```
Unit Kemasan: STRIP
Unit Terkecil: KAPSUL
Isi Per Kemasan: 10
Harga Jual: Rp 26.000 (per strip)
→ Harga eceran otomatis: Rp 2.600/kapsul
```

4. Klik **Simpan**

---

## 4. Penerimaan Barang

Menu untuk mencatat barang masuk dari supplier.

### Langkah-langkah:
1. Klik menu **Transaksi** → **Penerimaan Barang**
2. Klik tombol **Penerimaan Baru**
3. Isi **Header**:
   - **Supplier**: Pilih dari dropdown
   - **No. Invoice**: Nomor faktur dari supplier
   - **Tanggal**: Tanggal terima barang
   - **Diskon**: Diskon total (jika ada)
   - **Konsinyasi**: Centang jika barang konsinyasi

4. Tambah **Detail Barang**:
   - Klik **Tambah Baris**
   - **Produk**: Pilih obat yang diterima
   - **Batch**: Nomor batch dari kemasan
   - **Expired**: Tanggal kadaluarsa
   - **Qty**: Jumlah diterima
   - **Bonus**: Qty bonus (jika ada)
   - **Harga Beli**: Harga satuan

5. Klik **Simpan Penerimaan**

### Contoh:
```
Supplier: PT Kimia Farma
No. Invoice: INV-2026-001
Tanggal: 11 Januari 2026

Detail:
- Sangobion Strip
  Batch: 2026A01
  Expired: 31-12-2027
  Qty: 50 strip
  Bonus: 5 strip
  Harga: Rp 20.000/strip
```

### Yang Terjadi Otomatis:
- ✅ Stok bertambah (50 + 5 = 55 strip)
- ✅ Batch tracking tersimpan
- ✅ Hutang tercatat (jika bukan konsinyasi)
- ✅ Stock movement terekam

### Edit Penerimaan:
- Klik tombol **Edit** di daftar penerimaan
- Sistem otomatis rollback stok lama, lalu input data baru
- Semua perubahan tercatat di stock movement

---

## 5. Penjualan (POS)

Menu kasir untuk transaksi penjualan.

### Langkah-langkah:
1. Klik menu **POS Penjualan** atau **Transaksi** → **Penjualan**
2. Pilih **Metode Pembayaran**: CASH atau NON CASH

### A. Tambah Item:

#### Cara 1: Manual
1. Klik **Baris Baru**
2. Pilih **Produk** dari dropdown
3. Pilih **Unit**:
   - Kemasan (harga normal)
   - Eceran (harga per kapsul/tablet) *jika tersedia*
4. Isi **Qty**
5. **Harga** otomatis muncul
6. **Diskon/Unit**: Isi jika ada diskon

#### Cara 2: Search
1. Ketik SKU atau nama di kolom **Cari Produk**
2. Klik **Tambah**
3. Item otomatis masuk ke tabel

### B. Contoh Transaksi:

**Skenario: Pasien beli Sangobion**

**Pilihan 1 - Beli per strip:**
```
Produk: Sangobion
Unit: STRIP
Qty: 2
Harga: Rp 26.000
Subtotal: Rp 52.000
```

**Pilihan 2 - Beli eceran:**
```
Produk: Sangobion
Unit: KAPSUL
Qty: 5
Harga: Rp 2.600
Subtotal: Rp 13.000
```

### C. Pembayaran:
1. **Total** otomatis dihitung
2. **Diskon Total**: Isi jika ada (opsional)
3. **Metode CASH**:
   - Isi **Bayar**: Jumlah uang dari customer
   - **Kembalian** otomatis dihitung
4. **Metode NON CASH**:
   - Tidak perlu isi bayar (auto sama dengan total)

### D. Data Resep (Opsional):
- **No. Resep**: Nomor resep dokter
- **Dokter**: Nama dokter
- **Catatan**: Catatan tambahan

### E. Proses:
1. Klik **Proses Pembayaran**
2. Transaksi tersimpan
3. **Struk** otomatis muncul
4. Klik **Print** atau **Print Thermal** (jika ada printer)

### Yang Terjadi Otomatis:
- ✅ Stok berkurang (FIFO - First In First Out)
- ✅ Batch expired terlama keluar duluan
- ✅ Stock movement terekam
- ✅ Laporan penjualan terupdate

---

## 6. Menu Stok

Ada 4 menu untuk kelola stok:

### 6.1. Stock Batches

**Fungsi**: Lihat stok per batch/expired

**Kegunaan**:
- Cek berapa stok tersisa per batch
- Monitor tanggal expired
- Track harga beli per batch

**Cara Lihat**:
1. Klik **Stok** → **Stock Batches**
2. Filter per produk (jika perlu)
3. Lihat kolom:
   - **Batch No**: Nomor batch
   - **Expired**: Tanggal kadaluarsa
   - **Qty On Hand**: Sisa stok
   - **Cost Price**: Harga beli

**Kegunaan Praktis**:
- Identifikasi batch yang hampir expired
- Pastikan FIFO berjalan (batch lama keluar dulu)
- Cross-check dengan stok fisik

---

### 6.2. Stock Movements

**Fungsi**: History semua pergerakan stok (masuk/keluar)

**Jenis Movement**:
- **IN**: Penerimaan barang, adjustment naik
- **OUT**: Penjualan, retur, adjustment turun

**Cara Lihat**:
1. Klik **Stok** → **Stock Movements**
2. Filter berdasarkan:
   - Tanggal
   - Produk
   - Tipe (IN/OUT)
3. Lihat detail:
   - **Type**: IN atau OUT
   - **Qty**: Jumlah
   - **Ref Type**: Sumber transaksi (PURCHASE, SALE, ADJUSTMENT)
   - **User**: Siapa yang input
   - **Notes**: Catatan

**Kegunaan Praktis**:
- Audit trail stok
- Investigasi selisih stok
- Tracking siapa yang input/edit data

---

### 6.3. Stock Opname

**Fungsi**: Rekonsiliasi stok sistem vs stok fisik

**Kapan Digunakan**:
- Setiap akhir bulan
- Setelah stock taking fisik
- Jika ada selisih stok

**Cara Input**:
1. Klik **Stok** → **Stock Opname**
2. Klik **Opname Baru**
3. Isi:
   - **Tanggal**: Tanggal stock taking
   - **Catatan**: Periode atau keterangan
4. Tambah item:
   - **Produk**: Pilih produk
   - **Batch**: Pilih batch
   - **Stok Sistem**: Otomatis muncul
   - **Stok Fisik**: Isi hasil hitung fisik
   - **Selisih**: Otomatis dihitung
5. Klik **Simpan**

**Sistem Otomatis**:
- ✅ Adjustment stok sesuai selisih
- ✅ Stock movement tercatat
- ✅ Catatan opname tersimpan

**Contoh**:
```
Produk: Sangobion Strip
Batch: 2026A01
Stok Sistem: 50
Stok Fisik: 48
Selisih: -2 (kurang)
→ Sistem auto kurangi 2 dari stok
```

---

### 6.4. Stock Alerts / Stok Menipis

**Fungsi**: Alert produk yang stoknya di bawah minimal

**Cara Lihat**:
1. Klik **Stok** → **Stok Menipis** atau
2. Lihat di **Dashboard** bagian bawah

**Informasi**:
- Produk yang stoknya < minimal stok
- Rekomendasi order ulang

**Action**:
- Hubungi supplier
- Buat purchase order
- Tambah qty penerimaan

---

## 7. Laporan

### 7.1. Laporan Penjualan

**Cara Akses**:
1. Klik **Laporan** → **Penjualan**
2. Filter:
   - **Periode**: Pilih tanggal mulai - selesai
   - **Export Excel**: Download laporan

**Isi Laporan**:
- Tanggal transaksi
- No. invoice
- Total penjualan
- Metode pembayaran
- Kasir
- Detail item

---

### 7.2. Laporan Pembelian

**Cara Akses**:
1. Klik **Laporan** → **Pembelian**
2. Filter periode
3. Export Excel

**Isi Laporan**:
- Tanggal penerimaan
- Supplier
- No. invoice
- Total pembelian
- Status (Posted/Consignment)

---

### 7.3. Laporan Stok

**Cara Akses**:
1. Klik **Laporan** → **Stok**
2. Filter produk (jika perlu)
3. Export Excel

**Isi Laporan**:
- Nama produk
- Total stok (semua batch)
- Nilai stok (qty × harga beli)
- Detail per batch

---

### 7.4. Laporan Keuangan

**Fitur**:
- Laba/Rugi per periode
- Hutang supplier
- Omzet harian/bulanan
- Top selling products

---

## 8. Absensi Kasir

### Fungsi:
Kasir wajib buka shift sebelum transaksi, tutup shift setelah selesai.

### Cara Buka Shift:
1. Login sebagai **Kasir**
2. Jika belum ada shift aktif, klik **Buka Shift**
3. Isi **Saldo Awal** (uang cash di laci kasir)
4. Klik **Mulai Shift**

### Cara Tutup Shift:
1. Setelah selesai kerja, klik **Tutup Shift**
2. Isi:
   - **Saldo Akhir**: Total uang cash di laci
   - **Catatan**: Keterangan (opsional)
3. Sistem otomatis hitung:
   - Total penjualan shift
   - Selisih cash (seharusnya vs aktual)
4. Klik **Tutup Shift**

### Laporan Shift:
- Owner/Admin bisa lihat **Laporan** → **Shift Kasir**
- Rekap per kasir per hari
- Monitoring performa kasir

---

## 9. Management User

**Hak Akses**: Hanya **Owner**

### Melihat Daftar User:
1. Login sebagai **Owner**
2. Klik **Settings** → **Users**
3. Lihat daftar user dan rolenya

### Tambah User Baru:
1. Klik **Tambah User**
2. Isi:
   - **Nama**: Nama lengkap
   - **Email**: Email untuk login
   - **Password**: Password (min 8 karakter)
   - **Role**: Pilih role:
     - **owner**: Akses penuh
     - **kasir**: Hanya POS dan transaksi
     - **admin_gudang**: Kelola stok dan penerimaan
3. Klik **Simpan**

### Edit User:
1. Klik **Edit** pada user
2. Ubah data yang perlu
3. Klik **Simpan**

### Nonaktifkan User:
1. Klik **Edit**
2. Hilangkan centang **Active**
3. User tidak bisa login lagi

### Role & Hak Akses:

#### Owner
- ✅ Semua menu
- ✅ Laporan lengkap
- ✅ Management user
- ✅ Settings

#### Kasir
- ✅ POS Penjualan
- ✅ Lihat transaksi sendiri
- ✅ Absensi shift
- ❌ Edit master data
- ❌ Lihat laporan lengkap

#### Admin Gudang
- ✅ Kelola produk
- ✅ Penerimaan barang
- ✅ Stock opname
- ✅ Lihat laporan stok
- ❌ POS (tidak bisa jual)
- ❌ Management user

---

## 🎯 Alur Kerja Lengkap

### Setup Awal (Sekali):
1. ✅ Login sebagai Owner
2. ✅ Tambah Supplier
3. ✅ Tambah Produk (lengkapi master data)
4. ✅ Tambah User (Kasir & Admin Gudang)

### Penerimaan Barang:
1. ✅ Admin Gudang terima barang dari supplier
2. ✅ Input Penerimaan Barang (lengkap dengan batch & expired)
3. ✅ Stok otomatis bertambah

### Transaksi Harian Kasir:
1. ✅ Kasir login
2. ✅ Buka Shift (isi saldo awal)
3. ✅ Proses transaksi penjualan di POS
4. ✅ Tutup Shift (isi saldo akhir)
5. ✅ Logout

### Monitoring (Owner):
1. ✅ Cek Dashboard setiap hari
2. ✅ Review laporan penjualan
3. ✅ Monitor stok menipis
4. ✅ Stock opname rutin (akhir bulan)

---

## 📞 Tips Penggunaan

### ✅ DO (Lakukan):
- Selalu input batch & expired saat penerimaan
- Tutup shift kasir setiap selesai kerja
- Stock opname rutin minimal 1 bulan sekali
- Backup database secara berkala

### ❌ DON'T (Jangan):
- Edit transaksi yang sudah final
- Hapus data transaksi (gunakan void/retur)
- Share password antar user
- Skip input batch number

---

## 🆘 Butuh Bantuan?

- 📖 Dokumentasi Lengkap: `docs/user-guide.md`
- 🔧 Troubleshooting: `docs/TROUBLESHOOTING.md`
- 💊 Panduan Eceran: `docs/PANDUAN_PENJUALAN_ECERAN.md`

---

**Selamat menggunakan aplikasi Toko Obat Ro Tua!** 🎉

*Update: Januari 2026*
