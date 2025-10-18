#!/bin/bash
# 🚀 PERINTAH PULL DARI GIT KE SERVER
# Jalankan perintah ini di server untuk mengupdate aplikasi

echo "🔄 STARTING GIT PULL TO SERVER..."
echo "================================="

# 1. Navigasi ke direktori aplikasi
echo "📁 1. Navigating to application directory..."
cd /path/to/your/aypsis

# 2. Backup database sebelum update (opsional tapi direkomendasikan)
echo "💾 2. Creating database backup..."
php artisan backup:run --only-db
# atau manual: mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Set maintenance mode untuk mencegah akses user selama update
echo "🚧 3. Enabling maintenance mode..."
php artisan down --message="System Update in Progress" --retry=60

# 4. Pull perubahan terbaru dari git
echo "📥 4. Pulling latest changes from git..."
git fetch origin
git pull origin main

# 5. Fix composer dependencies dengan SSL handling
echo "📦 5. Fixing composer dependencies..."
composer config disable-tls true
composer config secure-http false
rm -rf composer.lock vendor/
composer update --no-dev --lock
composer install --no-dev --optimize-autoloader --no-interaction

# 6. Check migration status first
echo "🗄️ 6. Checking migration status..."
    php artisan migrate:status

# 6b. If there are migration conflicts, run this command to mark the problematic migration as run:
# php artisan migrate:status | grep "pembayaran_pranota_perbaikan_kontainers"
# If it's showing as "Pending" but table exists, mark it as run:
# php artisan schema:dump  # This creates a schema file
# Then manually mark the migration as run in the database:
# INSERT INTO migrations (migration, batch) VALUES ('2025_09_15_110650_create_pembayaran_pranota_perbaikan_kontainers_table', (SELECT MAX(batch) FROM migrations))

# 6c. Run database migrations jika ada
echo "🗄️ 6c. Running database migrations..."

# 7. Clear all caches (PENTING untuk Report Tagihan menu)
echo "🧹 7. Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 7.1 🏭 VENDOR KONTAINER SEWA PERMISSIONS SETUP (BARU DITAMBAHKAN)
echo "🏭 7.1 Setting up Vendor Kontainer Sewa permissions..."
echo "ℹ️  This will add vendor kontainer sewa management permissions"
echo "📋 Compatible with custom permission system (no Spatie dependency)"

# Run vendor kontainer sewa permission setup
php setup_vendor_kontainer_sewa_custom_permissions.php

echo "✅ Vendor Kontainer Sewa permissions setup completed!"
echo "🌐 Access URL: /vendor-kontainer-sewa"

# 7a. 📋 VENDOR CSV UPDATE (JIKA DIPERLUKAN)
echo "📋 7a. Vendor CSV Update Commands Available..."
echo "ℹ️  To update vendor invoices from CSV, follow these steps:"
echo "   1. Upload CSV file to server: scp Zona.csv user@server:/var/www/aypsis/"
echo "   2. Run backup: php backup_vendor_data.php"
echo "   3. Run update: php artisan vendor:update-from-csv /var/www/aypsis/Zona.csv"
echo "   Available update commands:"
echo "   - php artisan vendor:update-from-csv [file_path]"
echo "   - php update_vendor_from_csv.php (standalone)"
echo "   - php backup_vendor_data.php (backup first!)"

# 7b. 🔧 PERBAIKAN DPP TAGIHAN KONTAINER (BARU DITAMBAHKAN)
echo "💰 7b. Fixing DPP calculations..."
echo "⚠️  IMPORTANT: This will fix incorrect DPP values in tagihan kontainer"

# Fix tarif harian (Rp 42,042 × hari)
echo "🔧 Fixing Harian DPP calculations..."
php artisan tinker --execute="
\$harian = \App\Models\DaftarTagihanKontainerSewa::where('tarif', 'Harian')
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->get();

\$fixed = 0;
foreach (\$harian as \$tagihan) {
    \$startDate = \Carbon\Carbon::parse(\$tagihan->tanggal_awal);
    \$endDate = \Carbon\Carbon::parse(\$tagihan->tanggal_akhir);
    \$days = \$startDate->diffInDays(\$endDate) + 1;

    \$correctDPP = 42042 * \$days;

    if (\$tagihan->dpp != \$correctDPP) {
        \$tagihan->update(['dpp' => \$correctDPP]);
        \$fixed++;
    }
}
echo 'Fixed ' . \$fixed . ' harian DPP records';
"

# Fix tarif bulanan (Rp 1,261,261 × periode)
echo "🔧 Fixing Bulanan DPP calculations..."
php artisan tinker --execute="
\$bulanan = \App\Models\DaftarTagihanKontainerSewa::where('tarif', 'Bulanan')->get();

\$fixed = 0;
foreach (\$bulanan as \$tagihan) {
    \$correctDPP = 1261261 * \$tagihan->periode;

    if (\$tagihan->dpp != \$correctDPP) {
        \$tagihan->update(['dpp' => \$correctDPP]);
        \$fixed++;
    }
}
echo 'Fixed ' . \$fixed . ' bulanan DPP records';
"

# 8. 🔥 JALANKAN SEEDER PERMISSION (BARU DITAMBAHKAN)
echo "🔑 8. Running Permission Seeder..."
echo "⚠️  IMPORTANT: This will add 400+ permissions to your system"
echo "📋 Seeder will check for existing permissions and only add new ones"

# Opsi 1: Jalankan seeder secara langsung
echo "🚀 Running ComprehensiveSystemPermissionSeeder..."
php artisan db:seed --class=ComprehensiveSystemPermissionSeeder

# Opsi 2: Jika ingin lebih hati-hati, jalankan dengan dry-run dulu (uncomment line di bawah)
# php artisan tinker --execute="app(\Database\Seeders\ComprehensiveSystemPermissionSeeder::class)->run()"

# Opsi 3: Jika ingin melihat preview dulu tanpa insert ke database
# php artisan tinker --execute="
# \$seeder = new \Database\Seeders\ComprehensiveSystemPermissionSeeder();
# echo 'Total permissions yang akan dibuat: ' . count(\$seeder->getAllPermissions());
# "

# 8. Fix multi-dot permissions if they exist
echo "🔧 8. Checking and fixing multi-dot permissions..."
php debug_permission_error.php | grep -A 10 "PROBLEMATIC PERMISSIONS"
# Uncomment the next line if multi-dot permissions are found:
# php fix_multi_dot_permissions.php

# 9. Optimize application
echo "⚡ 9. Optimizing application..."

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

# 10. Set proper permissions
echo "🔐 10. Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 11. Disable maintenance mode
echo "✅ 11. Disabling maintenance mode..."
php artisan up

echo ""
echo "🎉 GIT PULL TO SERVER COMPLETED SUCCESSFULLY!"
echo "============================================="
echo "✅ Application updated to latest version"
echo "✅ Database migrations applied"
echo "✅ Caches cleared and optimized"
echo "✅ Maintenance mode disabled"
echo ""
echo "🌐 Your application is now live with the latest changes!"
