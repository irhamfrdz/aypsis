# Master Kapal - Dokumentasi Lengkap

## Tanggal: 16 Oktober 2025

## Modul: Master Data Kapal

---

## 📋 Deskripsi

Master Kapal adalah modul untuk mengelola data kapal dalam sistem AYPSIS. Modul ini menyediakan fitur CRUD (Create, Read, Update, Delete) lengkap untuk manajemen data kapal.

---

## 🗂️ Struktur Database

### Tabel: `master_kapals`

| Kolom        | Tipe                     | Constraint                  | Deskripsi                     |
| ------------ | ------------------------ | --------------------------- | ----------------------------- |
| `id`         | BIGINT UNSIGNED          | PRIMARY KEY, AUTO_INCREMENT | ID unik kapal                 |
| `kode`       | VARCHAR(50)              | UNIQUE, NOT NULL            | Kode unik kapal               |
| `kode_kapal` | VARCHAR(100)             | NULLABLE                    | Kode alternatif kapal         |
| `nama_kapal` | VARCHAR(255)             | NOT NULL                    | Nama kapal                    |
| `catatan`    | TEXT                     | NULLABLE                    | Catatan tambahan              |
| `lokasi`     | VARCHAR(255)             | NULLABLE                    | Lokasi kapal saat ini         |
| `status`     | ENUM('aktif','nonaktif') | DEFAULT 'aktif'             | Status kapal                  |
| `created_at` | TIMESTAMP                | NULLABLE                    | Tanggal dibuat                |
| `updated_at` | TIMESTAMP                | NULLABLE                    | Tanggal diupdate              |
| `deleted_at` | TIMESTAMP                | NULLABLE                    | Tanggal dihapus (soft delete) |

**Indexes:**

-   PRIMARY KEY pada `id`
-   UNIQUE INDEX pada `kode`
-   INDEX pada `status`

---

## 📂 File Structure

```
app/
├── Http/Controllers/
│   └── MasterKapalController.php          # Controller utama
└── Models/
    └── MasterKapal.php                     # Model dengan soft delete

database/
├── migrations/
│   └── 2025_10_16_113338_create_master_kapals_table.php
└── seeders/
    └── MasterKapalPermissionSeeder.php     # Seeder permissions

resources/views/
└── master-kapal/
    ├── index.blade.php                     # List view
    ├── create.blade.php                    # Form tambah
    ├── edit.blade.php                      # Form edit
    └── show.blade.php                      # Detail view

routes/
└── web.php                                 # Route definitions
```

---

## 🔐 Permissions

Modul ini menggunakan 4 permissions:

| Permission            | Deskripsi                      | Route         |
| --------------------- | ------------------------------ | ------------- |
| `master-kapal.view`   | Melihat data master kapal      | index, show   |
| `master-kapal.create` | Membuat data master kapal baru | create, store |
| `master-kapal.edit`   | Mengedit data master kapal     | edit, update  |
| `master-kapal.delete` | Menghapus data master kapal    | destroy       |

### Cara Assign Permissions

Jalankan seeder:

```bash
php artisan db:seed --class=MasterKapalPermissionSeeder
```

Atau jalankan script manual:

```bash
php assign_master_kapal_permissions.php
```

---

## 🛣️ Routes

| Method    | URI                       | Name                 | Controller@Action |
| --------- | ------------------------- | -------------------- | ----------------- |
| GET       | `/master-kapal`           | master-kapal.index   | index             |
| GET       | `/master-kapal/create`    | master-kapal.create  | create            |
| POST      | `/master-kapal`           | master-kapal.store   | store             |
| GET       | `/master-kapal/{id}`      | master-kapal.show    | show              |
| GET       | `/master-kapal/{id}/edit` | master-kapal.edit    | edit              |
| PUT/PATCH | `/master-kapal/{id}`      | master-kapal.update  | update            |
| DELETE    | `/master-kapal/{id}`      | master-kapal.destroy | destroy           |

**Middleware:** Semua routes menggunakan `auth` middleware dan permission checks

---

## 🎯 Features

### 1. **List View** (`index.blade.php`)

-   ✅ Pagination (10 items per page)
-   ✅ Search functionality (kode, kode_kapal, nama_kapal, lokasi)
-   ✅ Filter by status (aktif/nonaktif)
-   ✅ Sort functionality
-   ✅ Responsive table with Bootstrap
-   ✅ Action buttons (View, Edit, Delete)
-   ✅ Empty state message
-   ✅ Success/Error alerts with auto-hide

### 2. **Create Form** (`create.blade.php`)

-   ✅ Validation errors display
-   ✅ Form fields:
    -   Kode (required, unique, max 50)
    -   Kode Kapal (optional, max 100)
    -   Nama Kapal (required, max 255)
    -   Lokasi (optional, max 255)
    -   Catatan (optional, textarea)
    -   Status (required, dropdown: aktif/nonaktif)
-   ✅ Breadcrumb navigation
-   ✅ Helper text untuk guidance
-   ✅ Cancel & Save buttons

### 3. **Edit Form** (`edit.blade.php`)

-   ✅ Pre-filled data
-   ✅ Same validation as create
-   ✅ Unique validation excludes current record
-   ✅ Update button

### 4. **Detail View** (`show.blade.php`)

