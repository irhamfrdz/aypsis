#!/bin/bash

# ============================================================================
# AYP SIS Permissions Seeder Test Script
# Test script to verify permissions seeder functionality
# ============================================================================

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1"
}

# Success message
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Info message
info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Warning message
warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Error message
error() {
    echo -e "${RED}❌ $1${NC}"
}

# Header
echo "=================================================================================="
echo "🧪 AYP SIS - Permissions Seeder Test Script"
echo "=================================================================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    error "Artisan file not found. Please run this script from the Laravel project root directory."
    exit 1
fi

info "Testing permissions seeder functionality..."

# Test 1: Check if seeders exist
echo ""
info "Test 1: Checking seeder files..."
if [ -f "database/seeders/ComprehensivePermissionsSeeder.php" ]; then
    success "ComprehensivePermissionsSeeder.php exists"
else
    error "ComprehensivePermissionsSeeder.php not found"
fi

if [ -f "database/seeders/RoleAndPermissionSeeder.php" ]; then
    success "RoleAndPermissionSeeder.php exists"
else
    error "RoleAndPermissionSeeder.php not found"
fi

# Test 2: Check if models exist
echo ""
info "Test 2: Checking required models..."
if [ -f "app/Models/Permission.php" ]; then
    success "Permission model exists"
else
    error "Permission model not found"
fi

if [ -f "app/Models/Role.php" ]; then
    success "Role model exists"
else
    error "Role model not found"
fi

if [ -f "app/Models/User.php" ]; then
    success "User model exists"
else
    error "User model not found"
fi

# Test 3: Check database connection
echo ""
info "Test 3: Testing database connection..."
if php artisan migrate:status > /dev/null 2>&1; then
    success "Database connection successful"
else
    error "Database connection failed"
fi

# Test 4: Check if tables exist
echo ""
info "Test 4: Checking database tables..."
TABLES=$(php artisan tinker --execute="echo implode(', ', array_keys(\Illuminate\Support\Facades\Schema::getConnection()->getDoctrineSchemaManager()->listTableNames()));" 2>/dev/null)

if echo "$TABLES" | grep -q "permissions"; then
    success "permissions table exists"
else
    warning "permissions table does not exist (will be created by migrations)"
fi

if echo "$TABLES" | grep -q "roles"; then
    success "roles table exists"
else
    warning "roles table does not exist (will be created by migrations)"
fi

if echo "$TABLES" | grep -q "users"; then
    success "users table exists"
else
    warning "users table does not exist (will be created by migrations)"
fi

# Test 5: Check current data counts
echo ""
info "Test 5: Checking current database state..."
PERMISSIONS_COUNT=$(php artisan tinker --execute="echo App\Models\Permission::count();" 2>/dev/null)
if [ $? -eq 0 ]; then
    success "Current permissions count: $PERMISSIONS_COUNT"
else
    warning "Could not check permissions count"
fi

ROLES_COUNT=$(php artisan tinker --execute="echo App\Models\Role::count();" 2>/dev/null)
if [ $? -eq 0 ]; then
    success "Current roles count: $ROLES_COUNT"
else
    warning "Could not check roles count"
fi

USERS_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null)
if [ $? -eq 0 ]; then
    success "Current users count: $USERS_COUNT"
else
    warning "Could not check users count"
fi

# Test 6: Validate seeder syntax
echo ""
info "Test 6: Validating seeder syntax..."
if php -l database/seeders/ComprehensivePermissionsSeeder.php > /dev/null 2>&1; then
    success "ComprehensivePermissionsSeeder.php syntax is valid"
else
    error "ComprehensivePermissionsSeeder.php has syntax errors"
fi

if php -l database/seeders/RoleAndPermissionSeeder.php > /dev/null 2>&1; then
    success "RoleAndPermissionSeeder.php syntax is valid"
else
    error "RoleAndPermissionSeeder.php has syntax errors"
fi

# Test 7: Check execution scripts
echo ""
info "Test 7: Checking execution scripts..."
if [ -f "run_permissions_seeder.sh" ]; then
    success "run_permissions_seeder.sh exists"
    if [ -x "run_permissions_seeder.sh" ]; then
        success "run_permissions_seeder.sh is executable"
    else
        warning "run_permissions_seeder.sh is not executable (run: chmod +x run_permissions_seeder.sh)"
    fi
else
    error "run_permissions_seeder.sh not found"
fi

if [ -f "run_permissions_seeder.bat" ]; then
    success "run_permissions_seeder.bat exists"
else
    error "run_permissions_seeder.bat not found"
fi

# Test 8: Check README
echo ""
info "Test 8: Checking documentation..."
if [ -f "COMPREHENSIVE_PERMISSIONS_SEEDER_README.md" ]; then
    success "README file exists"
else
    error "README file not found"
fi

# Summary
echo ""
echo "=================================================================================="
echo "📊 TEST SUMMARY"
echo "=================================================================================="

echo ""
info "If all tests passed, you can run the seeder with:"
echo "  Linux/Mac: ./run_permissions_seeder.sh"
echo "  Windows: run_permissions_seeder.bat"
echo "  Manual: php artisan db:seed --class=ComprehensivePermissionsSeeder --force"
echo "          php artisan db:seed --class=RoleAndPermissionSeeder --force"

echo ""
info "Default users that will be created:"
echo "  • Admin: admin / admin123"
echo "  • Manager: manager / manager123"
echo "  • Staff: staff / staff123"
echo "  • Supervisor: supervisor / supervisor123"
echo "  • Supir: supir / supir123"

echo ""
success "Permissions seeder test completed!"
echo "=================================================================================="