#!/bin/bash

# Script deployment audit trail system ke server
# File: deploy_audit_trail.sh

echo "🚀 DEPLOYING AUDIT TRAIL SYSTEM TO SERVER"
echo "=========================================="
echo "Started at: $(date)"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
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

# 1. Check PHP version
echo -e "\n${BLUE}📋 CHECKING SYSTEM REQUIREMENTS${NC}"
echo "=================================="

PHP_VERSION=$(php -r "echo PHP_VERSION;")
print_info "PHP Version: $PHP_VERSION"

# 2. Run migration
echo -e "\n${BLUE}📊 RUNNING DATABASE MIGRATION${NC}"
echo "=============================="

if php artisan migrate --force; then
    print_status "Migration berhasil dijalankan"
else
    print_error "Migration gagal! Periksa konfigurasi database."
    exit 1
fi

# 3. Setup permissions
echo -e "\n${BLUE}🔑 SETTING UP PERMISSIONS${NC}"
echo "========================="

if [ -f "setup_audit_permissions_server.php" ]; then
    if php setup_audit_permissions_server.php; then
        print_status "Permissions setup berhasil"
    else
        print_warning "Permissions setup ada masalah, silakan cek manual"
    fi
else
    print_warning "File setup_audit_permissions_server.php tidak ditemukan"
fi

# 4. Add Auditable trait to models
echo -e "\n${BLUE}🏷️  ADDING AUDITABLE TRAIT TO MODELS${NC}"
echo "===================================="

if [ -f "add_auditable_to_all_models.php" ]; then
    if php add_auditable_to_all_models.php; then
        print_status "Auditable trait berhasil ditambahkan ke models"
    else
        print_warning "Ada masalah saat menambahkan Auditable trait"
    fi
else
    print_warning "File add_auditable_to_all_models.php tidak ditemukan"
fi

# 5. Clear Laravel caches
echo -e "\n${BLUE}🧹 CLEARING LARAVEL CACHES${NC}"
echo "=========================="

php artisan cache:clear && print_status "Application cache cleared"
php artisan config:clear && print_status "Config cache cleared"
php artisan route:clear && print_status "Route cache cleared"
php artisan view:clear && print_status "View cache cleared"

# 6. Set proper permissions for Laravel directories
echo -e "\n${BLUE}📁 SETTING FILE PERMISSIONS${NC}"
echo "==========================="

if [ -d "storage" ]; then
    chmod -R 775 storage && print_status "Storage permissions set to 775"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache && print_status "Bootstrap cache permissions set to 775"
fi

# 7. Test implementation
echo -e "\n${BLUE}🧪 TESTING IMPLEMENTATION${NC}"
echo "========================="

if [ -f "test_audit_log_implementation.php" ]; then
    print_info "Running audit log tests..."
    if php test_audit_log_implementation.php; then
        print_status "Testing berhasil!"
    else
        print_warning "Testing ada masalah, silakan cek manual"
    fi
else
    print_warning "File test_audit_log_implementation.php tidak ditemukan"
fi

# 8. Check routes
echo -e "\n${BLUE}📍 CHECKING ROUTES${NC}"
echo "=================="

print_info "Checking audit-log routes..."
if php artisan route:list | grep -i audit > /dev/null 2>&1; then
    print_status "Audit log routes ditemukan"
else
    print_warning "Audit log routes belum ada. Silakan tambahkan ke routes/web.php"
fi

# 9. Final checks
echo -e "\n${BLUE}🔍 FINAL VERIFICATION${NC}"
echo "===================="

# Check if audit_logs table exists
TABLE_CHECK=$(php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
try {
    \$exists = Schema::hasTable('audit_logs');
    echo \$exists ? 'EXISTS' : 'NOT_EXISTS';
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
")

if [[ "$TABLE_CHECK" == "EXISTS" ]]; then
    print_status "Table audit_logs sudah ada di database"
elif [[ "$TABLE_CHECK" == "NOT_EXISTS" ]]; then
    print_error "Table audit_logs belum ada di database"
else
    print_warning "Tidak dapat memeriksa table audit_logs: $TABLE_CHECK"
fi

# Check if AuditLog model exists
if [ -f "app/Models/AuditLog.php" ]; then
    print_status "AuditLog model file exists"
else
    print_error "AuditLog model file tidak ditemukan"
fi

# Check if AuditLogController exists
if [ -f "app/Http/Controllers/AuditLogController.php" ]; then
    print_status "AuditLogController file exists"
else
    print_error "AuditLogController file tidak ditemukan"
fi

# 10. Display next steps
echo -e "\n${GREEN}🎉 DEPLOYMENT COMPLETED!${NC}"
echo "========================="
echo "Waktu selesai: $(date)"
echo ""
echo -e "${YELLOW}📋 LANGKAH SELANJUTNYA:${NC}"
echo "1. ✅ Login ke aplikasi sebagai admin"
echo "2. ✅ Pastikan menu 'Audit Log' muncul di sidebar"
echo "3. ✅ Test tombol 'Riwayat' di halaman master data"
echo "4. ✅ Coba create/update/delete data untuk test audit trail"
echo "5. ✅ Periksa dashboard audit log di menu 'Audit Log'"
echo ""
echo -e "${BLUE}📞 JIKA ADA MASALAH:${NC}"
echo "- Cek Laravel log: tail -f storage/logs/laravel.log"
echo "- Cek web server error log"
echo "- Pastikan database connection berfungsi"
echo "- Pastikan permissions file sudah benar"
echo ""
echo -e "${GREEN}🔗 URL UNTUK TESTING:${NC}"
echo "- Dashboard audit log: /audit-logs"
echo "- Master data pages: /master-* (cek tombol Riwayat)"
echo ""
echo -e "${BLUE}💡 TROUBLESHOOTING:${NC}"
echo "- Jika 500 error: php artisan config:clear && php artisan cache:clear"
echo "- Jika permission denied: cek file permissions dan ownership"
echo "- Jika route not found: tambahkan routes audit log ke routes/web.php"
