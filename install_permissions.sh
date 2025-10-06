#!/bin/bash
# ONE-LINER COMMAND UNTUK MENJALANKAN PERMISSION SEEDER

# Command lengkap dalam satu baris (untuk server production)
cd /path/to/your/aypsis && php artisan down --message="Installing Permissions" --retry=60 && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan config:clear && php artisan cache:clear && php artisan db:seed --class=ComprehensiveSystemPermissionSeeder && php artisan up && echo "✅ Permission Seeder completed! Total permissions: $(php artisan tinker --execute="echo App\Models\Permission::count();")"

# Atau jika ingin step-by-step dengan konfirmasi:
echo "🚀 AYPSIS Permission Seeder Installer"
echo "====================================="
echo ""
read -p "📁 Masukkan path direktori aplikasi (contoh: /var/www/aypsis): " APP_PATH
echo ""
echo "📍 Navigating to: $APP_PATH"
cd "$APP_PATH"

echo "🚧 Setting maintenance mode..."
php artisan down --message="Installing Permissions" --retry=60

echo "📥 Pulling latest changes..."
git pull origin main

echo "📦 Updating dependencies..."
composer install --no-dev --optimize-autoloader

echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "🔑 READY TO INSTALL PERMISSIONS"
echo "==============================="
echo "⚠️  This will add 400+ permissions to your system"
echo "✅ Seeder automatically checks for duplicates"
echo ""
read -p "Proceed with permission installation? (y/N): " confirm

if [[ $confirm =~ ^[Yy]$ ]]; then
    echo "🚀 Installing permissions..."
    php artisan db:seed --class=ComprehensiveSystemPermissionSeeder

    echo "✅ Verifying installation..."
    TOTAL_PERMS=$(php artisan tinker --execute="echo App\Models\Permission::count();")
    echo "📊 Total permissions in system: $TOTAL_PERMS"

    echo "🔓 Disabling maintenance mode..."
    php artisan up

    echo ""
    echo "🎉 PERMISSION SEEDER COMPLETED SUCCESSFULLY!"
    echo "==========================================="
    echo "✅ Total permissions: $TOTAL_PERMS"
    echo "✅ System is back online"
    echo ""
else
    echo "❌ Installation cancelled"
    php artisan up
    echo "🔓 Maintenance mode disabled"
fi
