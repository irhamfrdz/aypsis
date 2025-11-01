<?php

use App\Models\User;
use App\Models\Permission;

echo "=== DEBUG: Sidebar Permission Mismatch for Pranota Rit Menus ===\n\n";

// 1. Check what permissions the sidebar expects
echo "1. Sidebar expects these permissions:\n";
echo "   - pranota-uang-rit-view (for Pranota Rit menu)\n";
echo "   - pranota-uang-kenek-view (for Pranota Rit Kenek menu)\n\n";

// 2. Check if these permissions exist in database
echo "2. Checking if sidebar permissions exist in database:\n";
$sidebarPermissions = ['pranota-uang-rit-view', 'pranota-uang-kenek-view'];

foreach ($sidebarPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "   ✓ Found: {$permName} (ID: {$permission->id})\n";
    } else {
        echo "   ✗ Missing: {$permName}\n";
    }
}
echo "\n";

// 3. Check what permissions admin user currently has
echo "3. Checking admin user's current pranota-related permissions:\n";
$adminUser = User::where('username', 'admin')->first();

if ($adminUser) {
    $pranotaPermissions = $adminUser->permissions()->where('name', 'like', 'pranota%')->orderBy('name')->get();
    
    foreach ($pranotaPermissions as $permission) {
        echo "   - {$permission->name} (ID: {$permission->id})\n";
    }
    
    echo "\n4. Checking if admin has sidebar permissions:\n";
    foreach ($sidebarPermissions as $permName) {
        $hasPermission = $adminUser->permissions()->where('name', $permName)->exists();
        $status = $hasPermission ? '✓ HAS' : '✗ MISSING';
        echo "   {$status}: {$permName}\n";
    }
} else {
    echo "   ERROR: Admin user not found!\n";
}

echo "\n=== DEBUG COMPLETE ===\n";