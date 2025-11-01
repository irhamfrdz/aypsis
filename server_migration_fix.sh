#!/bin/bash

# Quick Fix Script untuk Migration Error di Server
# Run this script on the server to fix migration issues

echo "=========================================="
echo "Migration Error Fix Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Pull latest changes
echo -e "${YELLOW}Step 1: Pulling latest changes from GitHub...${NC}"
git pull origin main

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Successfully pulled latest changes${NC}"
else
    echo -e "${RED}✗ Failed to pull changes${NC}"
    echo "Please resolve git conflicts manually"
    exit 1
fi

echo ""

# Step 2: Clear caches
echo -e "${YELLOW}Step 2: Clearing Laravel caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

# Step 3: Composer dump-autoload
echo -e "${YELLOW}Step 3: Running composer dump-autoload...${NC}"
composer dump-autoload

echo -e "${GREEN}✓ Autoload regenerated${NC}"
echo ""

# Step 4: Check migration status
echo -e "${YELLOW}Step 4: Checking current migration status...${NC}"
php artisan migrate:status

echo ""

# Step 5: Run migrations
echo -e "${YELLOW}Step 5: Running migrations...${NC}"
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed successfully!${NC}"
else
    echo -e "${RED}✗ Migration failed${NC}"
    echo ""
    echo "If you see 'Table already exists' errors, the migrations have been updated"
    echo "to handle this case. Please check the logs above."
    exit 1
fi

echo ""

# Step 6: Final verification
echo -e "${YELLOW}Step 6: Verifying migrations...${NC}"
php artisan migrate:status

echo ""
echo -e "${GREEN}=========================================="
echo "Migration Fix Completed!"
echo "==========================================${NC}"
echo ""
echo "Next steps:"
echo "1. Check if all migrations show 'Ran' status"
echo "2. Test the application"
echo "3. Check logs if any issues: tail -f storage/logs/laravel.log"

echo ""
echo "5. Lalu jalankan lagi:"
echo "php artisan migrate --path=database/migrations/2025_10_07_154324_finalize_stock_kontainers_structure.php"

echo ""
echo "=== Troubleshooting ==="
echo "Jika masih error, jalankan query manual:"
echo "ALTER TABLE stock_kontainers ADD COLUMN awalan_kontainer VARCHAR(10) NULL AFTER keterangan;"
echo "ALTER TABLE stock_kontainers ADD COLUMN nomor_seri_kontainer VARCHAR(20) NULL AFTER awalan_kontainer;"
echo "ALTER TABLE stock_kontainers ADD COLUMN akhiran_kontainer VARCHAR(5) NULL AFTER nomor_seri_kontainer;"
echo "ALTER TABLE stock_kontainers ADD COLUMN nomor_seri_gabungan VARCHAR(50) NULL AFTER akhiran_kontainer;"
echo "ALTER TABLE stock_kontainers ADD UNIQUE INDEX stock_kontainers_nomor_seri_gabungan_unique (nomor_seri_gabungan);"

echo ""
echo "=== Verification ==="
echo "Setelah selesai, verifikasi dengan:"
echo "php artisan tinker --execute=\"echo 'StockKontainer model test: '; echo App\\Models\\StockKontainer::count();\""
