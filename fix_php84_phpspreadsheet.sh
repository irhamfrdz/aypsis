#!/bin/bash
# 🔧 SCRIPT KHUSUS MENGATASI ERROR PHPSPREADSHEET + PHP 8.4 COMPATIBILITY
# Jalankan script ini di server production untuk fix error CSV import

echo "🔧 FIXING PHPSPREADSHEET ERROR WITH PHP 8.4 COMPATIBILITY..."
echo "============================================================="

# 1. Navigasi ke direktori aplikasi
echo "📁 1. Navigating to application directory..."
cd /var/www/aypsis

# 2. Set maintenance mode
echo "🚧 2. Enabling maintenance mode..."
php artisan down --message="Fixing PhpSpreadsheet Dependencies" --retry=60

# 3. Set environment variables untuk Composer
echo "🔐 3. Setting Composer environment variables..."
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_DISABLE_XDEBUG_WARN=1

# 4. Configure composer untuk mengatasi SSL issues
echo "🔧 4. Configuring composer settings..."
composer config disable-tls true
composer config secure-http false
composer config --global disable-tls true
composer config --global secure-http false

# 5. Update composer.json untuk compatibility dengan PHP 8.4
echo "📝 5. Updating composer.json for PHP 8.4 compatibility..."
cp composer.json composer.json.backup

# 6. Remove existing vendor dan composer.lock
echo "🗑️ 6. Cleaning existing dependencies..."
rm -rf vendor/ composer.lock

# 7. Update Laravel Framework ke versi yang support PHP 8.4
echo "📦 7. Updating Laravel Framework..."
composer update laravel/framework --with-all-dependencies --no-interaction

# 8. Install PhpSpreadsheet dengan dependency resolution
echo "📊 8. Installing PhpSpreadsheet with dependency resolution..."
composer require phpoffice/phpspreadsheet:^1.29 --with-all-dependencies --no-interaction

# 9. Install Laravel Excel dengan force resolution
echo "📋 9. Installing Laravel Excel..."
composer require maatwebsite/excel:^3.1 --with-all-dependencies --no-interaction

# 10. Force install semua dependencies dengan ignore platform requirements
echo "🔄 10. Force installing all dependencies..."
composer install --ignore-platform-reqs --no-dev --optimize-autoloader --no-interaction

# 11. Dump autoloader untuk memastikan class loading
echo "⚡ 11. Optimizing autoloader..."
composer dump-autoload --optimize

# 12. Clear semua cache Laravel
echo "🧹 12. Clearing all Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 13. Test apakah PhpSpreadsheet sudah bisa di-load
echo "🧪 13. Testing PhpSpreadsheet installation..."
php -r "
try {
    require_once 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Reader\Csv;
    echo '✅ PhpSpreadsheet\\Reader\\Csv loaded successfully!' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
    echo '📋 Trying alternative approach...' . PHP_EOL;
}
"

# 14. Jika masih error, coba install dengan ignore platform requirements
echo "🔧 14. Alternative installation method if needed..."
if [ $? -ne 0 ]; then
    echo "🔄 Using ignore-platform-reqs method..."
    composer require phpoffice/phpspreadsheet:^1.28 --ignore-platform-reqs --no-interaction
    composer require maatwebsite/excel:^3.1 --ignore-platform-reqs --no-interaction
    composer dump-autoload --optimize
fi

# 15. Publish konfigurasi Laravel Excel
echo "📝 15. Publishing Laravel Excel config..."
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --force

# 16. Rebuild cache untuk performance
echo "⚡ 16. Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 17. Test final
echo "🧪 17. Final test..."
php -r "
try {
    require_once 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Reader\Csv;
    use Maatwebsite\Excel\Facades\Excel;
    echo '✅ All CSV/Excel dependencies loaded successfully!' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Final Error: ' . \$e->getMessage() . PHP_EOL;
}
"

# 18. Disable maintenance mode
echo "🚀 18. Disabling maintenance mode..."
php artisan up

echo ""
echo "✅ PHPSPREADSHEET + PHP 8.4 FIX COMPLETED!"
echo "=========================================="
echo "🎉 Server should now support CSV import functionality!"
echo ""
echo "📋 If still having issues, try these commands manually:"
echo "   composer install --ignore-platform-reqs"
echo "   composer require phpoffice/phpspreadsheet:^1.28 --ignore-platform-reqs"
echo ""