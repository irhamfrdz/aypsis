<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Fix Pranota Rit Menu Issue ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "\n🔍 Problem Analysis:\n";
echo "   • Sidebar uses: pranota-uang-rit-view\n";
echo "   • Admin has: pranota-rit-view\n";
echo "   • Missing permission for menu visibility\n";

// Check missing permissions
$requiredPermissions = [
    'pranota-uang-rit-view',
    'pranota-uang-kenek-view'
];

$currentPermissions = $admin->permissions()->pluck('name')->toArray();
$missingPermissions = [];

echo "\n📊 Permission Status Check:\n";
foreach ($requiredPermissions as $requiredPerm) {
    if (in_array($requiredPerm, $currentPermissions)) {
        echo "   ✅ {$requiredPerm} - Already has\n";
    } else {
        $permission = Permission::where('name', $requiredPerm)->first();
        if ($permission) {
            $missingPermissions[] = $permission->id;
            echo "   ❌ {$requiredPerm} - Missing (ID: {$permission->id})\n";
        } else {
            echo "   ⚠️  {$requiredPerm} - Permission does not exist in database\n";
        }
    }
}

// Add missing permissions
if (!empty($missingPermissions)) {
    echo "\n🔧 Adding missing permissions to admin user...\n";
    $admin->permissions()->attach($missingPermissions);
    
    foreach ($missingPermissions as $permId) {
        $perm = Permission::find($permId);
        echo "   ✅ Added: {$perm->name}\n";
    }
} else {
    echo "\n→ No missing permissions to add\n";
}

// Final verification
echo "\n🎯 Final Verification:\n";
$finalPermissionCount = $admin->permissions()->count();
echo "   • Admin total permissions: {$finalPermissionCount}\n";

// Check if admin now has required permissions for menu
$hasUangRitView = $admin->permissions()->where('name', 'pranota-uang-rit-view')->exists();
$hasUangKenekView = $admin->permissions()->where('name', 'pranota-uang-kenek-view')->exists();

echo "\n📋 Menu Visibility Check:\n";
echo "   • Pranota Uang Rit menu: " . ($hasUangRitView ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";
echo "   • Pranota Uang Kenek menu: " . ($hasUangKenekView ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";

if ($hasUangRitView && $hasUangKenekView) {
    echo "\n🎉 SUCCESS! Both menus should now be visible in sidebar.\n";
    echo "\n📝 Next Steps:\n";
    echo "   1. Clear browser cache: Ctrl+F5\n";
    echo "   2. Logout and login again\n";
    echo "   3. Check sidebar under 'Aktivitas' section\n";
} else {
    echo "\n❌ Some permissions are still missing. Check the issues above.\n";
}

echo "\n✨ Fix complete!\n";

?>