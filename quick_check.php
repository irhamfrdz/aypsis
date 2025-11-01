<?php

// Simple permission check
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';

// Bootstrap Laravel
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== QUICK PERMISSION CHECK ===\n";

// Check sidebar permissions
$sidebarPermissions = ['pranota-uang-rit-view', 'pranota-uang-kenek-view'];

foreach ($sidebarPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "✓ Found: {$permName} (ID: {$permission->id})\n";
    } else {
        echo "✗ Missing: {$permName}\n";
    }
}

// Check admin
$admin = User::where('username', 'admin')->first();
if ($admin) {
    echo "\nAdmin permissions:\n";
    foreach ($sidebarPermissions as $permName) {
        $has = $admin->permissions()->where('name', $permName)->exists();
        echo ($has ? '✓' : '✗') . " {$permName}\n";
    }
}

echo "\n=== DONE ===\n";