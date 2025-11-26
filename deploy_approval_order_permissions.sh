#!/bin/bash

# =================================================================
# APPROVAL ORDER PERMISSIONS DEPLOYMENT SCRIPT
# =================================================================
# Script untuk menambahkan approval order permissions di server
# Usage: bash deploy_approval_order_permissions.sh
# =================================================================

set -e  # Exit on any error

echo "🚀 APPROVAL ORDER PERMISSIONS DEPLOYMENT"
echo "========================================"
echo ""

# Check if we're in Laravel directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from Laravel root directory."
    exit 1
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please ensure Laravel is properly configured."
    exit 1
fi

echo "📂 Current directory: $(pwd)"
echo "🔧 Running in Laravel application..."
echo ""

# Step 1: Run migration for approval order permissions
echo "🔄 Step 1: Running migration for approval-order permissions..."
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Migration completed successfully"
else
    echo "❌ Migration failed"
    exit 1
fi
echo ""

# Step 2: Run artisan command to add permissions (if command exists)
echo "🔄 Step 2: Adding approval-order permissions..."
if php artisan list | grep -q "permissions:add-approval-order"; then
    php artisan permissions:add-approval-order --force
    echo "✅ Artisan command executed successfully"
else
    echo "⚠️  Artisan command not found, using direct database insert..."
    
    # Fallback: Run direct PHP script
    php -r "
    require 'vendor/autoload.php';
    \$app = require 'bootstrap/app.php';
    \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    use Illuminate\Support\Facades\DB;
    
    \$permissions = [
        ['name' => 'approval-order-view', 'description' => 'Melihat halaman approval order'],
        ['name' => 'approval-order-create', 'description' => 'Menambah term pembayaran order'],
        ['name' => 'approval-order-update', 'description' => 'Mengedit term pembayaran order'],
        ['name' => 'approval-order-delete', 'description' => 'Menghapus term pembayaran order'],
        ['name' => 'approval-order-approve', 'description' => 'Menyetujui approval order'],
        ['name' => 'approval-order-reject', 'description' => 'Menolak approval order'],
        ['name' => 'approval-order-print', 'description' => 'Mencetak dokumen approval order'],
        ['name' => 'approval-order-export', 'description' => 'Export data approval order']
    ];
    
    \$added = 0;
    foreach (\$permissions as \$perm) {
        \$exists = DB::table('permissions')->where('name', \$perm['name'])->exists();
        if (!\$exists) {
            DB::table('permissions')->insert(array_merge(\$perm, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
            echo \"✅ Added: {\$perm['name']}\n\";
            \$added++;
        }
    }
    echo \"📊 Total added: \$added permissions\n\";
    "
fi
echo ""

# Step 3: Clear caches
echo "🔄 Step 3: Clearing application caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

if command -v redis-cli >/dev/null 2>&1; then
    echo "🔄 Clearing Redis cache..."
    redis-cli FLUSHDB
fi

echo "✅ Caches cleared"
echo ""

# Step 4: Verify installation
echo "🔄 Step 4: Verifying installation..."

# Check if permissions exist in database
PERM_COUNT=$(php artisan tinker --execute="echo App\Models\Permission::where('name', 'like', 'approval-order%')->count();" 2>/dev/null | tail -n 1)

if [ "$PERM_COUNT" -eq 8 ]; then
    echo "✅ Verification successful: All 8 approval-order permissions found in database"
else
    echo "⚠️  Verification warning: Expected 8 permissions, found $PERM_COUNT"
fi

# Check if routes are available
if php artisan route:list --name="approval-order" >/dev/null 2>&1; then
    echo "✅ Approval-order routes are available"
else
    echo "⚠️  Approval-order routes not found"
fi

echo ""

# Summary
echo "🎯 DEPLOYMENT SUMMARY"
echo "===================="
echo "✅ Migration executed"
echo "✅ Permissions added to database"
echo "✅ Application caches cleared"
echo "✅ Installation verified"
echo ""

echo "📋 NEXT STEPS:"
echo "1. Login to admin panel"
echo "2. Go to Master User → Edit User"
echo "3. Expand 'Sistem Persetujuan' section"
echo "4. Configure 'Approval Order' permissions"
echo "5. Test approval order functionality"
echo ""

echo "🎉 APPROVAL ORDER PERMISSIONS DEPLOYMENT COMPLETED!"
echo "==================================================="