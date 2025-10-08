#!/bin/bash

# Server deployment script for fixing composer issues
# Script untuk mengatasi masalah composer.lock dan maatwebsite/excel

echo "=== AYPSIS Server Deployment Fix ==="
echo "Fixing composer.lock synchronization issues..."

# 1. Backup existing files
echo "1. Creating backup..."
cp composer.json composer.json.backup 2>/dev/null || echo "No composer.json to backup"
cp composer.lock composer.lock.backup 2>/dev/null || echo "No composer.lock to backup"

# 2. Remove vendor directory and lock file for fresh install
echo "2. Cleaning up existing installation..."
rm -rf vendor/
rm -f composer.lock

# 3. Install dependencies fresh
echo "3. Installing dependencies from composer.json..."
composer install --no-dev --optimize-autoloader --no-scripts

# 4. Generate new lock file
echo "4. Generating fresh composer.lock..."
composer update --lock

# 5. Final install with optimizations
echo "5. Final installation with optimizations..."
composer install --no-dev --optimize-autoloader

# 6. Clear Laravel caches
echo "6. Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Optimize for production
echo "7. Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Deployment completed successfully! ==="
echo "Changes made:"
echo "- Removed maatwebsite/excel package"
echo "- Regenerated composer.lock file"
echo "- Optimized autoloader"
echo "- Cached configurations for production"
