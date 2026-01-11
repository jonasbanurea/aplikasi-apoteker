# Cara Edit Tanggal Expired Obat

## Skenario
Obat **NEOZEP** ternyata belum expired dan perlu diupdate tanggal expired-nya.

## Cara 1: Edit melalui Stock Batch (RECOMMENDED) ✅

### Langkah-langkah:
1. **Login** ke aplikasi dengan user Owner atau Admin Gudang
2. Buka menu **Stok** → **Stok per Batch**
3. **Filter** produk dengan memilih "NEOZEP" di dropdown Produk
4. Klik tombol **Filter**
5. Pada baris obat NEOZEP yang ingin diedit, klik tombol **Edit** (ikon pensil) 📝
6. Ubah **Expired Date** ke tanggal yang benar
7. Klik tombol **Update**
8. ✅ Tanggal expired berhasil diupdate!

### Keuntungan cara ini:
- Langsung edit data batch yang tersimpan
- Bisa edit expired date, cost price, qty, dll
- Tidak perlu edit purchase/penerimaan
- Lebih cepat dan praktis

---

## Cara 2: Edit melalui Purchase/Penerimaan

### Langkah-langkah:
1. **Login** ke aplikasi dengan user Owner atau Admin Gudang
2. Buka menu **Pembelian** → **Penerimaan**
3. **Cari** penerimaan yang berisi obat NEOZEP (bisa filter berdasarkan tanggal atau supplier)
4. Klik tombol **Edit** pada penerimaan tersebut
5. Pada baris obat NEOZEP, ubah kolom **Expired**
6. Klik tombol **Simpan**
7. ✅ Data batch akan diupdate otomatis!

### Catatan:
- Cara ini akan update data purchase_items DAN stock_batches
- Gunakan cara ini jika Anda juga perlu edit data pembelian lainnya (qty, harga, dll)

---

## Hak Akses
Hanya **Owner** dan **Admin Gudang** yang bisa mengedit stock batch atau penerimaan.

## Tips
- Pastikan tanggal expired yang baru sudah benar sebelum disimpan
- Jika obat memiliki beberapa batch, pastikan edit batch yang tepat
- Setelah edit, cek di halaman **Stok per Batch** untuk memastikan perubahannya tersimpan

---

## Screenshot Lokasi Menu
```
Dashboard
  └─ Stok
      └─ Stok per Batch  ← Di sini!
          ├─ Filter produk
          ├─ Pilih NEOZEP
          └─ Klik Edit (ikon pensil)
```

---

**Dibuat:** 11 Januari 2026  
**Untuk:** Toko Obat Ro Tua
