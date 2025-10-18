#!/bin/bash

# Script untuk server - Pull latest changes dan migrate dengan aman
# File: server_pull_and_migrate.sh

echo "🚀 SERVER UPDATE SCRIPT - PULL & MIGRATE"
echo "========================================"
echo "Started at: $(date)"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in Laravel project directory
if [ ! -f "artisan" ]; then
    print_error "File 'artisan' tidak ditemukan. Pastikan Anda berada di direktori Laravel project."
    exit 1
fi

print_info "Detected Laravel project in: $(pwd)"

# 1. Backup database sebelum migrate (optional)
echo -e "\n${BLUE}💾 DATABASE BACKUP (OPTIONAL)${NC}"
echo "=================================="
read -p "Apakah Anda ingin backup database sebelum migrate? (y/N): " backup_choice
if [[ $backup_choice =~ ^[Yy]$ ]]; then
    # Ambil config database dari .env
    DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
    DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
    DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)

    if [ ! -z "$DB_DATABASE" ]; then
        backup_file="backup_$(date +%Y%m%d_%H%M%S)_${DB_DATABASE}.sql"
        print_info "Creating database backup: $backup_file"
        mysqldump -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$backup_file"
        if [ $? -eq 0 ]; then
            print_status "Database backup created: $backup_file"
        else
            print_warning "Database backup failed, continuing anyway..."
        fi
    fi
fi

# 2. Pull latest changes dari repository
echo -e "\n${BLUE}📥 PULLING LATEST CHANGES${NC}"
echo "=========================="

# Stash local changes jika ada
git stash push -m "Auto stash before pull $(date)"
print_info "Local changes stashed (if any)"

# Pull latest changes
if git pull origin main; then
    print_status "Successfully pulled latest changes"
else
    print_error "Git pull failed!"
    exit 1
fi

# 3. Install/Update dependencies jika ada perubahan composer
echo -e "\n${BLUE}📦 CHECKING DEPENDENCIES${NC}"
echo "========================="

if [ -f "composer.lock" ]; then
    if git diff HEAD~1 HEAD --name-only | grep -q "composer.json\|composer.lock"; then
        print_info "Composer files changed, updating dependencies..."
        composer install --no-dev --optimize-autoloader
        print_status "Dependencies updated"
    else
        print_info "No dependency changes detected"
    fi
fi

# 4. Cek migration status sebelum migrate
echo -e "\n${BLUE}📊 CHECKING MIGRATION STATUS${NC}"
echo "=============================="

print_info "Current migration status:"
php artisan migrate:status | tail -10

# 5. Run migrations dengan handling error
echo -e "\n${BLUE}🗄️  RUNNING MIGRATIONS${NC}"
echo "======================"

print_info "Running migrations..."

# Jalankan migrate dengan output yang detail
if php artisan migrate --force --verbose; then
    print_status "Migrations completed successfully!"
else
    print_error "Migration failed!"

    # Tampilkan informasi untuk troubleshooting
    echo -e "\n${YELLOW}🔧 TROUBLESHOOTING INFORMATION:${NC}"
    echo "================================"

    # Cek migration files yang pending
    print_info "Checking pending migrations..."
    php artisan migrate:status | grep "No"

    # Cek apakah ada conflict dengan table yang sudah ada
    print_info "Checking for table conflicts..."

    # Opsi untuk skip migration yang bermasalah
    echo -e "\n${YELLOW}💡 OPSI TROUBLESHOOTING:${NC}"
    echo "1. Rollback last migration: php artisan migrate:rollback --step=1"
    echo "2. Reset migrations: php artisan migrate:reset (DANGER: akan hapus semua data!)"
    echo "3. Fresh migrate: php artisan migrate:fresh (DANGER: akan hapus semua data!)"
    echo "4. Skip problematic migration manually"
    echo ""

    read -p "Apakah Anda ingin mencoba rollback dan migrate ulang? (y/N): " retry_choice
    if [[ $retry_choice =~ ^[Yy]$ ]]; then
        print_info "Rolling back last migration..."
        php artisan migrate:rollback --step=1

        print_info "Trying migrate again..."
        if php artisan migrate --force; then
            print_status "Migration successful after rollback!"
        else
            print_error "Migration still failed. Manual intervention required."
            exit 1
        fi
    else
        print_warning "Migration failed. Please check manually."
        exit 1
    fi
fi

# 6. Clear caches setelah update
echo -e "\n${BLUE}🧹 CLEARING CACHES${NC}"
echo "=================="

php artisan cache:clear && print_status "Application cache cleared"
php artisan config:clear && print_status "Config cache cleared"
php artisan route:clear && print_status "Route cache cleared"
php artisan view:clear && print_status "View cache cleared"

# 7. Set file permissions (untuk production server)
echo -e "\n${BLUE}📁 SETTING FILE PERMISSIONS${NC}"
echo "==========================="

if [ -d "storage" ]; then
    chmod -R 775 storage
    print_status "Storage permissions set to 775"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
    print_status "Bootstrap cache permissions set to 775"
fi

# 8. Test aplikasi
echo -e "\n${BLUE}🧪 TESTING APPLICATION${NC}"
echo "======================"

# Test basic Laravel functionality
if php artisan --version > /dev/null 2>&1; then
    print_status "Laravel artisan working"
else
    print_error "Laravel artisan not working!"
fi

# Test database connection
if php artisan migrate:status > /dev/null 2>&1; then
    print_status "Database connection OK"
else
    print_warning "Database connection issue"
fi

# 9. Final status
echo -e "\n${GREEN}🎉 SERVER UPDATE COMPLETED!${NC}"
echo "============================"
echo "Completed at: $(date)"
echo ""

print_info "Current migration status:"
php artisan migrate:status | tail -5

echo -e "\n${YELLOW}📋 NEXT STEPS:${NC}"
echo "1. ✅ Test website functionality"
echo "2. ✅ Check audit log features (if applicable)"
echo "3. ✅ Verify all menus working properly"
echo "4. ✅ Test create/update/delete operations"
echo ""

echo -e "${BLUE}💡 IF ISSUES OCCUR:${NC}"
echo "- Check Laravel logs: tail -f storage/logs/laravel.log"
echo "- Check web server logs"
echo "- Verify file permissions"
echo "- Clear caches again if needed"
echo ""

print_status "Server update script completed!"
