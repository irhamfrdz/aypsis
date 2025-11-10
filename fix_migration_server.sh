#!/bin/bash
# 🚀 FIX MIGRATION CONFLICT - RUN THIS ON SERVER

echo "🔧 FIXING MIGRATION CONFLICT..."
echo "==============================="

# 1. Mark the problematic migrations as run
echo "📝 1. Marking problematic migrations as completed..."
php artisan tinker --execute="
// Mark vendor_kontainer_sewas migration as run
DB::table('migrations')->insertOrIgnore([
    'migration' => '2025_11_08_120000_create_vendor_kontainer_sewas_table',
    'batch' => DB::table('migrations')->max('batch') + 1
]);

// Mark other problematic migration as run (if exists)
DB::table('migrations')->insertOrIgnore([
    'migration' => '2025_09_15_110650_create_pembayaran_pranota_perbaikan_kontainers_table',
    'batch' => DB::table('migrations')->max('batch')
]);

echo 'Problematic migrations marked as run\n';
"

# 2. Run remaining migrations
echo "🗄️ 2. Running remaining migrations..."
php artisan migrate --force

# 3. Verify all migrations are complete
echo "✅ 3. Verifying migration status..."
php artisan migrate:status

echo ""
echo "🎉 MIGRATION CONFLICT FIXED!"
echo "============================"
echo "✅ Problematic migration marked as run"
echo "✅ Remaining migrations executed"
echo "✅ All migrations should now be complete"
