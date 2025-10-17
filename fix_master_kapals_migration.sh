#!/bin/bash

# Script untuk memperbaiki migration master_kapals yang gagal
# Jalankan ini di server production

echo "=== Fixing Master Kapals Migration ==="
echo "Date: $(date)"

# Backup database dulu (opsional tapi direkomendasikan)
echo "1. Creating database backup..."
# mysqldump -u username -p database_name > backup_before_migration_fix_$(date +%Y%m%d_%H%M%S).sql

# Rollback migration yang bermasalah
echo "2. Rolling back the failed migration..."
php artisan migrate:rollback --step=1

# Check status migrations
echo "3. Checking migration status..."
php artisan migrate:status

# Migrate ulang dengan file yang sudah diperbaiki
echo "4. Running migration again with fixed file..."
php artisan migrate

# Verify hasil
echo "5. Verifying table structure..."
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo 'Master Kapals table columns:' . PHP_EOL;
\$columns = DB::select('DESCRIBE master_kapals');
foreach(\$columns as \$column) {
    echo \$column->Field . ' (' . \$column->Type . ')' . PHP_EOL;
}
"

echo "=== Migration fix completed ==="
