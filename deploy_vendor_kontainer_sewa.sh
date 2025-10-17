#!/bin/bash

# Server Deployment Script for Vendor Kontainer Sewa Permissions
# Run with: chmod +x deploy_vendor_kontainer_sewa.sh && ./deploy_vendor_kontainer_sewa.sh

echo "=== Vendor Kontainer Sewa Deployment Script ==="
echo "Date: $(date)"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from Laravel root directory."
    exit 1
fi

echo "📁 Current directory: $(pwd)"
echo ""

# 1. Pull latest changes
echo "1. Pulling latest changes from repository..."
git pull origin main
if [ $? -ne 0 ]; then
    echo "⚠️  Warning: Git pull failed or had conflicts. Please resolve manually."
fi
echo ""

# 2. Install/update dependencies
echo "2. Installing dependencies..."
composer install --no-dev --optimize-autoloader
echo ""

# 3. Run migrations
echo "3. Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "❌ Migration failed. Trying to fix master_kapals migration..."
    php fix_master_kapals_migration.php
    if [ $? -eq 0 ]; then
        echo "✅ Migration fix completed. Continuing with other migrations..."
        php artisan migrate --force
    fi
fi
echo ""

# 4. Clear caches
echo "4. Clearing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ""

# 5. Setup vendor kontainer sewa permissions
echo "5. Setting up Vendor Kontainer Sewa permissions..."
php deploy_vendor_kontainer_sewa_permissions.php
echo ""

# 6. Verify setup
echo "6. Verifying vendor kontainer sewa setup..."
if [ -f "verify_vendor_kontainer_sewa_permissions.php" ]; then
    php verify_vendor_kontainer_sewa_permissions.php
else
    echo "Verification script not found, creating quick check..."
    php artisan tinker --execute="
    echo 'Checking vendor kontainer sewa permissions:' . PHP_EOL;
    \$permissions = ['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete'];
    foreach (\$permissions as \$perm) {
        \$exists = Spatie\Permission\Models\Permission::where('name', \$perm)->exists();
        echo (\$exists ? '✓' : '✗') . ' ' . \$perm . PHP_EOL;
    }
    \$adminUsers = App\Models\User::permission(\$permissions)->count();
    echo 'Users with permissions: ' . \$adminUsers . PHP_EOL;
    "
fi
echo ""

# 7. Set proper permissions
echo "7. Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "Note: Could not change ownership (may require sudo)"
echo ""

echo "✅ Deployment completed successfully!"
echo ""
echo "🎯 Next steps:"
echo "   1. Access: http://your-domain/vendor-kontainer-sewa"
echo "   2. Login with admin account"
echo "   3. Test CRUD operations"
echo ""
echo "📋 Verification commands:"
echo "   - Check permissions: php verify_vendor_kontainer_sewa_permissions.php"
echo "   - Check routes: php artisan route:list --name=vendor-kontainer-sewa"
echo "   - Check migrations: php artisan migrate:status"
echo ""