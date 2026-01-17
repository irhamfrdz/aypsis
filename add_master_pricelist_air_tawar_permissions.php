<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding Master Pricelist Air Tawar Permissions ===\n\n";

// Define permissions
$permissions = [
    'master-pricelist-air-tawar-view' => 'View Master Pricelist Air Tawar',
    'master-pricelist-air-tawar-create' => 'Create Master Pricelist Air Tawar',
    'master-pricelist-air-tawar-update' => 'Update Master Pricelist Air Tawar',
    'master-pricelist-air-tawar-delete' => 'Delete Master Pricelist Air Tawar',
];

$createdCount = 0;
$existingCount = 0;

// Create permissions
foreach ($permissions as $permissionName => $description) {
    $existing = DB::table('permissions')->where('name', $permissionName)->first();
    
    if (!$existing) {
        DB::table('permissions')->insert([
            'name' => $permissionName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Created permission: {$permissionName}\n";
        $createdCount++;
    } else {
        echo "- Permission already exists: {$permissionName}\n";
        $existingCount++;
    }
}

echo "\n";

// Assign all permissions to admin role
$adminRole = DB::table('roles')->where('name', 'admin')->first();

if ($adminRole) {
    echo "=== Assigning Permissions to Admin Role ===\n\n";
    
    foreach (array_keys($permissions) as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if ($permission) {
            $hasPermission = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $permission->id)
                ->exists();
            
            if (!$hasPermission) {
                DB::table('permission_role')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $adminRole->id,
                ]);
                echo "✓ Assigned '{$permissionName}' to admin role\n";
            } else {
                echo "- Admin already has '{$permissionName}' permission\n";
            }
        }
    }
} else {
    echo "⚠ Warning: Admin role not found! Permissions created but not assigned.\n";
}

echo "\n=== Summary ===\n";
echo "Created: {$createdCount} permissions\n";
echo "Already existed: {$existingCount} permissions\n";
echo "Total permissions: " . count($permissions) . "\n";
echo "\n✓ Done!\n";
