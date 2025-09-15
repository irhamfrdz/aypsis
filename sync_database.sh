#!/bin/bash

# Database Synchronization Script
# This script will sync server database with laptop database

echo "🚀 Starting Database Synchronization..."
echo "======================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

# Backup reminder
print_warning "IMPORTANT: Make sure you have backed up your database before proceeding!"
echo ""
read -p "Have you backed up your database? (y/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_error "Please backup your database first, then run this script again."
    exit 1
fi

# Check database connection
print_status "Checking database connection..."
php artisan migrate:status > /dev/null 2>&1
if [ $? -ne 0 ]; then
    print_error "Cannot connect to database. Please check your .env file."
    exit 1
fi
print_success "Database connection OK"

# Run the synchronization seeder
print_status "Starting database synchronization..."
echo ""

php artisan db:seed --class=DatabaseSyncSeeder

if [ $? -eq 0 ]; then
    print_success "Database synchronization completed successfully!"
    echo ""
    print_status "Verifying synchronization..."

    # Count records
    PERMISSIONS_COUNT=$(php artisan tinker --execute="echo DB::table('permissions')->count();" | tail -1)
    USERS_COUNT=$(php artisan tinker --execute="echo DB::table('users')->count();" | tail -1)
    USER_PERMISSIONS_COUNT=$(php artisan tinker --execute="echo DB::table('user_permissions')->count();" | tail -1)

    echo ""
    print_success "Synchronization Summary:"
    echo "  📊 Permissions: $PERMISSIONS_COUNT (should be 381)"
    echo "  👥 Users: $USERS_COUNT (should be 7)"
    echo "  🔐 User Permissions: $USER_PERMISSIONS_COUNT"

    # Verify admin permissions
    ADMIN_PERMISSIONS=$(php artisan tinker --execute="echo DB::table('user_permissions')->where('user_id', 1)->count();" | tail -1)
    echo "  👑 Admin Permissions: $ADMIN_PERMISSIONS (should be 381)"

    echo ""
    print_success "🎉 Database is now synced with laptop database!"
    print_status "You can now test the application with the updated permissions."

else
    print_error "Database synchronization failed!"
    print_status "Please check the error messages above and try again."
    exit 1
fi
