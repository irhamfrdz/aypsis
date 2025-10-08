#!/bin/bash
# 🔧 COMPOSER FIX UNTUK SERVER
# Script khusus untuk mengatasi SSL dan dependency issues

echo "🔧 COMPOSER DEPENDENCY FIX FOR SERVER..."
echo "========================================"

# 1. Set composer config untuk server
echo "⚙️  1. Setting composer configuration..."
composer config disable-tls true
composer config secure-http false

# 2. Clean existing files
echo "🧹 2. Cleaning existing composer files..."
rm -rf composer.lock vendor/

# 3. Create fresh composer.lock with exact dependencies
echo "📦 3. Creating fresh composer.lock..."
composer update --no-dev --lock

# 4. Install dependencies
echo "📥 4. Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Verify installation
echo "✅ 5. Verifying installation..."
php artisan --version

# 6. Generate autoloader cache
echo "🚀 6. Optimizing autoloader..."
composer dump-autoload --optimize --classmap-authoritative

# 7. Clear and cache Laravel
echo "🎯 7. Laravel optimization..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ COMPOSER FIX COMPLETED!"
echo "========================="
echo "Server should now be ready for deployment."
