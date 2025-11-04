#!/bin/bash
# 🔧 SCRIPT KHUSUS MENGATASI ERROR PHPSPREADSHEET DI SERVER
# Jalankan script ini di server production untuk fix error CSV import

echo "🔧 FIXING PHPSPREADSHEET ERROR ON SERVER..."
echo "============================================="

# 1. Navigasi ke direktori aplikasi
echo "📁 1. Navigating to application directory..."
cd /path/to/your/aypsis

# 2. Set maintenance mode
echo "🚧 2. Enabling maintenance mode..."
php artisan down --message="Fixing PhpSpreadsheet Dependencies" --retry=60

# 3. Configure composer untuk mengatasi SSL issues
echo "🔐 3. Configuring composer SSL settings..."
composer config disable-tls true
composer config secure-http false

# 4. Remove existing vendor dan composer.lock
echo "🗑️ 4. Cleaning existing dependencies..."
rm -rf vendor/ composer.lock

# 5. Install PhpSpreadsheet secara eksplisit
echo "📊 5. Installing PhpSpreadsheet..."
composer require phpoffice/phpspreadsheet:^1.29 --no-interaction

# 6. Install Laravel Excel untuk CSV/Excel handling
echo "📋 6. Installing Laravel Excel (Maatwebsite)..."
composer require maatwebsite/excel:^3.1 --no-interaction

# 7. Install semua dependencies dengan optimasi
echo "📦 7. Installing all dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 8. Dump autoloader untuk memastikan class loading
echo "🔄 8. Optimizing autoloader..."
composer dump-autoload --optimize

# 9. Clear semua cache Laravel
echo "🧹 9. Clearing all Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 10. Rebuild cache untuk performance
echo "⚡ 10. Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 11. Test apakah PhpSpreadsheet sudah bisa di-load
echo "🧪 11. Testing PhpSpreadsheet installation..."
php -r "
try {
    require_once 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Reader\Csv;
    echo '✅ PhpSpreadsheet\\Reader\\Csv loaded successfully!' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

# 12. Publish konfigurasi Laravel Excel jika belum ada
echo "📝 12. Publishing Laravel Excel config..."
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# 13. Disable maintenance mode
echo "🚀 13. Disabling maintenance mode..."
php artisan up

echo ""
echo "✅ PHPSPREADSHEET FIX COMPLETED!"
echo "================================="
echo "🎉 Server should now support CSV import functionality!"
echo ""
echo "📋 Next steps to verify:"
echo "1. Test CSV import functionality in browser"
echo "2. Check error logs: tail -f storage/logs/laravel.log"
echo "3. If still error, check composer.json requirements"
echo ""