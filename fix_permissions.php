<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "Checking permissions...\n";

$admin = User::find(1);
if (!$admin) {
    echo "Admin user not found!\n";
    exit;
}

echo "Admin user: " . $admin->name . "\n";

// Get pranota surat jalan permissions
$permissions = Permission::where('name', 'LIKE', 'pranota-surat-jalan%')->get();

echo "Found " . $permissions->count() . " pranota surat jalan permissions:\n";
foreach ($permissions as $perm) {
    echo "- ID: " . $perm->id . ", Name: " . $perm->name . "\n";
}

// Check if admin has these permissions
echo "\nChecking admin permissions:\n";
foreach ($permissions as $perm) {
    $has = $admin->hasPermissionTo($perm->name);
    echo "- " . $perm->name . ": " . ($has ? "YES" : "NO") . "\n";

    if (!$has) {
        // Assign permission
        $admin->permissions()->attach($perm->id);
        echo "  -> Assigned!\n";
    }
}

echo "\nDone!\n";
