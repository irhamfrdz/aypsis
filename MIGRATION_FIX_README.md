# Fix Master Kapals Migration Error

## Problem
Migration `2025_10_16_160202_add_capacity_fields_to_master_kapals_table` failed with error:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'pelayaran' in 'master_kapals'
```

## Root Cause
The migration tried to add columns after a column named `pelayaran` which doesn't exist in the `master_kapals` table.

## Solution
The migration file has been fixed to use `after('lokasi')` instead of `after('pelayaran')`.

## How to Fix on Server

### Option 1: Rollback and Re-migrate (Recommended for dev/staging)
```bash
# Run the bash script
chmod +x fix_master_kapals_migration.sh
./fix_master_kapals_migration.sh
```

### Option 2: Manual SQL Fix (For production with existing data)
```sql
-- Run the SQL commands in fix_master_kapals_manual.sql
mysql -u username -p database_name < fix_master_kapals_manual.sql
```

### Option 3: PHP Script Fix (Most flexible)
```bash
# Run the PHP script
php fix_master_kapals_migration.php
```

## Expected Table Structure After Fix
```sql
master_kapals:
- id (bigint unsigned)
- kode (varchar 50, unique)
- kode_kapal (varchar 100, nullable)
- nama_kapal (varchar 255)
- catatan (text, nullable)
- lokasi (varchar 255, nullable)
- kapasitas_kontainer_palka (int, nullable) -- NEW
- kapasitas_kontainer_deck (int, nullable)  -- NEW
- gross_tonnage (decimal 12,2, nullable)    -- NEW
- status (enum: aktif/nonaktif, default aktif)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)
```

## Verification Commands
```bash
# Check migration status
php artisan migrate:status

# Verify table structure
php artisan tinker --execute="print_r(DB::select('DESCRIBE master_kapals'));"

# Test migration rollback (if needed)
php artisan migrate:rollback --step=1
```

## Files Created/Modified
1. `fix_master_kapals_migration.sh` - Bash script for automatic fix
2. `fix_master_kapals_manual.sql` - SQL commands for manual fix
3. `fix_master_kapals_migration.php` - PHP script for flexible fix
4. `database/migrations/2025_10_16_160202_add_capacity_fields_to_master_kapals_table.php` - Fixed migration file