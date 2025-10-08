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
php artisan migrate --force

# 7. Clear all caches (PENTING untuk Report Tagihan menu)
echo "🧹 7. Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

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
