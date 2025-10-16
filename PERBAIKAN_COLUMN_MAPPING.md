# Perbaikan Column Mapping Setelah Penghapusan DPP Nilai Lain

## Tanggal: 2024
## File: resources/views/daftar-tagihan-kontainer-sewa/index.blade.php

## Masalah
Setelah menghapus kolom "DPP Nilai Lain" dari tabel, semua kolom setelahnya bergeser ke kiri satu posisi. Hal ini menyebabkan selector JavaScript nth-child() menunjuk ke kolom yang salah, sehingga tombol "Masukan ke Pranota" tidak berfungsi.

## Urutan Kolom SETELAH Penghapusan DPP Nilai Lain

| No | Nama Kolom | Index nth-child |
|----|------------|-----------------|
| 1  | Checkbox | 1 |
| 2  | Grup | 2 |
| 3  | Vendor | 3 |
| 4  | Nomor Kontainer | 4 |
| 5  | Size | 5 |
| 6  | Periode | 6 |
| 7  | Masa | 7 |
| 8  | Tgl Awal | 8 |
| 9  | Tgl Akhir | 9 |
| 10 | Tarif | 10 |
| 11 | DPP | 11 |
| 12 | Adjustment | 12 |
| 13 | Alasan Adjustment | 13 |
| 14 | Invoice Vendor | 14 |
| 15 | Tanggal Vendor | 15 |
| 16 | PPN | 16 |
| 17 | PPH | 17 |
| 18 | Grand Total | 18 |
| 19 | Status | 19 |
| 20 | Status Pranota | 20 |
| 21 | Aksi | 21 |

## Perubahan yang Dilakukan

### 1. Line ~1389 - Fungsi updateBulkActionsVisibility()
**Sebelum:**
```javascript
const statusPranotaElement = row.querySelector('td:nth-child(21)');
```
**Sesudah:**
```javascript
const statusPranotaElement = row.querySelector('td:nth-child(20)'); // Status Pranota column (index 20, was 21 before)
```

### 2. Line ~1615 - Fungsi masukanKePranota() - Validasi Invoice Vendor
**Tidak berubah** (tetap 14):
```javascript
const invoiceVendorElement = row.querySelector('td:nth-child(14)'); // Invoice Vendor column (index 14)
```

### 3. Line ~1643 - Fungsi masukanKePranota() - Validasi Status Pranota
**Sebelum:**
```javascript
const statusPranotaElement = row.querySelector('td:nth-child(21)');
```
**Sesudah:**
```javascript
const statusPranotaElement = row.querySelector('td:nth-child(20)'); // Status Pranota column (index 20, was 21 before)
```

### 4. Line ~1694 - Fungsi masukanKePranota() - Extract Grand Total
**Sebelum:**
```javascript
const totalElement = row.querySelector('td:nth-child(19)'); // Grand Total column (19th column)
```
**Sesudah:**
```javascript
const totalElement = row.querySelector('td:nth-child(18)'); // Grand Total column (18th column, was 19 before)
```

### 5. Line ~1765 - Fungsi buatPranotaTerpilih() - Extract Grand Total
**Sebelum:**
```javascript
const totalElement = row.querySelector('td:nth-child(19)'); // Grand Total column (19th column)
```
**Sesudah:**
```javascript
const totalElement = row.querySelector('td:nth-child(18)'); // Grand Total column (18th column, was 19 before)
```

### 6. Line ~1857 - Fungsi ungroupSelectedContainers() - Validasi Group
**Sebelum:**
```javascript
const groupCell = row.querySelector('td:nth-child(7)'); // Group column (index 7)
```
**Sesudah:**
```javascript
const groupCell = row.querySelector('td:nth-child(2)'); // Group column (index 2, was 7 before)
```

## Kolom yang TIDAK Berubah

Kolom-kolom berikut masih menggunakan index yang sama karena berada SEBELUM kolom yang dihapus:

- **Grup**: nth-child(2) ✓
- **Vendor**: nth-child(3) ✓
- **Nomor Kontainer**: nth-child(4) ✓
- **Size**: nth-child(5) ✓
- **Periode**: nth-child(6) ✓
- **Invoice Vendor**: nth-child(14) ✓ (DPP Nilai Lain ada SETELAH Invoice Vendor)

## Fungsi yang Terpengaruh

1. ✅ `updateBulkActionsVisibility()` - Diperbaiki
2. ✅ `masukanKePranota()` - Diperbaiki
3. ✅ `buatPranotaTerpilih()` - Diperbaiki
4. ✅ `ungroupSelectedContainers()` - Diperbaiki

## Testing Checklist

- [ ] Tombol "Masukan ke Pranota" dapat diklik
- [ ] Validasi grup berfungsi dengan benar
- [ ] Validasi invoice vendor berfungsi
- [ ] Validasi status pranota berfungsi
- [ ] Grand total terbaca dengan benar
- [ ] Fungsi ungroup bekerja dengan benar
- [ ] Data yang diekstrak sesuai dengan kolom yang dimaksud

## Catatan Penting

⚠️ **JIKA MENAMBAH/MENGHAPUS KOLOM DI MASA DEPAN:**

1. Pastikan update semua selector nth-child() yang terpengaruh
2. Gunakan dokumentasi ini sebagai referensi posisi kolom
3. Test semua fungsi JavaScript yang menggunakan nth-child()
4. Perhatikan bahwa nth-child dimulai dari index 1 (bukan 0)
5. Kolom yang berubah posisi akan mempengaruhi validasi dan data extraction

## File Terkait

- `resources/views/daftar-tagihan-kontainer-sewa/index.blade.php` - File utama yang diperbaiki
- `PERUBAHAN_HAPUS_DPP_NILAI_LAIN.md` - Dokumentasi penghapusan kolom
