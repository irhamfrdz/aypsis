#!/bin/bash

# ============================================================================
# AYP SIS Permissions Seeder Runner
# Comprehensive permissions seeding script for production server
# ============================================================================

# Configuration
APP_NAME="AYP SIS"
SCRIPT_VERSION="1.0.0"
BACKUP_DIR="./backups"
LOG_FILE="./logs/seeder_$(date +%Y%m%d_%H%M%S).log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Create log directory
mkdir -p ./logs

# Logging function
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Error handling
error_exit() {
    echo -e "${RED}❌ Error: $1${NC}" | tee -a "$LOG_FILE"
    echo -e "${YELLOW}📋 Check log file: $LOG_FILE${NC}"
    exit 1
}

# Success message
success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

# Info message
info() {
    echo -e "${BLUE}ℹ️  $1${NC}" | tee -a "$LOG_FILE"
}

# Warning message
warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

# Header
echo "==================================================================================" | tee "$LOG_FILE"
echo "🚀 $APP_NAME - Comprehensive Permissions Seeder v$SCRIPT_VERSION" | tee -a "$LOG_FILE"
echo "==================================================================================" | tee -a "$LOG_FILE"
echo "" | tee -a "$LOG_FILE"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    error_exit "Artisan file not found. Please run this script from the Laravel project root directory."
fi

# Check PHP
if ! command -v php &> /dev/null; then
    error_exit "PHP is not installed or not in PATH"
fi

info "PHP version: $(php --version | head -n 1)"

# Check Laravel
if ! php artisan --version &> /dev/null; then
    error_exit "Laravel artisan is not working. Check your Laravel installation."
fi

info "Laravel version: $(php artisan --version)"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Function to create database backup
create_backup() {
    info "Creating database backup..."
    BACKUP_FILE="$BACKUP_DIR/backup_before_permissions_$(date +%Y%m%d_%H%M%S).sql"

    if php artisan db:dump --database=mysql > "$BACKUP_FILE" 2>/dev/null; then
        success "Database backup created: $BACKUP_FILE"
    else
        warning "Could not create database backup. Continuing without backup..."
    fi
}

# Function to run seeder
run_seeder() {
    local seeder_name="$1"
    local description="$2"

    info "Running $seeder_name seeder..."
    info "Description: $description"

    if php artisan db:seed --class="$seeder_name" --force; then
        success "$seeder_name seeder completed successfully"
        return 0
    else
        error_exit "$seeder_name seeder failed"
        return 1
    fi
}

# Function to check seeder results
check_results() {
    info "Checking seeder results..."

    # Check permissions count
    PERMISSIONS_COUNT=$(php artisan tinker --execute="echo App\Models\Permission::count();" 2>/dev/null)
    if [ $? -eq 0 ]; then
        success "Total permissions in database: $PERMISSIONS_COUNT"
    else
        warning "Could not check permissions count"
    fi

    # Check roles count
    ROLES_COUNT=$(php artisan tinker --execute="echo App\Models\Role::count();" 2>/dev/null)
    if [ $? -eq 0 ]; then
        success "Total roles in database: $ROLES_COUNT"
    else
        warning "Could not check roles count"
    fi

    # Check users count
    USERS_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null)
    if [ $? -eq 0 ]; then
        success "Total users in database: $USERS_COUNT"
    else
        warning "Could not check users count"
    fi
}

