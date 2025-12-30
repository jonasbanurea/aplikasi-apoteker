# User Acceptance Test (UAT) - Toko Obat Rotua

## Cara Pakai
- Jalankan `php artisan migrate --seed` sebelum testing untuk memastikan data dasar tersedia.
- Gunakan browser dalam mode incognito/private agar sesi bersih.
- Uji pada tiga akun default: owner@rotua.test, kasir@rotua.test, gudang@rotua.test (semua password: `password`).

## Skenario End-to-End (Input → Proses → Output yang Diharapkan)

### 1) Login & Redirect per Role
- **Input:** Masuk ke halaman login, isi kredensial sesuai role.
- **Proses:** Submit form login.
- **Output:**
  - Owner diarahkan ke dashboard owner, sidebar menampilkan menu "OWNER MENU" dengan "Manajemen User" aktif.
  - Kasir diarahkan ke dashboard kasir; menu owner tidak terlihat.
  - Admin gudang diarahkan ke dashboard admin gudang; menu owner tidak terlihat.
  - Jika user berstatus non-aktif, login ditolak dengan pesan error.

### 2) Pembatasan Akses /users (RBAC)
- **Input:** Akses URL /users sebagai owner, kasir, admin gudang.
- **Proses:** Buka halaman langsung atau via sidebar (untuk owner).
- **Output:**
  - Owner dapat melihat daftar user.
  - Kasir dan admin gudang ditolak (redirect atau 403) tanpa menampilkan data user.

### 3) Owner Membuat User Baru
- **Input:** Di halaman Manajemen User → klik Tambah User, isi: nama, email unik, password, konfirmasi, role (owner/kasir/admin_gudang), status aktif.
- **Proses:** Submit form create.
- **Output:**
  - Pesan sukses tampil.
  - User baru muncul di tabel dengan role dan status sesuai input.
  - Audit log mencatat aksi "create_user" oleh owner.

### 4) Owner Mengubah User (Profil dan Role)
- **Input:** Di daftar user pilih Edit pada user lain, ubah nama/email/role/status.
- **Proses:** Submit form edit.
- **Output:**
  - Perubahan tersimpan dan muncul di tabel.
  - Audit log mencatat "update_user" dengan detail user yang diubah.

### 5) Owner Menonaktifkan User (Disable)
- **Input:** Klik Nonaktifkan pada user aktif (bukan diri sendiri, bukan satu-satunya owner).
- **Proses:** Konfirmasi aksi jika diminta.
- **Output:**
  - Status user berubah menjadi non-aktif di tabel.
  - User tersebut tidak bisa login; percobaan login menampilkan pesan gagal.
  - Audit log mencatat "disable_user".

### 6) Proteksi Owner Terakhir
- **Input:** Coba nonaktifkan atau turunkan role owner terakhir.
- **Proses:** Submit aksi.
- **Output:**
  - Aksi ditolak dengan pesan error bahwa minimal satu owner harus ada.
  - Tidak ada perubahan data.

### 7) Ubah Password (Semua Role)
- **Input:** Dari menu Akun → Ubah Password, isi password saat ini, password baru, konfirmasi.
- **Proses:** Submit form.
- **Output:**
  - Pesan sukses tampil.
  - Sesi perangkat lain user tersebut di-logout (uji dengan tab browser kedua: akses dilogout dan diminta login ulang).
  - Audit log mencatat "change_password" untuk user tersebut.

### 8) Login Setelah Password Diganti
- **Input:** Coba login dengan password lama dan kemudian dengan password baru.
- **Proses:** Submit form login.
- **Output:**
  - Password lama gagal, password baru berhasil.

### 9) Sidebar & Menu Visibilitas
- **Input:** Login sebagai masing-masing role, amati sidebar.
- **Proses:** Navigasi halaman.
- **Output:**
  - Owner melihat menu "Manajemen User" di OWNER MENU.
  - Kasir/admin gudang tidak melihat menu tersebut.
  - Menu lain sesuai role tampil (master, transaksi, laporan bila ada route-nya).

### 10) Logout
- **Input:** Klik Logout di sidebar.
- **Proses:** Submit form logout.
- **Output:**
  - Kembali ke halaman login, sesi berakhir; akses ke halaman internal memaksa login ulang.

### 11) Audit Log Konsistensi (Spot Check)
- **Input:** Setelah menjalankan aksi create/update/disable user dan ubah password.
- **Proses:** Cek tabel `audit_logs` (via DB client) atau endpoint/log viewer jika tersedia.
- **Output:**
  - Setiap aksi tercatat dengan kolom user_id, action, target_type/target_id, metadata (role/status), dan timestamp.

## Catatan Verifikasi Teknis
- Pastikan validasi form bekerja: email unik, konfirmasi password sama, password minimal sesuai aturan, status wajib.
- Pastikan pesan error/sukses tampil rapi dan tidak membocorkan detail sensitif.
- Gunakan data uji berbeda untuk membedakan hasil pada tabel audit.
