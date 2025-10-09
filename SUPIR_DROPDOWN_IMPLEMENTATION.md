# Supir Dropdown Implementation for Pembayaran DP OB & OB

## Overview

Implementasi dropdown supir untuk form pembayaran DP OB dan OB yang mengambil data dari tabel `karyawans` dengan divisi "supir".

## Changes Made

### 1. Controller Updates

#### PembayaranDpObController.php

-   Added `use App\Models\Karyawan;`
-   Updated `create()` method to fetch active supir list
-   Updated `edit()` method to include supir list
-   Changed validation for `supir` field from `string` to `exists:karyawans,id`

#### PembayaranObController.php

-   Added `use App\Models\Karyawan;`
-   Updated `create()` method to fetch active supir list
-   Updated `edit()` method to include supir list
-   Changed validation for `supir` field from `string` to `exists:karyawans,id`

### 2. View Updates

#### create.blade.php (both DP OB and OB)

Changed from text input to dropdown select:

**Before:**

```html
<input
    type="text"
    name="supir"
    id="supir"
    value="{{ old('supir') }}"
    placeholder="Masukkan nama supir"
    required
/>
```

**After:**

```html
<select name="supir"
        id="supir"
        required>
    <option value="">-- Pilih Supir --</option>
    @foreach($supirList as $supir)
        <option value="{{ $supir->id }}" {{ old('supir') == $supir->id ? 'selected' : '' }}>
            {{ $supir->nama_lengkap }} ({{ $supir->nik }})
        </option>
    @endforeach
</select>
```

### 3. Database Query

The supir dropdown fetches data using:

```php
$supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                    ->where('status', 'active') // hanya karyawan aktif
                    ->orderBy('nama_lengkap')
                    ->get();
```

### 4. Current Test Data

Based on test results:

-   Total karyawan: 34
-   Total supir aktif: 1 (JONI, NIK: 123321, ID: 68)

## Features

### ✅ Implemented

-   Case-insensitive divisi search (`LOWER(divisi) = 'supir'`)
-   Only active employees (`status = 'active'`)
-   Sorted by employee name (`ORDER BY nama_lengkap`)
-   Dropdown shows name and NIK format: "Nama Lengkap (NIK)"
-   Form validation ensures selected ID exists in database
-   Old input value preservation on validation errors

### 🔄 Future Implementation Notes

When implementing actual database models, consider:

1. **Display in Index Tables**: Need to join with karyawan table to show supir name

    ```php
    // In model or controller
    public function supir()
    {
        return $this->belongsTo(Karyawan::class, 'supir', 'id');
    }

    // In view
    {{ $pembayaran->supir->nama_lengkap ?? 'Supir tidak ditemukan' }}
    ```

2. **Search Functionality**: Update search to work with supir names

    ```php
    // In controller index method
    if ($request->filled('supir')) {
        $query->whereHas('supir', function($q) use ($request) {
            $q->where('nama_lengkap', 'like', '%' . $request->supir . '%');
        });
    }
    ```

3. **Data Storage**: Supir field now stores `karyawan.id` instead of text

## Testing

Run the test script to verify functionality:

```bash
php test_supir_dropdown.php
```

## Usage

1. Navigate to Pembayaran DP OB Create: `/pembayaran-dp-ob/create`
2. Navigate to Pembayaran OB Create: `/pembayaran-ob/create`
3. Select supir from dropdown showing "JONI (123321)"
4. Submit form - validation will check if selected ID exists in karyawans table

## Error Handling

-   If no supir found: Dropdown shows only "-- Pilih Supir --" option
-   If validation fails: Dropdown retains selected value
-   Database constraint: `exists:karyawans,id` ensures data integrity

## Dependencies

-   App\Models\Karyawan model
-   karyawans table with columns: id, nama_lengkap, nik, divisi, status
-   Active karyawan records with divisi = 'SUPIR' (case-insensitive)
