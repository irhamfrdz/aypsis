#!/bin/bash

# Deploy Script - Pull Latest Changes from Git
# Script untuk melakukan pull perubahan terbaru dari repository git ke server

echo "🚀 Starting deployment process..."
echo "================================="

# 1. Navigasi ke direktori aplikasi
echo "📁 Navigating to application directory..."
cd /var/www/html/aypsis || {
    echo "❌ Error: Application directory not found!"
    exit 1
}

# 2. Backup current state (optional)
echo "💾 Creating backup of current state..."
cp -r . ../aypsis-backup-$(date +%Y%m%d-%H%M%S) 2>/dev/null || echo "⚠️  Backup skipped"

# 3. Check current git status
echo "📊 Checking current git status..."
git status

# 4. Stash any local changes
echo "📦 Stashing local changes (if any)..."
git stash

# 5. Pull latest changes from main branch
echo "⬇️  Pulling latest changes from origin/main..."
git pull origin main

# 6. Install/Update composer dependencies
echo "📚 Installing/Updating composer dependencies..."
composer install --optimize-autoloader --no-dev

# 7. Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 8. Migrate database (if needed)
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 9. Optimize application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Set proper permissions
echo "🔐 Setting proper file permissions..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache

# 11. Restart services (if needed)
echo "🔄 Restarting services..."
sudo systemctl reload nginx
sudo systemctl restart php8.1-fpm 2>/dev/null || sudo systemctl restart php8.0-fpm 2>/dev/null || sudo systemctl restart php7.4-fpm

echo "================================="
echo "✅ Deployment completed successfully!"
echo "🌐 Application is now updated with latest changes"
echo "📅 Deployed at: $(date)"
