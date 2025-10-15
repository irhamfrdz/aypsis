# Dokumentasi Script Perbaikan DPP Calculation

## Overview
Script `fix_dpp_calculation.php` digunakan untuk memperbaiki bug perhitungan DPP (Dasar Pengenaan Pajak) pada kontainer dengan tarif bulanan. Bug ini menyebabkan DPP dikalikan dengan periode, padahal untuk tarif bulanan seharusnya menggunakan harga flat dari master pricelist.

## Bug yang Diperbaiki
- **Masalah**: Kontainer dengan tarif bulanan memiliki DPP yang salah karena dikalikan dengan periode
- **Contoh**: Kontainer TEXU7210230 dengan periode 6 memiliki DPP Rp 7.567.566 padahal seharusnya Rp 1.261.261
- **Impact**: Total selisih billing mencapai ratusan juta rupiah

## Cara Menggunakan Script

### 1. Persiapan
```bash
# Pastikan berada di directory project
cd /path/to/aypsis

# Pastikan database connection tersedia
# Script akan menggunakan konfigurasi Laravel yang ada
```

### 2. Menjalankan Script
```bash
php fix_dpp_calculation.php
```

### 3. Proses yang Terjadi

#### Step 1: Analisis
Script akan mencari semua record dengan masalah DPP:
```
=== PERBAIKAN DPP CALCULATION BUG ===
Step 1: Mencari semua record dengan masalah DPP...
Ditemukan 567 records dengan DPP yang salah
```

#### Step 2: Preview Perbaikan
Script menampilkan preview 10 record pertama yang akan diperbaiki:
```
=== PREVIEW PERBAIKAN ===
Container: TEXU7210230 (ZONA 40ft) - Periode: 6
  Current DPP: Rp 7.567.566
  Correct DPP: Rp 1.261.261
  Selisih: Rp 6.306.305
  Grand Total Selisih: Rp 6.874.353
```

#### Step 3: Konfirmasi
Script meminta konfirmasi sebelum melakukan perbaikan:
```
Total estimasi selisih Grand Total: Rp 460.274.011

Apakah Anda ingin melanjutkan perbaikan? (y/n): 
```

**PENTING**: Ketik `y` untuk melanjutkan atau `n` untuk membatalkan

#### Step 4: Perbaikan
Jika dikonfirmasi, script akan:
- Menggunakan database transaction untuk keamanan
- Memperbaiki DPP sesuai master pricelist
- Menghitung ulang PPN (11% dari DPP)
- Menghitung ulang PPh (2% dari DPP)  
- Menghitung ulang Grand Total

```
=== MEMULAI PERBAIKAN ===
✓ Fixed: TEXU7210230 - DPP: Rp 1.261.261
✓ Fixed: BMOU4192536 - DPP: Rp 1.261.261
...

=== PERBAIKAN SELESAI ===
Records berhasil diperbaiki: 567
Records yang error: 0
```

#### Step 5: Verifikasi
Script menampilkan hasil akhir untuk memastikan perbaikan berhasil:
```
=== VERIFIKASI HASIL ===
Verifikasi TEXU7210230:
  DPP saat ini: Rp 1.261.261
  PPN: Rp 138.739
  PPh: Rp 25.225
  Grand Total: Rp 1.374.774
```

## Kriteria Record yang Diperbaiki

Script hanya memperbaiki record yang memenuhi kriteria:

1. **Tarif Bulanan**: Record harus menggunakan tarif `bulanan` dari master pricelist
2. **Selisih DPP**: DPP actual berbeda lebih dari 10% dari harga master pricelist
3. **Data Valid**: Master pricelist tersedia untuk ukuran dan vendor kontainer

## Formula Perbaikan

### DPP (Dasar Pengenaan Pajak)
```
DPP = harga_master_pricelist (untuk tarif bulanan)
```

### PPN (Pajak Pertambahan Nilai)
```
PPN = DPP × 11%
```

### PPh (Pajak Penghasilan)
```
PPh = DPP × 2%
```

### Grand Total
```
Grand Total = DPP + PPN - PPh
```

## Safety Features

### 1. Database Transaction
- Semua perubahan menggunakan transaction
- Jika ada error, semua perubahan akan di-rollback
- Tidak ada perubahan partial yang bisa merusak data

### 2. Preview Mode
- Script menampilkan preview sebelum melakukan perubahan
- User harus konfirmasi eksplisit untuk melanjutkan
- Estimasi dampak finansial ditampilkan

### 3. Error Handling
```php
try {
    // Proses perbaikan
    DB::commit();
} catch (Exception $e) {
    DB::rollback();
    echo "Error: " . $e->getMessage();
}
```

## Log Output Sample

```
=== PERBAIKAN DPP CALCULATION BUG ===
Step 1: Mencari semua record dengan masalah DPP...
Ditemukan 567 records dengan DPP yang salah

=== PREVIEW PERBAIKAN ===
Container: AMFU8640522 (ZONA 40ft) - Periode: 2
  Current DPP: Rp 2.522.522
  Correct DPP: Rp 1.261.261
  Selisih: Rp 1.261.261
  Grand Total Selisih: Rp 1.374.774

... [preview 10 records] ...

Total estimasi selisih Grand Total: Rp 460.274.011

Apakah Anda ingin melanjutkan perbaikan? (y/n): y

=== MEMULAI PERBAIKAN ===
✓ Fixed: AMFU8640522 - DPP: Rp 1.261.261
✓ Fixed: TEXU7210230 - DPP: Rp 1.261.261
... [567 records fixed] ...

=== PERBAIKAN SELESAI ===
Records berhasil diperbaiki: 567
Records yang error: 0
Total records diproses: 567

Perbaikan berhasil disimpan ke database.
```

## Troubleshooting

### Error: "Database connection failed"
```bash
# Cek konfigurasi database di .env
php artisan config:cache
php artisan config:clear
```

### Error: "Class not found"
```bash
# Regenerate autoload
composer dump-autoload
```

### Error: "Transaction timeout"
```bash
# Untuk dataset besar, tingkatkan timeout MySQL
# Atau jalankan script dalam batch kecil
```

## Backup Recommendation

**SANGAT DISARANKAN** untuk backup database sebelum menjalankan script:

```bash
# MySQL dump
mysqldump -u username -p database_name > backup_before_dpp_fix.sql

# Atau backup specific table
mysqldump -u username -p database_name daftar_tagihan_kontainer_sewa > tagihan_backup.sql
```

## Post-Fix Verification

Setelah menjalankan script, verifikasi hasil dengan:

```sql
-- Cek apakah masih ada DPP yang salah untuk tarif bulanan
SELECT dt.nomor_kontainer, dt.vendor, dt.size, dt.periode, dt.dpp, mp.harga
FROM daftar_tagihan_kontainer_sewa dt
JOIN master_pricelist_sewa_kontainer mp ON mp.ukuran_kontainer = dt.size AND mp.vendor = dt.vendor
WHERE mp.tarif = 'bulanan' 
AND ABS(dt.dpp - mp.harga) > (mp.harga * 0.1)
LIMIT 10;
```

Jika query di atas tidak mengembalikan hasil, berarti perbaikan berhasil 100%.

## Notes

- Script ini hanya memperbaiki **tarif bulanan**
- **Tarif harian** sudah benar dan tidak diubah
- Script aman dijalankan berulang kali (idempotent)
- Semua perubahan tercatat dalam log output

---

**Author**: GitHub Copilot  
**Date**: October 15, 2025  
**Version**: 1.0