# Panduan Penamaan SKU Produk

## Apa itu SKU?

**SKU (Stock Keeping Unit)** adalah kode unik untuk setiap produk yang memudahkan identifikasi, pencatatan stok, dan transaksi. SKU harus **UNIK** - tidak boleh ada dua produk dengan SKU yang sama.

---

## Format SKU yang Direkomendasikan

### Format Umum
```
[KATEGORI][BENTUK]-[NOMOR URUT]
```

### Contoh Penerapan

#### 1. **Obat Tablet/Kaplet/Kapsul**
```
OBT-0001   → PARACETAMOL 500MG TAB
OBT-0002   → AMOXICILLIN 500MG KPS
OBT-0143   → ANTASIDA DOEN TAB
```

#### 2. **Obat Sirup/Cairan**
```
SRP-0001   → OBH COMBI SIRUP 100ML
SRP-0002   → AMBROXOL SIRUP 60ML
SRP-0015   → PARACETAMOL DROP 15ML
```

#### 3. **Salep/Krim/Gel**
```
TOP-0001   → BETADINE SALEP 10G
TOP-0002   → BIOPLACENTON GEL 15G
TOP-0023   → ACNE CREAM 20G
```

#### 4. **Injeksi/Ampul**
```
INJ-0001   → NEUROBION 3ML INJ
INJ-0002   → KETOROLAC 30MG/ML AMP
```

#### 5. **Alat Kesehatan**
```
ALK-0001   → MASKER 3PLY (BOX 50PCS)
ALK-0002   → HANDSCOON LATEX SIZE M
ALK-0015   → TENSIMETER DIGITAL
```

#### 6. **Suplemen/Vitamin**
```
SUP-0001   → VITAMIN C 500MG TAB
SUP-0002   → OMEGA 3 KAPSUL
SUP-0034   → MULTIVITAMIN SYRUP
```

---

## Kode Kategori Produk

| Kode | Kategori | Contoh Produk |
|------|----------|---------------|
| **OBT** | Obat Oral (Tab/Kapsul/Kaplet) | Paracetamol, Amoxicillin, CTM |
| **SRP** | Sirup/Cairan/Drop | OBH, Ambroxol Sirup, Vitamin Drop |
| **TOP** | Topikal (Salep/Krim/Gel) | Betadine, Bioplacenton, Acne Gel |
| **INJ** | Injeksi/Ampul | Neurobion Inj, Ketorolac Amp |
| **ALK** | Alat Kesehatan | Masker, Plester, Tensimeter |
| **SUP** | Suplemen/Vitamin | Vitamin C, Omega 3, Multivitamin |
| **HRB** | Herbal/Jamu | Tolak Angin, Antangin, Hemaviton |

---

## Aturan Penamaan SKU

### ✅ **DO (Lakukan)**

1. **Gunakan huruf KAPITAL**
   - ✅ `OBT-0001`
   - ❌ `obt-0001`

2. **Gunakan nomor urut dengan leading zero**
   - ✅ `OBT-0001`, `OBT-0023`, `OBT-0145`
   - ❌ `OBT-1`, `OBT-23`, `OBT-145`

3. **Konsisten dengan pemisah (gunakan dash `-`)**
   - ✅ `OBT-0001`
   - ❌ `OBT0001`, `OBT_0001`, `OBT.0001`

4. **Pastikan SKU UNIK**
   - Setiap produk harus punya SKU berbeda
   - Cek dulu sebelum input produk baru

5. **Simpel dan mudah diingat**
   - Jangan terlalu panjang atau rumit
   - Maksimal 10-12 karakter

### ❌ **DON'T (Jangan)**

1. **Jangan pakai spasi**
   - ❌ `OBT 0001`, `OBT - 0001`

2. **Jangan pakai karakter khusus berlebihan**
   - ❌ `OBT@001`, `OBT#001`, `OBT/001`

3. **Jangan pakai tanggal atau info yang berubah**
   - ❌ `OBT-2025-001` (tahun bisa ganti)

4. **Jangan terlalu spesifik ke batch atau expired**
   - ❌ `PCT-500-EXP2025` (expired beda-beda per batch)

---

## Panduan Praktis Input SKU

### Saat Menambah Produk BARU

