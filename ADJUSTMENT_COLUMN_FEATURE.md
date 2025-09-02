# ADJUSTMENT COLUMN FEATURE

## Overview

Fitur kolom Adjustment telah berhasil ditambahkan ke tabel `daftar_tagihan_kontainer_sewa` untuk memungkinkan penyesuaian harga DPP secara manual. Fitur ini memungkinkan pengguna untuk menambah atau mengurangi nilai DPP tanpa mengubah nilai DPP asli.

## Features Implemented

### 1. Database Structure

-   **Column**: `adjustment` (DECIMAL 15,2)
-   **Default**: 0
-   **Position**: Setelah kolom `dpp`
-   **Allow**: Nilai positif dan negatif
-   **Range**: -999,999,999.99 hingga +999,999,999.99

### 2. User Interface

-   **Header**: Kolom "Adjustment" dengan tooltip penjelasan
-   **Display**: Menampilkan nilai adjustment dengan format:
    -   Positif: `+Rp 100,000` (hijau) dengan label "↗ Penambahan"
    -   Negatif: `-Rp 50,000` (merah) dengan label "↘ Pengurangan"
    -   Kosong: `-` (abu-abu) dengan label "Tidak ada"
-   **Edit Button**: Muncul saat hover dengan ikon edit
-   **Styling**: Background cyan untuk membedakan dari kolom DPP

### 3. Functionality

-   **Inline Editing**: Click edit button untuk mengubah nilai
-   **Validation**: Validasi range nilai dan format number
-   **AJAX Update**: Update nilai tanpa reload halaman penuh
-   **Real-time Display**: Nilai langsung diupdate setelah sukses

### 4. Backend Implementation

-   **Route**: `PATCH /daftar-tagihan-kontainer-sewa/{id}/adjustment`
-   **Controller**: `DaftarTagihanKontainerSewaController@updateAdjustment`
-   **Validation**: Request validation untuk nilai adjustment
-   **Logging**: Audit trail untuk perubahan adjustment
-   **Error Handling**: Try-catch dengan feedback ke user

## How to Use

### 1. Viewing Adjustment

-   Kolom Adjustment berada di sebelah kolom DPP
-   Nilai ditampilkan dengan format currency
-   Warna menunjukkan jenis adjustment (positif/negatif)

### 2. Editing Adjustment

1. Hover mouse ke kolom Adjustment
2. Click tombol edit yang muncul
3. Masukkan nilai adjustment di prompt:
    - Untuk penambahan: masukkan angka positif (contoh: `100000`)
    - Untuk pengurangan: masukkan angka negatif (contoh: `-50000`)
    - Untuk reset: masukkan `0`
4. Click OK untuk menyimpan
5. Halaman akan refresh untuk menampilkan nilai baru

### 3. Calculation Impact

-   **Original DPP**: Rp 775,000
-   **Adjustment**: +Rp 100,000
-   **Effective DPP**: Rp 875,000
-   **PPN (11%)**: Rp 96,250 (dari effective DPP)

UPDATED! Sekarang adjustment benar-benar berdampak pada perhitungan:

-   **Real-time Calculation**: Adjustment langsung mempengaruhi perhitungan
-   **PPN Recalculation**: PPN dihitung dari (DPP + Adjustment) × 11%
-   **PPH Recalculation**: PPH dihitung dari (DPP + Adjustment) × 2%
-   **Grand Total Update**: Total baru = Adjusted DPP + DPP Nilai Lain + New PPN - New PPH
-   **Visual Indicators**: Menampilkan "Dihitung dari DPP yang disesuaikan"
-   **Impact Display**: Menampilkan selisih dari total asli (+Rp XXX dari asli)

## Technical Details

### Database Migration

```sql
ALTER TABLE daftar_tagihan_kontainer_sewa
ADD COLUMN adjustment DECIMAL(15,2) DEFAULT 0
AFTER dpp
COMMENT 'Penyesuaian harga DPP (bisa positif atau negatif)';
```

### Model Updates

