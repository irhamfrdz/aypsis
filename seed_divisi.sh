#!/bin/bash

# Divisi Seeder Runner Script
# Usage: ./seed_divisi.sh [environment]

ENVIRONMENT=${1:-"production"}
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "=========================================="
echo "   Master Divisi Seeder Runner"
echo "=========================================="
echo "Environment: $ENVIRONMENT"
echo "Directory: $SCRIPT_DIR"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from Laravel project root."
    exit 1
fi

# Check if DivisiSeeder exists
if [ ! -f "database/seeders/DivisiSeeder.php" ]; then
    echo "❌ Error: DivisiSeeder.php not found in database/seeders/"
    exit 1
fi

echo "✅ Laravel project detected"
echo "✅ DivisiSeeder found"
echo ""

# Function to run seeder
run_seeder() {
    local seeder_class=$1
    local description=$2

    echo "🔄 Running $description..."
    echo "Command: php artisan db:seed --class=$seeder_class"
    echo ""

    if php artisan db:seed --class=$seeder_class; then
        echo "✅ $description completed successfully!"
        echo ""
    else
        echo "❌ Error running $description"
        exit 1
    fi
}

# Main execution
echo "Starting Divisi Seeder..."
echo ""

# Run Divisi Seeder
run_seeder "DivisiSeeder" "Master Divisi Seeder"

echo "=========================================="
echo "   Seeder Execution Summary"
echo "=========================================="
echo "✅ Master Divisi data has been seeded"
echo ""
echo "Next steps:"
echo "1. Check the web interface: Master → Divisi"
echo "2. Verify data in database:"
echo "   php artisan tinker"
echo "   App\\Models\\Divisi::count()"
echo ""
echo "=========================================="
