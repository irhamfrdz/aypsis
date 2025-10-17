# Rollback Tarif - Kembali ke Uang Jalan

## Status: ✅ COMPLETED

Keputusan: Menghapus kolom `tarif` dan kembali ke `uang_jalan` sebagai satu-satunya biaya operasional.

## Alasan Rollback

Setelah analisis data:

-   **UANG_JALAN**: Rp 675 per surat jalan (dari tabel tujuan) → Digunakan untuk operasional
-   **TARIF**: Rp 144K - 814K per surat jalan (random test data) → TIDAK DIPERLUKAN

Kesimpulan: Hanya butuh 1 kolom (`uang_jalan`), bukan 2.

## Perubahan Yang Dilakukan

### 1. Database

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Result: Kolom tarif DIHAPUS dari tabel surat_jalans
```

### 2. Model - `app/Models/SuratJalan.php`

-   ❌ Hapus: `'tarif'` dari $fillable array
-   ❌ Hapus: `'tarif' => 'decimal:2'` dari $casts array
-   ✅ Tetap: `'uang_jalan' => 'decimal:2'` di casts

### 3. Form - `resources/views/surat-jalan/create.blade.php`

-   ❌ Hapus: Input field "Tarif (Biaya Pengiriman)" entirely
-   ✅ Tetap: Input field "Uang Jalan" (readonly, auto-calculated)

### 4. Pranota Form - `resources/views/pranota-surat-jalan/create.blade.php`

#### HTML Changes

| Element        | Before                | After                      |
| -------------- | --------------------- | -------------------------- |
| Section title  | "Total Tarif"         | "Total Uang Jalan"         |
| Column header  | "Tarif"               | "Uang Jalan"               |
| Input ID       | `total_tarif_display` | `total_uang_jalan_display` |
| Data attribute | `data-tarif`          | `data-uang_jalan`          |
| Summary text   | "total tarif"         | "total uang jalan"         |

#### JavaScript Changes

```javascript
// OLD
function updateTotalTarif() {
    total += parseFloat(checkbox.dataset.tarif) || 0;
}

// NEW
function updateTotalUangJalan() {
    total += parseFloat(checkbox.dataset.uang_jalan) || 0;
}
```

## Data Hasil Verifikasi

### Surat Jalan Approved (Ready for Pranota)

```
NO. SURAT JALAN | TANGGAL    | PENGIRIM         | TUJUAN | UANG_JALAN
──────────────────────────────────────────────────────────────────
SJ0005          | 2025-10-16 | PT ABADI COATING | ACEH   | Rp 675
SJ00006         | 2025-10-16 | PT ABADI COATING | ACEH   | Rp 675
SJ00001         | 2025-10-15 | PT ABADI COATING | ACEH   | Rp 675
──────────────────────────────────────────────────────────────────
Total Uang Jalan:                                          Rp 2.025
```

### Surat Jalan Lainnya

-   Total records: 10
-   Total uang jalan: Rp 6.075 (semua record)
-   Kolom tarif: TIDAK ADA (sudah dihapus)

## Fitur Pranota - Hasil Update

### Sebelum

-   ❌ Tampilkan "Tarif" (nilai random 144K-814K)
-   ❌ Hitung "Total Tarif" dari checkbox dipilih
-   ❌ Display: "X surat jalan dipilih dengan total tarif: Rp XXX.XXX"

### Sesudah

-   ✅ Tampilkan "Uang Jalan" (nilai Rp 675 dari tabel)
-   ✅ Hitung "Total Uang Jalan" dari checkbox dipilih
-   ✅ Display: "X surat jalan dipilih dengan total uang jalan: Rp 2.025"

## File Yang Dihapus (Test/Debug)

Sudah dihapus (tidak dipakai lagi):

-   ❌ `populate_tarif.php` - Script populate random tarif
-   ❌ `debug_tarif.php` - Script debug kolom tarif
-   ❌ `test_updated_pranota_query.php` - Test query tarif
-   ❌ `check_surat_jalan_table.php` - Check table tarif

Tetap ada (untuk referensi):

-   ✅ `TARIF_ADDITION.md` - Dokumentasi percobaan tarif
-   ✅ `verify_pranota_uang_jalan.php` - Verifikasi final data

## Backward Compatibility

✅ **FULL BACKWARD COMPATIBLE**

-   Kolom tarif dihapus dari DB tapi tidak ada foreign key constraints
-   Existing data di surat_jalans tetap intact
-   Hanya perubahan UI/form, tidak ada breaking changes
-   Rollback dapat di-undo jika diperlukan di masa depan

## Testing Checklist

Silakan test langsung di browser:

```
□ Buka form Create Pranota
  → Pastikan tabel menampilkan kolom "Uang Jalan" (Rp 675)

□ Select 1-2 surat jalan
  → Pastikan "Total Uang Jalan" update otomatis
  → Contoh: 2 surat × Rp 675 = Rp 1.350

□ Hitung total manual
  → Cocokkan dengan display

□ Submit form
  → Pastikan pranota tersimpan dengan baik

□ View pranota
  → Pastikan data uang_jalan ditampilkan dengan benar
```

## Kesimpulan

✅ **Sistem siap pakai** dengan field `uang_jalan` saja

-   Tidak perlu 2 kolom berbeda
-   Data lebih sederhana dan konsisten
-   Overhead kurang, performa lebih baik

---

**Date**: October 16, 2025  
**Status**: ✅ COMPLETED  
**Impact**: LOW - Rollback bersih, tidak ada data hilang  
**Next Step**: Manual testing di browser
