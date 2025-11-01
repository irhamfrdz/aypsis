<?php

// Fix pranota rit menu visibility issue
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';

// Bootstrap Laravel
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== FIX PRANOTA RIT MENU VISIBILITY ===\n\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "ERROR: Admin user not found!\n";
    exit(1);
}

echo "Admin user found (ID: {$admin->id})\n";

// Get the required sidebar permissions
$requiredPermissions = [
    'pranota-uang-rit-view',
    'pranota-uang-kenek-view'
];

$addedPermissions = [];

foreach ($requiredPermissions as $permName) {
    // Check if permission exists in database
    $permission = Permission::where('name', $permName)->first();
    
    if (!$permission) {
        echo "ERROR: Permission '{$permName}' not found in database!\n";
        continue;
    }
    
    // Check if admin already has this permission
    $hasPermission = $admin->permissions()->where('permission_id', $permission->id)->exists();
    
    if ($hasPermission) {
        echo "✓ Admin already has: {$permName}\n";
    } else {
        // Add permission to admin
        $admin->permissions()->attach($permission->id);
        $addedPermissions[] = $permName;
        echo "✓ Added to admin: {$permName} (ID: {$permission->id})\n";
    }
}

if (!empty($addedPermissions)) {
    echo "\n=== SUMMARY ===\n";
    echo "Added " . count($addedPermissions) . " permissions to admin user:\n";
    foreach ($addedPermissions as $perm) {
        echo "  - {$perm}\n";
    }
    
    // Show final permission count
    $totalPermissions = $admin->permissions()->count();
    echo "\nAdmin now has {$totalPermissions} total permissions.\n";
    
    echo "\n=== SOLUTION ===\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Logout and login again\n";
    echo "3. Check sidebar - Pranota Rit and Pranota Rit Kenek menus should now appear\n";
} else {
    echo "\n=== NO CHANGES NEEDED ===\n";
    echo "Admin already has all required sidebar permissions.\n";
    echo "If menu still not visible, check:\n";
    echo "1. Browser cache (try Ctrl+F5)\n";
    echo "2. Route definitions\n";
    echo "3. Controller access permissions\n";
}

echo "\n=== DONE ===\n";