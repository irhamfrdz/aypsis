# Migration Organization Guide

## Folder Structure

Folder `database/migrations/` telah diorganisir dengan struktur berikut:

### 📂 Root Directory

Hanya berisi migrasi terbaru yang **aktif** dan perlu dijalankan:

-   `2025_10_03_142344_create_aktivitas_lainnya_table.php`
-   `2025_10_03_142401_create_pembayaran_aktivitas_lainnya_table.php`
-   `2025_10_03_142416_create_pembayaran_aktivitas_lainnya_items_table.php`

### 📁 Archived Directory Structure

```
archived/
├── 01_core_system/          # Laravel core tables (1 file)
├── 02_users_auth/           # Authentication & user management (17 files)
├── 03_master_data/          # Master data tables (62 files)
├── 04_permohonan_system/    # Permission/request system (5 files)
├── 05_pranota_system/       # Pranota management (35 files)
├── 06_pembayaran_system/    # Payment system (2 files)
├── 07_tagihan_system/       # Billing & invoice system (24 files)
├── 08_perbaikan_system/     # Repair & maintenance (2 files)
├── 09_accounting_system/    # Accounting & financial (19 files)
└── 99_misc_updates/         # Miscellaneous updates (17 files)
```

## Benefits

### ✅ **Improved Organization**

-   Migration files grouped by functionality
-   Easy to find related migrations
-   Better code maintainability

### ✅ **Performance**

-   Laravel only scans root directory for new migrations
-   Faster migration discovery
-   Reduced file clutter

### ✅ **Team Collaboration**

-   Clear categorization for team members
-   Easier to review migration history
-   Better understanding of system evolution

## Usage Guidelines

### For New Migrations

1. **Create in root**: New migration files should be created in the root `migrations/` directory
2. **Run normally**: Use `php artisan make:migration` as usual
3. **Archive periodically**: Move old migrations to appropriate archived folders

### For Viewing History

1. **Check archived folders** for historical migrations
2. **Read README.md** in each folder for context
3. **Use INDEX.md** for overview

### Important Notes

-   ⚠️ **Never modify archived migrations** - they are historical records
-   ⚠️ **Don't move running migrations** - only move completed ones
-   ✅ **Keep root clean** - only active migrations should be in root

## Maintenance

To maintain this organization:

1. **Weekly cleanup**: Move old migrations to archived folders
2. **Update documentation**: Keep README files current
3. **Review categories**: Ensure proper categorization

---

_Last organized: October 3, 2025_