-   ✅ Display all information
-   ✅ Status badges (colored)
-   ✅ Timestamps (created, updated, deleted)
-   ✅ Catatan section (if available)
-   ✅ Danger Zone with delete button
-   ✅ Edit button (if has permission)
-   ✅ Delete confirmation

---

## 💻 Controller Methods

### `index(Request $request)`

**Purpose:** Display list of kapal with search, filter, and pagination

**Parameters:**

-   `search` - Search term (optional)
-   `status` - Filter by status (optional)
-   `sort_by` - Column to sort (default: created_at)
-   `sort_order` - Sort direction (default: desc)

**Returns:** View with paginated `$kapals`

---

### `create()`

**Purpose:** Show form to create new kapal

**Returns:** Create view

---

### `store(Request $request)`

**Purpose:** Save new kapal to database

**Validation:**

-   `kode`: required, string, max:50, unique
-   `kode_kapal`: nullable, string, max:100
-   `nama_kapal`: required, string, max:255
-   `catatan`: nullable, string
-   `lokasi`: nullable, string, max:255
-   `status`: required, in:aktif,nonaktif

**Returns:** Redirect to index with success/error message

---

### `show(MasterKapal $masterKapal)`

**Purpose:** Display detail of specific kapal

**Returns:** Show view with `$masterKapal`

---

### `edit(MasterKapal $masterKapal)`

**Purpose:** Show form to edit kapal

**Returns:** Edit view with `$masterKapal`

---

### `update(Request $request, MasterKapal $masterKapal)`

**Purpose:** Update existing kapal

**Validation:** Same as store, but unique validation excludes current record

**Returns:** Redirect to index with success/error message

---

### `destroy(MasterKapal $masterKapal)`

**Purpose:** Soft delete kapal

**Returns:** Redirect to index with success/error message

---

## 🎨 Model Features

### Traits

-   `SoftDeletes` - Enables soft deletion

### Fillable Fields

```php
[
    'kode',
    'kode_kapal',
    'nama_kapal',
    'catatan',
    'lokasi',
    'status',
]
```

### Scopes

-   `scopeAktif($query)` - Filter only active kapal
-   `scopeNonaktif($query)` - Filter only inactive kapal

### Usage Example:

```php
// Get only active ships
$activeShips = MasterKapal::aktif()->get();

// Get inactive ships
$inactiveShips = MasterKapal::nonaktif()->get();

// Search by name
$ships = MasterKapal::where('nama_kapal', 'like', "%{$search}%")->get();
```

---

## 🎯 Menu Integration

Menu Master Kapal ditambahkan di sidebar setelah "Stock Kontainer" dengan:

-   Icon: Ship/Building icon
-   Permission check: `master-kapal.view`
-   Active state highlighting
-   Responsive design

**Location:** `resources/views/layouts/app.blade.php` line ~660

---

## 📊 Migration Details

**File:** `2025_10_16_113338_create_master_kapals_table.php`

```bash
# Run migration
php artisan migrate

# Rollback
php artisan migrate:rollback
```

---

## ✅ Testing Checklist

-   [x] Migration runs successfully
-   [x] Permissions seeded
-   [x] Routes accessible
-   [x] Index page loads
-   [x] Search functionality works
-   [x] Filter by status works
-   [x] Create form submits
-   [x] Validation works
-   [x] Edit form loads with data
-   [x] Update works
-   [x] Delete with confirmation
-   [x] Soft delete works
-   [x] Menu appears in sidebar
-   [x] Permission checks work
-   [x] Responsive design

---

## 🚀 Usage Examples

### Create New Kapal

1. Login as admin
2. Navigate to Master Data → Master Kapal
3. Click "Tambah Kapal"
4. Fill form:
    - Kode: `KPL001`
    - Nama Kapal: `MV. Sinar Jaya`
    - Lokasi: `Pelabuhan Tanjung Priok`
    - Status: `Aktif`
5. Click "Simpan Data"

### Search Kapal

1. Go to Master Kapal index
2. Type in search box: "Sinar"
3. Click "Cari"
4. Results filtered automatically

### Filter by Status

1. Go to Master Kapal index
2. Select status dropdown: "Aktif"
3. Click "Cari"
4. Only active ships shown

---

## 🔧 Maintenance

### Database Cleanup

```bash
# Hard delete soft-deleted records older than 30 days
php artisan tinker
>>> MasterKapal::onlyTrashed()->where('deleted_at', '<', now()->subDays(30))->forceDelete();
```

### Restore Deleted Kapal

```php
$kapal = MasterKapal::withTrashed()->find($id);
$kapal->restore();
```

---

## 📝 Notes

1. **Kode** harus unique di seluruh sistem
2. **Soft delete** digunakan - data tidak benar-benar dihapus
3. **Status** hanya 2 pilihan: aktif/nonaktif
4. **Lokasi** & **Catatan** bersifat optional
5. **Kode Kapal** adalah alternatif identifier (optional)

---

## 🐛 Known Issues

-   None reported

---

## 📅 Changelog

### Version 1.0.0 - 16 Oktober 2025

-   ✅ Initial release
-   ✅ CRUD functionality
-   ✅ Search & filter
-   ✅ Permissions system
-   ✅ Soft delete
-   ✅ Menu integration
-   ✅ Responsive UI

---

## 👥 Credits

**Developer:** GitHub Copilot & Development Team  
**Date:** 16 Oktober 2025  
**System:** AYPSIS
