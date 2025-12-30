# Panduan Pengguna Toko Obat Rotua

## Daftar Isi
- [Pendahuluan](#pendahuluan)
- [Login & Logout](#login--logout)
- [Dashboard](#dashboard)
- [Master Data](#master-data)
- [Shift Kasir](#shift-kasir)
- [Penjualan (POS)](#penjualan-pos)
- [Pembelian/Penerimaan Barang](#pembelianpenerimaan-barang)
- [Manajemen Stok](#manajemen-stok)
- [Laporan](#laporan)
- [Manajemen User (Owner)](#manajemen-user-owner)
- [Ganti Password](#ganti-password)

---

## Pendahuluan

**Toko Obat Rotua** adalah sistem informasi manajemen apotek yang mengelola penjualan, pembelian, stok, dan laporan dengan pembagian peran:

- **Owner**: Akses penuh (master data, laporan, manajemen user, persetujuan opname).
- **Kasir**: Transaksi penjualan, shift, laporan terbatas.
- **Admin Gudang**: Penerimaan barang, stok, opname, retur supplier.

---

## Login & Logout

### Login
1. Buka aplikasi di browser (misal: `http://localhost:8000`).
2. Masukkan **Email** dan **Password**.
3. Klik **Login**.
4. Anda akan diarahkan ke dashboard sesuai role.

**Catatan**: User yang dinonaktifkan tidak bisa login.

### Logout
- Klik menu **Logout** di sidebar kiri bawah.
- Sesi akan berakhir dan kembali ke halaman login.

---

## Dashboard

Setelah login, Anda akan melihat dashboard dengan:
- **Ringkasan**: Total penjualan, transaksi, produk, stok.
- **Grafik Penjualan**: Per periode (harian/mingguan/bulanan).
- **Top Produk**: Produk terlaris.
- **Produk Hampir Habis**: Alert reorder point.
- **Produk Kadaluarsa/Hampir Kadaluarsa**: Alert expired.
- **Hutang Jatuh Tempo**: Hutang ke supplier (owner/admin gudang).

Dashboard disesuaikan per role.

---

## Master Data

### Supplier
**Akses**: Owner, Admin Gudang, Kasir

**Menambah Supplier**:
1. Klik menu **Supplier** di sidebar.
2. Klik **Tambah Supplier**.
3. Isi: Nama, Alamat, Telepon, Email (opsional), Kontak Person (opsional).
4. Klik **Simpan**.

**Mengedit/Menghapus**:
- Klik **Edit** untuk mengubah data.
- Klik **Hapus** untuk menghapus (tidak bisa jika sudah ada transaksi terkait).

### Produk/Obat
**Akses**: Owner, Admin Gudang, Kasir

**Menambah Produk**:
1. Klik menu **Produk/Obat** di sidebar.
2. Klik **Tambah Produk**.
3. Isi:
   - **SKU**: Kode unik produk.
   - **Nama Dagang**: Nama obat.
   - **Nama Generik**: (opsional).
   - **Satuan**: strip, box, botol, dll.
   - **Golongan**: Obat Keras, Obat Bebas, dll.
   - **Harga Beli**: Harga modal.
   - **Harga Jual**: Harga eceran.
   - **Reorder Point**: Qty alert stok rendah.
   - **Reorder Qty**: Qty saran pesan ulang.
4. Klik **Simpan**.

**Mengedit/Menghapus**:
- Klik **Edit** untuk mengubah.
- Klik **Hapus** untuk menghapus (tidak bisa jika ada transaksi/batch).

---

## Shift Kasir

**Akses**: Kasir

Setiap kasir harus **buka shift** sebelum transaksi dan **tutup shift** di akhir sesi.

### Buka Shift
1. Klik menu **Shift** (jika ada route) atau langsung dari dashboard.
2. Klik **Buka Shift**.
3. Isi **Modal Awal** (uang tunai awal).
4. Klik **Simpan**.

### Tutup Shift
1. Klik **Tutup Shift** di daftar shift atau dashboard.
2. Isi **Uang Tunai Akhir** (hasil hitung fisik).
3. Sistem akan menghitung selisih dengan transaksi.
4. Klik **Simpan**.
5. Cetak **Laporan Z-Report** (PDF).

**Catatan**: Hanya bisa transaksi jika shift terbuka.

---

## Penjualan (POS)

**Akses**: Kasir, Admin Gudang, Owner

### Membuat Transaksi Penjualan
1. Klik **POS Penjualan** di sidebar atau **Transaksi** (kasir menu).
2. Pilih **Tanggal Penjualan** (default hari ini).
3. **Tambah Item**:
   - Pilih **Produk** dari dropdown.
   - Sistem akan tampilkan batch tersedia (FIFO).
   - Masukkan **Qty**, **Harga** (default dari master), **Diskon** (opsional).
   - Klik **+** atau enter untuk menambah baris.
4. Sistem akan hitung **Total** otomatis.
5. Pilih **Metode Pembayaran**: CASH, QRIS, DEBIT, CREDIT.
6. Masukkan **Jumlah Bayar**.
7. Sistem tampilkan **Kembali**.
8. Klik **Simpan Penjualan**.
9. Setelah tersimpan, klik **Cetak Struk** untuk struk thermal 58mm.

### Riwayat Transaksi
- Klik **Riwayat Transaksi** atau **POS Penjualan** (tab index).
- Lihat detail: Invoice No, Tanggal, Total, Kasir, Item, Batch.
- Klik **Cetak Ulang** untuk cetak struk lagi.

---

## Pembelian/Penerimaan Barang

**Akses**: Owner, Admin Gudang

### Membuat Penerimaan Barang
1. Klik **Penerimaan Barang** di sidebar.
2. Klik **Tambah Penerimaan**.
3. Pilih **Supplier**.
4. Isi **Tanggal**, **Tgl Jatuh Tempo** (opsional).
5. **Tambah Item**:
   - Pilih **Produk**.
   - Isi **Batch No** (unik per produk).
   - Isi **Expired Date** (opsional, format: YYYY-MM-DD).
   - Isi **Qty**, **Harga Beli**, **Diskon** (opsional).
6. Sistem hitung **Total** otomatis.
7. Isi **Catatan** (opsional).
8. Klik **Simpan**.
9. Status otomatis **Posted** dan stok masuk ke batch.

### Riwayat Pembelian
- Lihat daftar PO dengan supplier, tanggal, total, status.
- Klik **Detail** untuk lihat item dan batch.

---

## Manajemen Stok

### Stok per Batch
**Akses**: Owner, Admin Gudang, Kasir

- Klik **Stok per Batch** di sidebar.
- Lihat: Produk, Batch No, Expired Date, Qty On Hand, Harga Beli.
- Filter per produk.

### Kartu Stok (Stock Movements)
**Akses**: Owner, Admin Gudang, Kasir

- Klik **Kartu Stok** di sidebar.
- Filter per **Produk**, **Batch**, **Tanggal**, **Tipe** (IN/OUT/ADJUST).
- Lihat histori pergerakan stok: penerimaan, penjualan, retur, opname.

### Stock Opname
**Akses**: Owner (approve), Admin Gudang (input)

**Membuat Opname**:
1. Klik **Stock Opname** di sidebar.
2. Klik **Buat Opname**.
3. Isi **Tanggal Opname**, **Catatan** (opsional).
4. **Tambah Item**:
   - Pilih **Batch**.
   - Sistem tampilkan **Qty Sistem** otomatis.
   - Isi **Qty Fisik** (hasil hitung manual).
   - Pilih **Reason**: SELISIH_OPNAME, RUSAK, KADALUARSA, HILANG.
   - Isi **Catatan** (opsional).
5. Klik **Simpan Opname**.
6. Jika selisih nilai > threshold (config), status **Pending** dan perlu persetujuan owner.
7. Jika di bawah threshold, otomatis **Approved** dan stok disesuaikan.

**Persetujuan Opname (Owner)**:
1. Buka detail opname dengan status **Pending**.
2. Periksa selisih qty dan nilai.
3. Klik **Setujui** atau **Tolak**.
4. Isi **Catatan Persetujuan**.
5. Jika disetujui, stok akan disesuaikan otomatis.

### Retur Supplier
**Akses**: Owner, Admin Gudang

**Membuat Retur**:
1. Klik **Retur Supplier** di sidebar.
2. Klik **Buat Retur**.
3. Pilih **Supplier**.
4. Isi **Tanggal Retur**, **Catatan** (opsional).
5. **Tambah Item**:
   - Pilih **Batch** (yang dibeli dari supplier).
   - Sistem tampilkan **Qty On Hand**.
   - Isi **Qty Retur**, **Alasan** (opsional).
6. Klik **Simpan Retur**.
7. Status otomatis **Posted** dan stok berkurang dari batch.

---

## Laporan

**Akses**: Owner, Admin Gudang, Kasir

### Membuka Laporan
1. Klik **Laporan** di sidebar.
2. Atur **Filter**:
   - **Periode**: Start Date - End Date.
   - **Grouping**: Harian, Mingguan, Bulanan.
   - **Near Expired**: 30/60/90 hari.
3. Klik **Tampilkan**.

### Isi Laporan
- **Ringkasan Penjualan**: Total sales, transaksi, per periode.
- **Penjualan per Kasir**: Performa kasir.
- **Penjualan per Item**: Top 25 produk terlaris.
- **Penjualan per Golongan**: Performa kategori obat.
- **Gross Profit**: Margin per produk.
- **Metode Pembayaran**: 7 hari terakhir.
- **Pembelian per Supplier**: Total PO dan nilai.
- **Hutang Jatuh Tempo**: 14 hari ke depan.
- **Stok Snapshot**: 50 batch terbaru.
- **Produk Hampir/Sudah Kadaluarsa**: Alert + estimasi loss.
- **Reorder Alert**: Produk di bawah reorder point.

### Ekspor & Kirim
- **Download PDF**: Klik **Download PDF** untuk laporan lengkap.
- **Kirim Email**: Klik **Kirim ke Email** untuk kirim laporan ke email owner (config).

---

## Manajemen User (Owner)

**Akses**: Owner only

### Menambah User
1. Klik **Manajemen User** di sidebar (OWNER MENU).
2. Klik **Tambah User**.
3. Isi:
   - **Nama**: Nama lengkap.
   - **Email**: Email unik (login).
   - **Password**: Min 8 karakter.
   - **Konfirmasi Password**.
   - **Role**: Owner, Kasir, Admin Gudang.
   - **Status**: Aktif/Non-aktif.
4. Klik **Simpan**.

### Mengedit User
1. Klik **Edit** di daftar user.
2. Ubah **Nama**, **Email**, **Role**, atau **Status**.
3. **Password** kosongkan jika tidak diubah.
4. Klik **Simpan**.

**Catatan**: Tidak bisa edit diri sendiri (role/status).

### Menonaktifkan User
- Klik **Nonaktifkan** untuk ubah status user menjadi non-aktif.
- User non-aktif tidak bisa login.
- **Proteksi**: Tidak bisa nonaktifkan atau turunkan role owner terakhir (minimal 1 owner harus ada).

### Audit Log
- Semua aksi create/update/disable user tercatat di tabel `audit_logs`.
- Owner bisa lihat histori via database (belum ada UI viewer).

---

## Ganti Password

**Akses**: Semua role

### Cara Ganti Password
1. Klik menu **Profil / Password** di sidebar (AKUN).
2. Isi:
   - **Password Saat Ini**: Untuk verifikasi.
   - **Password Baru**: Min 8 karakter.
   - **Konfirmasi Password Baru**.
3. Klik **Ganti Password**.
4. Sistem akan:
   - Logout sesi perangkat lain (keamanan).
   - Sesi saat ini tetap aktif.
5. Pesan sukses tampil.

**Catatan**: Gunakan password kuat (kombinasi huruf besar/kecil, angka, simbol).

---

## Tips & Best Practices

1. **Buka/Tutup Shift** (Kasir): Wajib setiap hari kerja untuk akurasi kas.
2. **Cek Expired**: Rutin cek dashboard/laporan produk hampir kadaluarsa.
3. **Stock Opname**: Lakukan minimal bulanan untuk validasi stok.
4. **Backup Data**: Owner pastikan backup database rutin.
5. **Ganti Password**: Secara berkala (3-6 bulan) untuk keamanan.
6. **Cetak Struk**: Selalu cetak struk untuk customer (thermal 58mm).
7. **Laporan Rutin**: Review laporan mingguan/bulanan untuk evaluasi bisnis.

---

## Troubleshooting

### Tidak Bisa Login
- Cek email dan password benar.
- Pastikan akun **Aktif** (hubungi owner jika dinonaktifkan).

### Stok Tidak Sesuai
- Cek **Kartu Stok** untuk histori pergerakan.
- Lakukan **Stock Opname** untuk koreksi.

### Laporan Email Gagal
- Pastikan konfigurasi email di `.env` benar.
- Atau gunakan **Download PDF** sebagai alternatif.

### Produk Tidak Muncul di POS
- Pastikan ada **Batch** dengan **Qty On Hand > 0**.
- Cek **Expired Date** tidak kadaluarsa.

---

## Kontak Support

Jika ada pertanyaan atau masalah teknis, hubungi:
- **Admin Sistem**: [Your Contact]
- **Email**: [support@example.com]

---

**Terima kasih telah menggunakan Toko Obat Rotua!**
