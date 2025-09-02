<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Permission;

// Get admin user (assuming user ID 1 is admin)
$admin = User::find(1);

if ($admin) {
    echo "Admin user: " . $admin->name . "\n";

    // Give master-pranota permission
    $permission = Permission::where('name', 'master-pranota')->first();
    if ($permission) {
        if (!$admin->hasPermissionTo('master-pranota')) {
            $admin->givePermissionTo('master-pranota');
            echo "Gave 'master-pranota' permission to admin\n";
        } else {
            echo "Admin already has 'master-pranota' permission\n";
        }
    }

    // Check all admin permissions
    echo "\nAdmin permissions:\n";
    foreach ($admin->getAllPermissions() as $permission) {
        echo "- " . $permission->name . "\n";
    }
} else {
    echo "Admin user not found!\n";
}

echo "\nCompleted!\n";
