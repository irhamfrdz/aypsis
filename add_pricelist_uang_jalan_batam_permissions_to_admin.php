<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration and bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Adding Pricelist Uang Jalan Batam Permissions to Admin User ===\n\n";

// Find admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✓ Found admin user: {$admin->username}\n";

// Define permissions yang diperlukan
$permissionNames = [
    'master-pricelist-uang-jalan-batam-view',
    'master-pricelist-uang-jalan-batam-create',
    'master-pricelist-uang-jalan-batam-edit',
    'master-pricelist-uang-jalan-batam-delete',
];

// Cek apakah permissions sudah ada di database
$existingPermissions = Permission::whereIn('name', $permissionNames)->get();

echo "\nChecking existing permissions in database...\n";

if ($existingPermissions->count() === 0) {
    echo "⚠️  No permissions found in database. Creating them now...\n\n";
    
    // Buat permissions baru
    $permissionsData = [
        [
            'name' => 'master-pricelist-uang-jalan-batam-view',
            'description' => 'View Pricelist Uang Jalan Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'master-pricelist-uang-jalan-batam-create',
            'description' => 'Create Pricelist Uang Jalan Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'master-pricelist-uang-jalan-batam-edit',
            'description' => 'Edit Pricelist Uang Jalan Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'master-pricelist-uang-jalan-batam-delete',
            'description' => 'Delete Pricelist Uang Jalan Batam',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];

    foreach ($permissionsData as $permData) {
        $permission = Permission::create($permData);
        echo "✓ Created permission: {$permission->name}\n";
    }

    // Refresh permissions list
    $existingPermissions = Permission::whereIn('name', $permissionNames)->get();
    echo "\n✅ Created {$existingPermissions->count()} permissions\n";
} else {
    echo "✓ Found {$existingPermissions->count()} existing permissions\n";
    
    // Jika tidak semua permissions ada, buat yang kurang
    if ($existingPermissions->count() < count($permissionNames)) {
        $existingNames = $existingPermissions->pluck('name')->toArray();
        $missingNames = array_diff($permissionNames, $existingNames);
        
        echo "\n⚠️  Missing permissions found. Creating them...\n";
        
        foreach ($missingNames as $missingName) {
            $description = ucwords(str_replace(['-', 'master-pricelist-uang-jalan-batam-'], [' ', ''], $missingName));
            $permission = Permission::create([
                'name' => $missingName,
                'description' => $description . ' Pricelist Uang Jalan Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Created missing permission: {$permission->name}\n";
        }
        
        // Refresh permissions list
        $existingPermissions = Permission::whereIn('name', $permissionNames)->get();
    }
}

echo "\n";

// Get current admin permissions
$currentPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();
$newPermissionIds = $existingPermissions->pluck('id')->toArray();

// Check which permissions are new
$permissionsToAdd = array_diff($newPermissionIds, $currentPermissionIds);

if (empty($permissionsToAdd)) {
    echo "✓ Admin user already has all pricelist uang jalan batam permissions\n";
} else {
    // Add new permissions (without removing existing ones)
    $admin->permissions()->syncWithoutDetaching($newPermissionIds);
    echo "✓ Added " . count($permissionsToAdd) . " new pricelist uang jalan batam permissions to admin user\n";
}

// Verify permissions were added
$adminPermissions = $admin->permissions()
    ->whereIn('name', $permissionNames)
    ->get();

echo "\n=== Verification ===\n";
echo "Admin now has these pricelist uang jalan batam permissions:\n";
foreach ($adminPermissions as $permission) {
    echo "  ✓ {$permission->name} - {$permission->description}\n";
}

echo "\nTotal admin permissions: " . $admin->permissions()->count() . "\n";
echo "Pricelist Uang Jalan Batam permissions for admin: " . $adminPermissions->count() . "/" . count($permissionNames) . "\n";

if ($adminPermissions->count() === count($permissionNames)) {
    echo "\n✅ Successfully added all pricelist uang jalan batam permissions to admin user!\n";
} else {
    echo "\n⚠️  Some pricelist uang jalan batam permissions might be missing. Please check manually.\n";
}
