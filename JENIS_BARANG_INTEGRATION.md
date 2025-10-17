# Jenis Barang Integration for Tanda Terima

## Overview

Added `jenis_barang` (item/goods type) field to Tanda Terima module. This field is automatically copied from Surat Jalan during approval process and displayed in all Tanda Terima views.

## Changes Made

### 1. Database Migration

**File**: `database/migrations/2025_10_16_142049_add_jenis_barang_to_tanda_terimas_table.php`

```php
Schema::table('tanda_terimas', function (Blueprint $table) {
    $table->string('jenis_barang')->nullable()->after('kegiatan');
});
```

**Details**:

-   Column type: `VARCHAR(255)`
-   Nullable: YES
-   Position: After `kegiatan` column
-   Status: ✅ Migrated successfully (70.90ms)

### 2. Model Update

**File**: `app/Models/TandaTerima.php`

Added `jenis_barang` to `$fillable` array:

```php
protected $fillable = [
    // ... existing fields
    'kegiatan',
    'jenis_barang',  // ← NEW
    'size',
    // ... rest of fields
];
```

### 3. Controller Update

**File**: `app/Http/Controllers/SuratJalanApprovalController.php`

Updated `createTandaTerima()` method to include jenis_barang:

```php
TandaTerima::create([
    'surat_jalan_id' => $suratJalan->id,
    'no_surat_jalan' => $suratJalan->no_surat_jalan,
    // ... other fields
    'kegiatan' => $suratJalan->kegiatan,
    'jenis_barang' => $suratJalan->jenis_barang,  // ← NEW
    'size' => $suratJalan->size,
    // ... rest
]);
```

### 4. View Updates

#### A. Index View (`index.blade.php`)

**Changes**:

-   Added "Jenis Barang" column header
-   Display jenis_barang with purple badge styling
-   Position: Between "Nama Kapal" and "Kegiatan" columns

**Display**:

```blade
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
    {{ $tandaTerima->jenis_barang ?: '-' }}
</span>
```

#### B. Show View (`show.blade.php`)

**Changes**:

-   Added "Jenis Barang" field in Informasi Surat Jalan section
-   Purple badge styling matching index view
-   Position: After "Supir" and before "Kegiatan"

#### C. Edit View (`edit.blade.php`)

**Changes**:

-   Added read-only "Jenis Barang" display in right sidebar
-   Purple badge styling for consistency
-   Position: In "Informasi Surat Jalan" card after "Supir"

## Data Flow

```
┌─────────────────┐
│  Surat Jalan    │
│  (jenis_barang) │
└────────┬────────┘
         │
         │ Approval Process
         │ (Auto-copy on final approval)
         ▼
┌─────────────────┐
│  Tanda Terima   │
│  (jenis_barang) │
└─────────────────┘
         │
         │ Display in Views
         ▼
    ┌───────────┐
    │  Index    │
    │  Show     │
    │  Edit     │
    └───────────┘
```

## Visual Design

### Badge Styling

-   **Color**: Purple (bg-purple-100, text-purple-800)
-   **Shape**: Rounded full pill
-   **Size**: Text-xs, padding 2.5px x 0.5px
-   **Display**: Inline-flex with items-center

### Rationale for Purple

-   **Jenis Barang**: Purple badge (NEW)
-   **Kegiatan**: Indigo badge (existing)
-   **Status**: Green/Blue/Gray badges (existing)
-   Provides clear visual distinction between different data types

## Testing Results

### Database

✅ Column created successfully
✅ Type: VARCHAR(255), Nullable: YES
✅ Position: After kegiatan

### Model

✅ Added to $fillable array
✅ No casting needed (string type)

### Controller

✅ Includes jenis_barang in TandaTerima::create()
✅ Auto-copies from surat_jalan on approval

### Views

✅ index.blade.php - Displays in table
✅ show.blade.php - Displays in detail view
✅ edit.blade.php - Displays as read-only

### Data Migration

✅ Updated 2 existing records

-   TT SJ0005: Elektronik Manual
-   TT SJ00006: Elektronik Manual

## User Guide

### For Admins

When a Surat Jalan is fully approved:

1. System automatically creates Tanda Terima
2. `jenis_barang` is copied from Surat Jalan
3. Value is read-only in Tanda Terima (inherited data)

### For Users

**Viewing Jenis Barang**:

-   **List View**: Purple badge in table
-   **Detail View**: Purple badge in sidebar
-   **Edit View**: Purple badge (read-only)

**Manual Tanda Terima**:

-   For manual tanda terima (without surat jalan), jenis_barang will be null
-   Shows "-" in all views

## Database Schema

```sql
ALTER TABLE tanda_terimas
ADD COLUMN jenis_barang VARCHAR(255) NULL
AFTER kegiatan;
```

## API / Data Structure

### TandaTerima Model

```php
[
    'no_surat_jalan' => 'SJ00006',
    'kegiatan' => 'KEG001',
    'jenis_barang' => 'Elektronik Manual',  // ← NEW
    'size' => '40',
    // ... other fields
]
```

## Backward Compatibility

✅ Existing tanda terima records updated with jenis_barang from their surat jalan
✅ Nullable column - no breaking changes
✅ Old records show "-" if no jenis_barang available

## Future Enhancements

-   [ ] Add jenis_barang filter in index view
-   [ ] Add jenis_barang to create.blade.php for manual input
-   [ ] Add validation for jenis_barang format
-   [ ] Create master table for jenis_barang (dropdown instead of free text)

## Related Files

```
database/migrations/2025_10_16_142049_add_jenis_barang_to_tanda_terimas_table.php
app/Models/TandaTerima.php
app/Http/Controllers/SuratJalanApprovalController.php
resources/views/tanda-terima/index.blade.php
resources/views/tanda-terima/show.blade.php
resources/views/tanda-terima/edit.blade.php
```

## Testing Scripts

-   `test_jenis_barang_integration.php` - Verify integration
-   `update_existing_tanda_terima_jenis_barang.php` - Migrate existing data

---

**Date**: October 16, 2025  
**Impact**: Enhanced data completeness in Tanda Terima module  
**Status**: ✅ Completed and Tested
