# Quick Reference: Format SKU Produk

## Format SKU
```
[KATEGORI]-[NOMOR URUT]
```

## Kode Kategori

| Kode | Jenis Produk | Contoh |
|------|--------------|--------|
| **OBT** | Tablet/Kapsul/Kaplet | `OBT-0001` |
| **SRP** | Sirup/Cairan/Drop | `SRP-0001` |
| **TOP** | Salep/Krim/Gel | `TOP-0001` |
| **INJ** | Injeksi/Ampul | `INJ-0001` |
| **ALK** | Alat Kesehatan | `ALK-0001` |
| **SUP** | Suplemen/Vitamin | `SUP-0001` |
| **HRB** | Herbal/Jamu | `HRB-0001` |

## Contoh Lengkap

### Obat Oral (OBT)
- `OBT-0001` → Paracetamol 500mg Tab
- `OBT-0002` → Amoxicillin 500mg Kapsul
- `OBT-0003` → CTM 4mg Tab

### Sirup/Cairan (SRP)
- `SRP-0001` → OBH Combi Sirup 100ml
- `SRP-0002` → Ambroxol Sirup 60ml
- `SRP-0003` → Paracetamol Drop 15ml

### Salep/Krim (TOP)
- `TOP-0001` → Betadine Salep 10g
- `TOP-0002` → Bioplacenton Gel 15g
- `TOP-0003` → Acne Cream 20g

### Alat Kesehatan (ALK)
- `ALK-0001` → Masker 3ply Box 50pcs
- `ALK-0002` → Handscoon Latex Size M
- `ALK-0003` → Plester Hansaplast 1 Roll

## Aturan Penting

✅ **LAKUKAN:**
- Gunakan huruf KAPITAL: `OBT-0001`
- Gunakan 4 digit: `0001`, `0045`, `0234`
- Pastikan UNIK (tidak ada duplikat)
- Cek SKU terakhir sebelum input baru

❌ **JANGAN:**
- Pakai huruf kecil: ~~`obt-0001`~~
- Pakai spasi: ~~`OBT 0001`~~
- Skip angka depan: ~~`OBT-1`~~
- Duplikat SKU yang sudah ada

## Cara Cepat Input SKU

1. Lihat kategori produk (Tablet? Sirup? Salep?)
2. Cek SKU terakhir di kategori tersebut
3. Tambah 1 ke nomor urut
4. Input dengan format: `[KODE]-[NOMOR]`

**Contoh:**
- SKU terakhir: `OBT-0045`
- Produk baru: `OBT-0046`

---

**Butuh panduan lengkap?** Buka: `docs/PANDUAN_SKU_PRODUK.md`
