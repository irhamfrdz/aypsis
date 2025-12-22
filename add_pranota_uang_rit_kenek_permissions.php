<?php

/**
 * Script untuk menambahkan permissions Pranota Uang Rit Kenek
 * 
 * Usage: php add_pranota_uang_rit_kenek_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Define permissions untuk Pranota Uang Rit Kenek
$permissions = [
    'pranota-uang-rit-kenek-view' => 'View Pranota Uang Rit Kenek',
    'pranota-uang-rit-kenek-create' => 'Create Pranota Uang Rit Kenek',
    'pranota-uang-rit-kenek-update' => 'Update Pranota Uang Rit Kenek',
    'pranota-uang-rit-kenek-delete' => 'Delete Pranota Uang Rit Kenek',
    'pranota-uang-rit-kenek-approve' => 'Approve Pranota Uang Rit Kenek',
    'pranota-uang-rit-kenek-mark-paid' => 'Mark Pranota Uang Rit Kenek as Paid',
];

echo "Adding Pranota Uang Rit Kenek permissions...\n";
echo str_repeat('=', 50) . "\n";

foreach ($permissions as $name => $description) {
    $permission = \App\Models\Permission::firstOrCreate(
        ['name' => $name],
        ['description' => $description, 'created_at' => now(), 'updated_at' => now()]
    );
    
    if ($permission->wasRecentlyCreated) {
        echo "✓ Created: {$name}\n";
    } else {
        echo "- Exists: {$name}\n";
    }
}

echo str_repeat('=', 50) . "\n";
echo "Assigning permissions to admin role via database...\n";

// Get admin role ID
$adminRole = \App\Models\Role::where('name', 'admin')->first();
if ($adminRole) {
    foreach (array_keys($permissions) as $permissionName) {
        // Get permission ID
        $permission = \App\Models\Permission::where('name', $permissionName)->first();
        if ($permission) {
            // Check if already assigned
            $exists = \DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $permission->id)
                ->exists();
            
            if (!$exists) {
                \DB::table('permission_role')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id
                ]);
                echo "✓ Assigned to admin: {$permissionName}\n";
            } else {
                echo "- Admin already has: {$permissionName}\n";
            }
        }
    }
    echo "\n✓ All permissions assigned to admin role!\n";
} else {
    echo "\n✗ Admin role not found!\n";
}

echo str_repeat('=', 50) . "\n";
echo "Done!\n";
