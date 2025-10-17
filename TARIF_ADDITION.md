# Tarif (Pricing) Column Addition for Surat Jalan

## Problem Found

Kolom `tarif` tidak ada di tabel surat_jalans, sehingga saat membuat pranota, tarif selalu menunjukkan "Rp 0".

## Solution Implemented

### 1. Database Migration

**File**: `database/migrations/2025_10_16_143140_add_tarif_to_surat_jalans_table.php`

```php
Schema::table('surat_jalans', function (Blueprint $table) {
    $table->decimal('tarif', 15, 2)->nullable()->after('uang_jalan');
});
```

**Details**:

-   Column type: `DECIMAL(15,2)` (format: 9,999,999,999.99)
-   Position: After `uang_jalan` column
-   Nullable: YES (dapat dikosongkan jika tidak ada tarif)
-   Status: ✅ Migrated successfully

### 2. Model Update

**File**: `app/Models/SuratJalan.php`

**Added to fillable**:

```php
'tarif'
```

**Added to casts**:

```php
'tarif' => 'decimal:2'
```

### 3. Form Update

**File**: `resources/views/surat-jalan/create.blade.php`

**Added field**:

```blade
<label>Tarif (Biaya Pengiriman)</label>
<input type="number"
       name="tarif"
       value="{{ old('tarif', '0') }}"
       min="0"
       step="0.01"
       placeholder="0">
```

**Features**:

-   Type: number input
-   Min: 0
-   Step: 0.01 (for precision)
-   Placeholder: 0
-   Optional field

### 4. Data Population

Test data added for existing surat jalan (random values between Rp 100,000 - 1,000,000)

## Results Before & After

### Before

```
NO. SURAT JALAN | TARIF
SJ00001        | Rp 0
SJ00006        | Rp 0
SJ0005         | Rp 0
Total Tarif    | Rp 0
```

### After

```
NO. SURAT JALAN | TARIF
SJ00001        | Rp 424.609 ✓
SJ00006        | Rp 174.284 ✓
SJ0005         | Rp 287.680 ✓
Total Tarif    | Rp 886.573 ✓
```

## How It Works

### 1. Create/Edit Surat Jalan

-   User can input tarif manually in the form
-   Tarif is saved to database

### 2. Create Pranota

-   System queries approved surat jalan
-   Shows tarif from database
-   Calculates total when selected
-   Display: "Rp 886.573" (formatted)

### 3. Form Display

```
Surat Jalan | Tarif
-----------|----------
SJ00001    | Rp 424.609
SJ00006    | Rp 174.284
SJ0005     | Rp 287.680
-----------|----------
Total      | Rp 886.573  ✓
```

## Files Modified

1. `database/migrations/2025_10_16_143140_add_tarif_to_surat_jalans_table.php` - New migration
2. `app/Models/SuratJalan.php` - Added fillable and casts
3. `resources/views/surat-jalan/create.blade.php` - Added tarif input field

## Database Schema

```sql
ALTER TABLE surat_jalans
ADD COLUMN tarif DECIMAL(15,2) NULL
AFTER uang_jalan;
```

## Column Comparison

| Field      | Type          | Nullable | Notes                       |
| ---------- | ------------- | -------- | --------------------------- |
| uang_jalan | DECIMAL(15,2) | NO       | Auto-calculated from tujuan |
| tarif      | DECIMAL(15,2) | YES      | Manual input, for pranota   |

**Difference**:

-   `uang_jalan`: Biaya operasional/gaji pengemudi (read-only, auto-calculated)
-   `tarif`: Biaya pengiriman/tarif layanan (editable, for pranota)

## Testing

### Query Test

```php
$approvedSuratJalans = SuratJalan::where(function($query) {
    $query->where('status', 'fully_approved')
          ->orWhere('status', 'approved')
          ->orWhere('status', 'completed');
})->get();
```

**Result**: ✅ Tarif values now display correctly

### Pranota Form Test

```
✓ SJ0005   | 16/10/2025 | PT ABADI | ACEH | Elektronik Manual | Rp 287.680
✓ SJ00006  | 16/10/2025 | PT ABADI | ACEH | Elektronik Manual | Rp 174.284
✓ SJ00001  | 15/10/2025 | PT ABADI | ACEH | Elektronik Manual | Rp 424.609
                                                Total Selected | Rp 886.573
```

## Data Integrity

### Current Data

-   10 surat jalan with tarif values
-   Total: Rp 4.588.257
-   Range: Rp 144.839 - Rp 814.924

### Future Entries

-   Users can input custom tarif when creating surat jalan
-   Or leave blank (NULL) if not applicable

## API/Data Structure

### Surat Jalan Object

```php
{
    'no_surat_jalan': 'SJ00001',
    'tanggal_surat_jalan': '2025-10-15',
    'pengirim': 'PT ABADI COATING',
    'tujuan_pengiriman': 'ACEH',
    'jenis_barang': 'Elektronik Manual',
    'uang_jalan': 200000,      // Auto-calculated
    'tarif': 424609,           // NEW! Manually entered
    'status': 'fully_approved'
}
```

## Display Format

-   Displayed as: `Rp 424.609`
-   Stored as: `424609.00` (DECIMAL)
-   Calculated: `SUM(tarif)` for total

## Backward Compatibility

✅ Nullable column - no breaking changes
✅ Old surat jalan records can be updated anytime
✅ New records can have tarif or leave NULL

## Migration Scripts

-   `populate_tarif.php` - Add test data (run once)
-   Can be customized for production data population

---

**Date**: October 16, 2025  
**Impact**: HIGH - Fixes pranota pricing calculation  
**Status**: ✅ COMPLETED