1. **Tentukan kategori produk** (OBT/SRP/TOP/INJ/ALK/SUP/HRB)
2. **Cek SKU terakhir di kategori tersebut** di daftar produk
3. **Tambahkan 1 ke nomor urut terakhir**
   - Terakhir: `OBT-0045` → Buat: `OBT-0046`
4. **Input SKU ke form produk**

### Saat Import dari Excel

- SKU akan **auto-generate** dengan format `OBT00001`, `OBT00002`, dst
- Anda bisa edit manual nanti jika ingin mengubah formatnya

---

## Tips & Trik

### 📌 **Tip 1: Catat SKU Terakhir**
Buat catatan SKU terakhir per kategori untuk memudahkan:
```
OBT: OBT-0150
SRP: SRP-0045
TOP: TOP-0032
ALK: ALK-0018
```

### 📌 **Tip 2: Reserved Number**
Sisakan nomor untuk produk khusus:
- `XXX-9001` sampai `XXX-9999` → Produk konsinyasi
- `XXX-0001` sampai `XXX-0100` → Produk fast moving

### 📌 **Tip 3: Dokumentasi**
Simpan Excel mapping SKU ↔ Nama Produk sebagai backup

### 📌 **Tip 4: Konsistensi Tim**
Pastikan semua staff paham format SKU yang sama

---

## FAQ (Frequently Asked Questions)

### ❓ Apakah SKU boleh diubah setelah produk dibuat?

**Tidak disarankan!** SKU sudah terkait dengan:
- Transaksi penjualan
- Stock movement
- Laporan

Jika terpaksa ubah, harus update manual di semua tabel terkait.

### ❓ Bagaimana jika produk sama tapi kemasan berbeda?

Buat SKU berbeda dengan suffix ukuran:
```
SRP-0010   → OBH COMBI 60ML
SRP-0011   → OBH COMBI 100ML
SRP-0012   → OBH COMBI 150ML
```

### ❓ Produk generik dan paten pakai SKU yang sama?

**TIDAK!** Produk berbeda = SKU berbeda, meskipun kandungan sama:
```
OBT-0050   → PARACETAMOL 500MG GENERIK
OBT-0051   → PANADOL 500MG (PATEN)
```

### ❓ Bagaimana dengan produk bundle/paket?

Gunakan kategori khusus atau prefix:
```
PKT-0001   → PAKET DEMAM (PCT + CTM + VITAMIN C)
ALK-0099   → PAKET LUKA (BETADINE + KASA + PLESTER)
```

---

## Contoh Kasus Nyata

### Kasus 1: Input 10 Produk Baru Kategori Tablet
```
OBT-0201 → PARACETAMOL 500MG TAB
OBT-0202 → AMOXICILLIN 500MG KPS
OBT-0203 → CTM 4MG TAB
OBT-0204 → ANTASIDA DOEN TAB
OBT-0205 → ASAM MEFENAMAT 500MG TAB
OBT-0206 → CETIRIZINE 10MG TAB
OBT-0207 → DOMPERIDONE 10MG TAB
OBT-0208 → OMEPRAZOLE 20MG KPS
OBT-0209 → LORATADINE 10MG TAB
OBT-0210 → SALBUTAMOL 4MG TAB
```

### Kasus 2: Input Produk Mix Kategori
```
OBT-0301 → PARACETAMOL 500MG TAB
SRP-0101 → PARACETAMOL DROP 15ML
TOP-0051 → BIOPLACENTON GEL 15G
ALK-0021 → MASKER SENSI 3PLY ISI 50
SUP-0015 → VITAMIN C 1000MG TAB
```

---

## Kesimpulan

✅ **SKU yang baik:**
- UNIK untuk setiap produk
- Konsisten formatnya
- Mudah diingat dan diidentifikasi
- Terstruktur berdasarkan kategori

✅ **Format rekomendasi:**
```
[KATEGORI]-[NOMOR URUT 4 DIGIT]
Contoh: OBT-0001, SRP-0045, TOP-0023
```

✅ **Selalu cek SKU terakhir** sebelum menambah produk baru untuk memastikan tidak ada duplikasi!

---

**Butuh bantuan?** Hubungi Admin Gudang atau Owner untuk konsultasi penamaan SKU produk khusus.
