#!/bin/bash
# 🔧 TROUBLESHOOTING MIGRATION CONFLICTS
# Jalankan script ini jika ada error "Table already exists" pada server

echo "🔧 MIGRATION TROUBLESHOOTING SCRIPT"
echo "==================================="

# 1. Check current migration status
echo "📊 1. Checking migration status..."
php artisan migrate:status

# 2. Check if the problematic table exists
echo "🗄️ 2. Checking if table exists in database..."
php artisan tinker --execute="
try {
    \$count = DB::table('pembayaran_pranota_perbaikan_kontainers')->count();
    echo \"Table exists with \$count records\n\";
} catch (Exception \$e) {
    echo \"Table does not exist: \" . \$e->getMessage() . \"\n\";
}
exit();
"

# 3. Check migrations table for the specific migration
echo "📋 3. Checking if migration is recorded as run..."
php artisan tinker --execute="
\$migration = DB::table('migrations')->where('migration', '2025_09_15_110650_create_pembayaran_pranota_perbaikan_kontainers_table')->first();
if (\$migration) {
    echo \"Migration is recorded as run in batch: \" . \$migration->batch . \"\n\";
} else {
    echo \"Migration is NOT recorded as run\n\";
}
exit();
"

echo ""
echo "🔍 ANALYSIS COMPLETE"
echo "==================="
echo "If table exists but migration is not recorded:"
echo "Run: php artisan tinker --execute=\"DB::table('migrations')->insert(['migration' => '2025_09_15_110650_create_pembayaran_pranota_perbaikan_kontainers_table', 'batch' => DB::table('migrations')->max('batch')]);\""
echo ""
echo "Then run: php artisan migrate --force"
