#!/bin/bash

# User Admin Seeder Runner Script
# This script runs the UserAdminSeeder to create user_admin with all permissions

echo "🚀 Starting User Admin Seeder..."
echo "================================="

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

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    print_error "Artisan file not found. Please run this script from the Laravel project root directory."
    exit 1
fi

print_status "Checking Laravel environment..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Please ensure your environment is properly configured."
fi

# Create backup of database (optional but recommended)
print_status "Creating database backup before seeding..."
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"

if command -v mysqldump &> /dev/null; then
    # Try to extract database credentials from .env file
    if [ -f ".env" ]; then
        DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2 | tr -d '"')
        DB_PORT=$(grep "^DB_PORT=" .env | cut -d '=' -f2 | tr -d '"')
        DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2 | tr -d '"')
        DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2 | tr -d '"')
        DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d '"')

        # Set defaults if not found
        DB_HOST=${DB_HOST:-"localhost"}
        DB_PORT=${DB_PORT:-"3306"}
        DB_DATABASE=${DB_DATABASE:-"laravel"}
        DB_USERNAME=${DB_USERNAME:-"root"}
        DB_PASSWORD=${DB_PASSWORD:-""}

        print_status "Creating backup: $BACKUP_FILE"
        mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null

        if [ $? -eq 0 ]; then
            print_success "Database backup created successfully: $BACKUP_FILE"
        else
            print_warning "Failed to create database backup. Continuing without backup..."
        fi
    else
        print_warning "Cannot create backup: .env file not found"
    fi
else
    print_warning "mysqldump not found. Skipping database backup..."
fi

echo ""
print_status "Running UserAdminSeeder..."
echo "================================="

# Run the UserAdminSeeder
php artisan db:seed --class=UserAdminSeeder

# Check the exit code
if [ $? -eq 0 ]; then
    print_success "UserAdminSeeder completed successfully!"
    echo ""
    print_status "User Admin Credentials:"
    echo "  Username: user_admin"
    echo "  Password: admin123"
    echo "  Status: approved (auto-approved)"
    echo ""
    print_success "The user_admin has been created with ALL permissions!"
    print_warning "Remember to change the default password after first login."
else
    print_error "UserAdminSeeder failed! Please check the error messages above."
    exit 1
fi

echo ""
print_status "Verifying user_admin creation..."

# Verify the user was created
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::where('username', 'user_admin')->count();" 2>/dev/null)

if [ "$USER_COUNT" = "1" ]; then
    print_success "Verification successful: user_admin created!"
else
    print_warning "Could not verify user creation automatically."
fi

echo ""
print_success "🎉 User Admin Seeder process completed!"
print_status "You can now login with user_admin / admin123"