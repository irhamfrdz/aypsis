# Fix Master Kapals Migration Error (Server Compatible)

## Problem

Migration `2025_10_16_160202_add_capacity_fields_to_master_kapals_table` failed with error:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'lokasi' in 'master_kapals'
```

## Root Cause

The migration tried to add columns after a reference column that doesn't exist on the server. Different environments have different table structures:

-   **Local**: Has `pelayaran` column (from previous migrations)
-   **Server**: May have `lokasi` column or different structure entirely

## Solution

The migration file has been updated to:

1. **Detect existing columns** dynamically
2. **Choose best reference column** (pelayaran > lokasi > catatan > nama_kapal)
3. **Skip existing columns** to prevent duplicate column errors
4. **Handle various table structures** across different environments

## How to Fix on Server

### Option 1: PHP Script Fix (⭐ RECOMMENDED)

```bash
# Run the intelligent PHP script
php fix_master_kapals_migration.php
```

**This script will:**

-   ✅ Analyze current table structure
-   ✅ Detect available reference columns
-   ✅ Add only missing columns
-   ✅ Handle failed migration records
-   ✅ Verify final structure

### Option 2: SQL Manual Fix (For direct database access)

```sql
-- Run the smart SQL script
mysql -u username -p database_name < fix_master_kapals_manual.sql
```

### Option 3: Rollback and Re-migrate (Development only)

```bash
# Run the bash script (only if no production data)
chmod +x fix_master_kapals_migration.sh
./fix_master_kapals_migration.sh
```

## Migration Logic Flow

```
1. Check table structure:
   ├── Has 'pelayaran'? → Use as reference
   ├── Has 'lokasi'? → Use as reference
   ├── Has 'catatan'? → Use as reference
   └── Default → Add at end

2. For each target column:
   ├── Already exists? → Skip
   └── Missing? → Add with smart positioning

3. Handle migration record:
   ├── Failed entry? → Remove and re-run
   └── Success → Verify structure
```

## Expected Table Structure After Fix

The final structure will vary by server but should include:

```sql
master_kapals:
- id (bigint unsigned)
- kode (varchar 50, unique)
- kode_kapal (varchar 100, nullable)
- nama_kapal (varchar 255)
- [nickname] (varchar 255, nullable) -- May exist
- catatan (text, nullable)
- [lokasi OR pelayaran] (varchar 255, nullable) -- Server dependent
- kapasitas_kontainer_palka (int, nullable) -- ✅ NEW
- kapasitas_kontainer_deck (int, nullable)  -- ✅ NEW
- gross_tonnage (decimal 12,2, nullable)    -- ✅ NEW
- status (enum: aktif/nonaktif, default aktif)
- created_at, updated_at, deleted_at (timestamps)
```

## Verification Commands

```bash
# Check migration status
php artisan migrate:status

# Verify table structure
php artisan tinker --execute="print_r(DB::select('DESCRIBE master_kapals'));"

# Test the fix script locally first
php fix_master_kapals_migration.php
```

## Error Prevention

The updated migration now:

-   ✅ **Detects column existence** before adding
-   ✅ **Chooses available reference columns** dynamically
-   ✅ **Handles multiple table structures**
-   ✅ **Provides detailed logging** for troubleshooting
-   ✅ **Includes cleanup** for failed attempts

## Files Updated

1. `database/migrations/2025_10_16_160202_add_capacity_fields_to_master_kapals_table.php` - Smart migration
2. `fix_master_kapals_migration.php` - Enhanced PHP fix script
3. `fix_master_kapals_manual.sql` - Conditional SQL statements
4. `fix_master_kapals_migration.sh` - Bash script (dev environments)

## For Server Deployment

**Use the PHP script** - it's the most robust and provides detailed feedback:

```bash
php fix_master_kapals_migration.php
```
