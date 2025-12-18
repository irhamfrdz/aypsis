# FIX DPP FROM MASTER PRICELIST

Script untuk memperbaiki DPP (Dasar Pengenaan Pajak) di tabel `daftar_tagihan_kontainer_sewa` berdasarkan harga di `master_pricelist_sewa_kontainers`.

## 📁 Files

1. **`fix_dpp_all_from_master_pricelist.php`** - Script lengkap dengan konfirmasi dan detail logging
2. **`fix_dpp_quick.php`** - Script cepat tanpa konfirmasi untuk testing

## 🎯 Tujuan

Memperbaiki perhitungan DPP yang salah dengan:
- Mengambil harga dari master pricelist berdasarkan vendor, size, dan tarif
- Untuk **Bulanan**: DPP = harga flat dari pricelist (tidak dikalikan periode)
- Untuk **Harian**: DPP = harga per hari × jumlah hari
- Recalculate PPN, PPH, dan Grand Total berdasarkan DPP yang benar

## 🔄 Cara Kerja

### 1. Membaca Master Pricelist
Script membaca semua data dari `master_pricelist_sewa_kontainers` dengan key:
```
{VENDOR}_{SIZE}_{TARIF}
```
Contoh: `DPE_40_BULANAN`, `ZONA_20_HARIAN`

### 2. Validasi Setiap Tagihan
Untuk setiap record di `daftar_tagihan_kontainer_sewa`:
- Cari pricelist yang sesuai berdasarkan vendor, size, dan tarif
- Hitung DPP yang seharusnya:
  - **Bulanan**: `DPP = harga_pricelist`
  - **Harian**: `DPP = harga_pricelist × jumlah_hari`

### 3. Perhitungan Pajak
Jika DPP berbeda (toleransi 1 rupiah):
```php
dpp_nilai_lain = dpp × (11/12)
ppn = dpp_nilai_lain × 0.12
pph = dpp × 0.02
grand_total = dpp + ppn - pph
```

### 4. Update Database
Update fields:
- `dpp`
- `dpp_nilai_lain`
- `ppn`
- `pph`
- `grand_total`
- `updated_at`

## 🚀 Cara Menggunakan

### Script Lengkap (dengan konfirmasi)
```bash
php fix_dpp_all_from_master_pricelist.php
```

**Output:**
```
╔══════════════════════════════════════════════════════════════════╗
║   FIX DPP BERDASARKAN MASTER PRICELIST SEWA KONTAINER           ║
╚══════════════════════════════════════════════════════════════════╝

📋 Master Pricelist yang tersedia:
──────────────────────────────────────────────────────────────────────
VENDOR     SIZE       TARIF                  HARGA
──────────────────────────────────────────────────────────────────────
DPE        40         Bulanan        Rp 1.500.000
DPE        20         Harian            Rp 25.000
ZONA       20         Bulanan          Rp 675.676
ZONA       40         Bulanan        Rp 1.261.261
ZONA       20         Harian            Rp 22.522
──────────────────────────────────────────────────────────────────────

⚠️  Script ini akan memperbaiki DPP di tabel daftar_tagihan_kontainer_sewa
   berdasarkan harga di master_pricelist_sewa_kontainers.

Lanjutkan? (y/n): y

╔══════════════════════════════════════════════════════════════════╗
║                    MEMULAI PERBAIKAN DPP                         ║
╚══════════════════════════════════════════════════════════════════╝

Total tagihan yang akan diproses: 150

✓ ID 1: RXTU4540180 - Periode 1
  Vendor: DPE, Size: 40, Tarif: BULANAN
  DPP        : Rp 1.500.000,00 → Rp 1.500.000,00
  PPN        : Rp 165.000,00 → Rp 165.000,00
  PPH        : Rp 30.000,00 → Rp 30.000,00
  Grand Total: Rp 1.635.000,00 → Rp 1.635.000,00
  Status: ✓ UPDATED

...

╔══════════════════════════════════════════════════════════════════╗
║                          SUMMARY                                 ║
╚══════════════════════════════════════════════════════════════════╝

📊 TARIF BULANAN:
  ✓ Diperbaiki        : 45 data
  ✓ Sudah benar       : 80 data
  ⚠ Pricelist missing : 2 data
  ✗ Error             : 0 data

📊 TARIF HARIAN:
  ✓ Diperbaiki        : 15 data
  ✓ Sudah benar       : 8 data
  ⚠ Pricelist missing : 0 data
  ✗ Error             : 0 data

📊 TOTAL:
  ✓ Total diperbaiki  : 60 data
  ✓ Total sudah benar : 88 data
  ⚠ Total no pricelist: 2 data
  ✗ Total error       : 0 data

✓ Script selesai! 60 data berhasil diperbaiki.
```

### Script Cepat (tanpa konfirmasi)
```bash
php fix_dpp_quick.php
```

**Output:**
```
=== FIX DPP FROM MASTER PRICELIST (QUICK MODE) ===

Pricelist tersedia: 5

Total tagihan: 150

✓ ID 1: RXTU4540180 - DPP updated
✓ ID 2: RXTU4540180 - DPP updated
...

=== SUMMARY ===
Fixed   : 60
Skipped : 88
Errors  : 2

✓ 60 data berhasil diperbaiki!
```

## ⚠️ Perhatian

1. **Backup Database**: Selalu backup database sebelum menjalankan script
2. **Pricelist Missing**: Jika ada tagihan yang vendor/size/tarif-nya tidak ada di master pricelist, data tersebut akan di-skip
3. **Toleransi**: Script menggunakan toleransi 1 rupiah untuk pembulatan
4. **Group Summary**: Script otomatis mengabaikan record dengan `nomor_kontainer` yang dimulai dengan `GROUP_SUMMARY_` atau `GROUP_TEMPLATE`

## 📊 Contoh Kasus

### Kasus 1: Tarif Bulanan
**Before:**
- DPP: Rp 3.000.000 (salah, mungkin dikalikan periode)
- PPN: Rp 330.000
- PPH: Rp 60.000
- Grand Total: Rp 3.270.000

**After:**
- DPP: Rp 1.500.000 (dari master pricelist)
- PPN: Rp 165.000
- PPH: Rp 30.000
- Grand Total: Rp 1.635.000

### Kasus 2: Tarif Harian
**Before:**
- Masa: 7 hari
- DPP: Rp 150.000 (salah perhitungan)
- PPN: Rp 16.500
- PPH: Rp 3.000
- Grand Total: Rp 163.500

**After:**
- Masa: 7 hari
- DPP: Rp 175.000 (25.000 × 7 hari)
- PPN: Rp 19.250
- PPH: Rp 3.500
- Grand Total: Rp 190.750

## 🔧 Troubleshooting

### Error: "Pricelist tidak ditemukan"
**Solusi:** Tambahkan data pricelist di tabel `master_pricelist_sewa_kontainers`
```sql
INSERT INTO master_pricelist_sewa_kontainers 
(vendor, tarif, ukuran_kontainer, harga, tanggal_harga_awal)
VALUES 
('DPE', 'Bulanan', '40', 1500000.00, '2025-01-01');
```

### Error: "Tanggal awal/akhir tidak ada"
**Solusi:** Pastikan data tagihan dengan tarif Harian memiliki `tanggal_awal` dan `tanggal_akhir`

## 📝 Changelog

**2025-12-18**
- Initial version
- Support untuk tarif Bulanan dan Harian
- Perhitungan pajak otomatis
- Error handling untuk pricelist missing