# Function to display summary
display_summary() {
    echo "" | tee -a "$LOG_FILE"
    echo "==================================================================================" | tee -a "$LOG_FILE"
    echo "📊 SEEDING SUMMARY" | tee -a "$LOG_FILE"
    echo "==================================================================================" | tee -a "$LOG_FILE"

    echo "✅ Completed Seeders:" | tee -a "$LOG_FILE"
    echo "   1. ComprehensivePermissionsSeeder - All system permissions" | tee -a "$LOG_FILE"
    echo "   2. RoleAndPermissionSeeder - Roles and user assignments" | tee -a "$LOG_FILE"
    echo "" | tee -a "$LOG_FILE"

    echo "👥 Default Users Created:" | tee -a "$LOG_FILE"
    echo "   • Admin: username='admin', password='admin123'" | tee -a "$LOG_FILE"
    echo "   • Manager: username='manager', password='manager123'" | tee -a "$LOG_FILE"
    echo "   • Staff: username='staff', password='staff123'" | tee -a "$LOG_FILE"
    echo "   • Supervisor: username='supervisor', password='supervisor123'" | tee -a "$LOG_FILE"
    echo "   • Supir: username='supir', password='supir123'" | tee -a "$LOG_FILE"
    echo "" | tee -a "$LOG_FILE"

    echo "🔐 Permission Categories:" | tee -a "$LOG_FILE"
    echo "   • Dashboard permissions" | tee -a "$LOG_FILE"
    echo "   • Master data permissions (Karyawan, Kontainer, Tujuan, etc.)" | tee -a "$LOG_FILE"
    echo "   • Pranota permissions (Supir, Tagihan Kontainer)" | tee -a "$LOG_FILE"
    echo "   • Pembayaran permissions" | tee -a "$LOG_FILE"
    echo "   • Tagihan permissions" | tee -a "$LOG_FILE"
    echo "   • Permohonan permissions" | tee -a "$LOG_FILE"
    echo "   • Perbaikan Kontainer permissions" | tee -a "$LOG_FILE"
    echo "   • User & Approval permissions" | tee -a "$LOG_FILE"
    echo "   • System permissions" | tee -a "$LOG_FILE"
    echo "" | tee -a "$LOG_FILE"

    echo "📋 Role Permissions:" | tee -a "$LOG_FILE"
    echo "   • Admin: Full access to all permissions" | tee -a "$LOG_FILE"
    echo "   • Manager: Most permissions except user/permission management" | tee -a "$LOG_FILE"
    echo "   • Supervisor: Operational + approval permissions" | tee -a "$LOG_FILE"
    echo "   • Staff: Basic view permissions" | tee -a "$LOG_FILE"
    echo "   • Supir: Limited to their own pranota" | tee -a "$LOG_FILE"
    echo "" | tee -a "$LOG_FILE"

    echo "📁 Log file: $LOG_FILE" | tee -a "$LOG_FILE"
    if [ -d "$BACKUP_DIR" ]; then
        echo "💾 Backup location: $BACKUP_DIR" | tee -a "$LOG_FILE"
    fi
    echo "" | tee -a "$LOG_FILE"
}

# Main execution
main() {
    info "Starting comprehensive permissions seeding process..."

    # Create backup
    create_backup

    # Clear cache
    info "Clearing Laravel cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear

    # Run seeders in order
    info "Running seeders in sequence..."

    # 1. Comprehensive Permissions Seeder
    run_seeder "ComprehensivePermissionsSeeder" "Creates all system permissions organized by categories"

    # 2. Role and Permission Seeder
    run_seeder "RoleAndPermissionSeeder" "Creates roles and assigns permissions to users"

    # Check results
    check_results

    # Display summary
    display_summary

    echo "==================================================================================" | tee -a "$LOG_FILE"
    success "🎉 Comprehensive permissions seeding completed successfully!"
    echo "==================================================================================" | tee -a "$LOG_FILE"

    # Final instructions
    echo "" | tee -a "$LOG_FILE"
    echo "🚀 NEXT STEPS:" | tee -a "$LOG_FILE"
    echo "1. Test login with default users" | tee -a "$LOG_FILE"
    echo "2. Verify permissions are working correctly" | tee -a "$LOG_FILE"
    echo "3. Update user passwords in production" | tee -a "$LOG_FILE"
    echo "4. Configure additional users as needed" | tee -a "$LOG_FILE"
    echo "" | tee -a "$LOG_FILE"
}

# Trap for cleanup
trap 'error_exit "Script interrupted by user"' INT TERM

# Run main function
main "$@"