```php
protected $fillable = [
    // ... other fields
    'adjustment',
];

protected $casts = [
    // ... other casts
    'adjustment' => 'decimal:2',
];
```

### Route Definition

```php
Route::patch('daftar-tagihan-kontainer-sewa/{id}/adjustment', [
    DaftarTagihanKontainerSewaController::class, 'updateAdjustment'
])->name('daftar-tagihan-kontainer-sewa.adjustment.update');
```

### Controller Method

```php
public function updateAdjustment(Request $request, $id)
{
    $request->validate([
        'adjustment' => 'required|numeric|between:-999999999.99,999999999.99',
    ]);

    $tagihan = DaftarTagihanKontainerSewa::findOrFail($id);
    $tagihan->adjustment = $request->input('adjustment');
    $tagihan->save();

    // Logging and response...
}
```

## Files Modified

### 1. Database

-   `database/migrations/2025_09_01_153409_add_adjustment_to_daftar_tagihan_kontainer_sewa_table.php`

### 2. Model

-   `app/Models/DaftarTagihanKontainerSewa.php`
    -   Added `adjustment` to `$fillable`
    -   Added `adjustment` to `$casts`

### 3. Controller

-   `app/Http/Controllers/DaftarTagihanKontainerSewaController.php`
    -   Added `updateAdjustment()` method
    -   Added imports for `Log` and `Auth`

### 4. Routes

-   `routes/web.php`
    -   Added PATCH route for adjustment updates

### 5. View

-   `resources/views/daftar-tagihan-kontainer-sewa/index.blade.php`
    -   Added adjustment column header
    -   Added adjustment column data display
    -   Added JavaScript editAdjustment function
    -   Updated table width and colspan

## Testing

### Test Files Created

1. `test_adjustment_column.php` - Basic column existence test
2. `test_adjustment_functionality.php` - Complete functionality test

### Test Results

✅ Database column exists  
✅ Migration executed successfully  
✅ Model fillable and casts updated  
✅ Controller method implemented  
✅ Route registered correctly  
✅ View displays adjustment data  
✅ JavaScript edit function works  
✅ Sample data with adjustments created

### Sample Data

-   ID 1: +Rp 100,000 (Penambahan)
-   ID 2: -Rp 50,000 (Pengurangan)
-   ID 3: +Rp 75,000 (Penambahan)
-   ID 4: -Rp 25,000 (Pengurangan)
-   ID 5: +Rp 150,000 (Penambahan)

## Usage Examples

### Case 1: Price Increase

-   Original DPP: Rp 500,000
-   Market rate increase: +Rp 25,000
-   Adjustment: +25000
-   New effective DPP: Rp 525,000

### Case 2: Discount Applied

-   Original DPP: Rp 800,000
-   Customer discount: -Rp 100,000
-   Adjustment: -100000
-   New effective DPP: Rp 700,000

### Case 3: Correction

-   Original DPP: Rp 750,000 (wrong calculation)
-   Correct amount should be: Rp 725,000
-   Adjustment: -25000
-   New effective DPP: Rp 725,000

## Future Enhancements

### Potential Improvements

1. **Adjustment History**: Track all adjustment changes with timestamps
2. **Bulk Adjustment**: Apply adjustment to multiple records at once
3. **Adjustment Types**: Categorize adjustments (discount, surcharge, correction, etc.)
4. **Approval Workflow**: Require approval for large adjustments
5. **Automatic Recalculation**: Auto-update grand_total when adjustment changes
6. **Adjustment Reports**: Generate reports showing all adjustments made

### Implementation Notes

-   Currently, adjustment affects effective DPP for display but doesn't auto-recalculate other fields
-   Grand total recalculation can be added if needed
-   Adjustment changes are logged for audit purposes
-   UI is responsive and mobile-friendly

## Conclusion

Kolom Adjustment telah berhasil diimplementasikan dengan lengkap dan siap digunakan. Fitur ini memungkinkan penyesuaian harga DPP yang fleksibel dengan interface yang user-friendly dan backend yang robust.
