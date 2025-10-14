# TANGGAL BERANGKAT FIELD UPDATES

## Summary

Removed "jam berangkat" field and converted "waktu berangkat" to "tanggal berangkat" with date input type.

## Changes Made

### 1. View Updates

**File: `resources/views/surat-jalan/create.blade.php`**

#### Removed Field

-   **jam_berangkat**: Completely removed the time input field from the form

#### Updated Field

-   **waktu_berangkat** → **tanggal_berangkat**
    -   Changed input type from `datetime-local` to `date`
    -   Updated label from "Waktu Berangkat (Full DateTime)" to "Tanggal Berangkat"
    -   Updated field name from `waktu_berangkat` to `tanggal_berangkat`
    -   Updated error validation from `@error('waktu_berangkat')` to `@error('tanggal_berangkat')`

### 2. Controller Updates

**File: `app/Http/Controllers/SuratJalanController.php`**

#### store() Method Validation

-   **Removed**: `'jam_berangkat' => 'nullable|date_format:H:i'`
-   **Updated**: `'waktu_berangkat' => 'nullable|date'` → `'tanggal_berangkat' => 'nullable|date'`

#### update() Method Validation

-   **Removed**: `'jam_berangkat' => 'nullable|date_format:H:i'`
-   **Updated**: `'waktu_berangkat' => 'nullable|date'` → `'tanggal_berangkat' => 'nullable|date'`

## Technical Details

### Before Changes

```blade
<!-- Jam Berangkat Field (REMOVED) -->
<div>
    <label>Jam Berangkat</label>
    <input type="time" name="jam_berangkat" ...>
</div>

<!-- Waktu Berangkat Field (UPDATED) -->
<div>
    <label>Waktu Berangkat (Full DateTime)</label>
    <input type="datetime-local" name="waktu_berangkat" ...>
</div>
```

### After Changes

```blade
<!-- Tanggal Berangkat Field (NEW) -->
<div>
    <label>Tanggal Berangkat</label>
    <input type="date" name="tanggal_berangkat" ...>
</div>
```

### Validation Rules Before

```php
'jam_berangkat' => 'nullable|date_format:H:i',
'waktu_berangkat' => 'nullable|date',
```

### Validation Rules After

```php
'tanggal_berangkat' => 'nullable|date',
```

## Benefits

1. **Simplified User Experience**: Single date field instead of separate date/time fields
2. **Cleaner Form**: Reduced form complexity by removing redundant time field
3. **Consistency**: Matches other date fields in the form (tanggal_muat, tanggal_surat_jalan)
4. **Database Compatibility**: Date field is easier to work with for scheduling and reporting

## Database Considerations

-   The database column should be updated to handle the new field name
-   Consider migration to rename `waktu_berangkat` to `tanggal_berangkat` if needed
-   Ensure existing data is compatible with date-only format

## Usage

1. User selects departure date using date picker
2. Form validates the date format
3. Data is stored as date in the database
4. Simpler querying and filtering by departure dates

## Migration Recommendation

If you want to update the database column name, create a migration:

```php
Schema::table('surat_jalans', function (Blueprint $table) {
    $table->renameColumn('waktu_berangkat', 'tanggal_berangkat');
    $table->dropColumn('jam_berangkat');
});
```
