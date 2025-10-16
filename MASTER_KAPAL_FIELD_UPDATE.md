# Master Kapal - Update Field Documentation

## Changes Made (October 16, 2025)

### Database Schema Updates

**Migration:** `2025_10_16_131735_update_master_kapals_add_nickname_and_change_lokasi_to_pelayaran`

#### 1. Field Renamed

-   **Old:** `lokasi` (VARCHAR 255) - Previously used for location/port
-   **New:** `pelayaran` (VARCHAR 255) - Now stores shipping company/vessel owner

**Rationale:** More accurately reflects the business requirement to track vessel ownership rather than generic location.

#### 2. Field Added

-   **New:** `nickname` (VARCHAR 255, nullable) - Vessel nickname/short name
-   **Position:** After `nama_kapal`

**Purpose:** Allow users to set convenient short names/nicknames for vessels (e.g., "SEJAHTERA" for "MV SEJAHTERA RAYA")

### Updated Database Structure

```sql
CREATE TABLE master_kapals (
    id BIGINT UNSIGNED PRIMARY KEY,
    kode VARCHAR(50) UNIQUE NOT NULL,
    kode_kapal VARCHAR(100),
    nama_kapal VARCHAR(255) NOT NULL,
    nickname VARCHAR(255),              -- NEW
    pelayaran VARCHAR(255),             -- RENAMED from 'lokasi'
    catatan TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### Model Changes

**File:** `app/Models/MasterKapal.php`

```php
protected $fillable = [
    'kode',
    'kode_kapal',
    'nama_kapal',
    'nickname',        // Added
    'pelayaran',       // Changed from 'lokasi'
    'catatan',
    'status',
];
```

### Controller Validation Updates

**File:** `app/Http/Controllers/MasterKapalController.php`

#### Store Method

```php
$validated = $request->validate([
    'kode' => 'required|string|max:50|unique:master_kapals,kode',
    'kode_kapal' => 'nullable|string|max:100',
    'nama_kapal' => 'required|string|max:255',
    'nickname' => 'nullable|string|max:255',      // Added
    'pelayaran' => 'nullable|string|max:255',     // Changed from 'lokasi'
    'catatan' => 'nullable|string',
    'status' => 'required|in:aktif,nonaktif',
]);
```

#### Update Method

Same validation rules as Store method.

#### CSV Import Method

Updated to support new fields:

**CSV Header (Old):**

```
kode;kode_kapal;nama_kapal;lokasi;catatan;status
```

**CSV Header (New):**

```
kode;kode_kapal;nama_kapal;nickname;pelayaran;catatan;status
```

### View Changes

#### 1. Index View (`index.blade.php`)

**Changed columns:**

-   Column 4: "Lokasi" → "Pelayaran (Pemilik)"
-   Added Column: "Nickname" (between Nama Kapal and Pelayaran)

**Table structure:**

```
No | Kode | Kode Kapal | Nama Kapal | Nickname | Pelayaran (Pemilik) | Catatan | Status | Aksi
```

#### 2. Create View (`create.blade.php`)

**Added fields:**

-   Nickname input (optional, with helper text)
-   Pelayaran input (optional, labeled as "Pelayaran (Pemilik Kapal)")

**Removed fields:**

-   Lokasi input

**Layout:** Fields arranged in 2-column grid for better UX

#### 3. Edit View (`edit.blade.php`)

Same changes as Create view, with pre-filled values.

#### 4. Show View (`show.blade.php`)

**Display changes:**

-   Shows "Nickname" with purple badge if exists
-   Shows "Pelayaran (Pemilik)" with ship icon
-   Removed "Lokasi" display

### CSV Template

**New Template:** `public/templates/master_kapal_template.csv`

**Example Data:**

```csv
kode;kode_kapal;nama_kapal;nickname;pelayaran;catatan;status
K001;KP-001;MV SEJAHTERA;SEJAHTERA;PT Pelayaran Indonesia;Kapal kontainer 20 feet;aktif
K002;KP-002;MV NUSANTARA;NUSA;PT Samudera Lines;Kapal cargo besar;aktif
K003;KP-003;MV BAHARI;;PT Pelni;Kapal penumpang;nonaktif
```

**Notes:**

-   Nickname and Pelayaran can be empty (leave blank between semicolons)
-   All other validation rules remain the same

### Migration Rollback

If needed, migration can be rolled back:

```bash
php artisan migrate:rollback
```

This will:

1. Remove the `nickname` column
2. Rename `pelayaran` back to `lokasi`

### Data Migration Considerations

**Important:** Existing data in `lokasi` field is automatically renamed to `pelayaran`.

**If you need to preserve old lokasi data:**

1. Before migration, export current data
2. Run migration
3. Manually update records where pelayaran doesn't match expected shipping company names

### Testing Steps

1. ✅ Run migration
2. ✅ Test Create form with new fields
3. ✅ Test Edit form with pre-filled values
4. ✅ Test CSV import with new template
5. ✅ Verify Index table displays correctly
6. ✅ Verify Show page displays new fields

### UI/UX Enhancements

-   **Nickname Badge:** Purple badge in Show view for visual distinction
-   **Pelayaran Icon:** Ship icon (fa-ship) for shipping company field
-   **Helper Text:** Clear instructions on form fields
-   **Column Order:** Logical flow from Nama → Nickname → Pelayaran

### Example Usage

**Scenario:** Adding a new vessel

```
Kode: K005
Kode Kapal: KP-005
Nama Kapal: MV SINAR JAYA RAYA
Nickname: SINAR JAYA (short/convenient name)
Pelayaran: PT Anugrah Pelayaran (owner company)
Catatan: Kapal tanker minyak
Status: aktif
```

**Benefits:**

-   Easy identification by nickname in reports
-   Clear ownership tracking via pelayaran field
-   Better data organization for shipping operations

### Backward Compatibility

⚠️ **Breaking Changes:**

-   CSV import with old format will fail (header validation)
-   API/forms expecting `lokasi` field will receive validation errors

**Action Required:**

-   Update all CSV files to new template
-   Update any external integrations to use `pelayaran` instead of `lokasi`
-   Update any custom scripts accessing the `lokasi` field

---

**Migration Date:** October 16, 2025  
**Status:** ✅ Completed  
**Impact:** All Master Kapal CRUD operations updated
