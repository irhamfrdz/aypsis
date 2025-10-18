<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration and bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "Adding master kapal permissions to admin user...\n";

// Find admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Found admin user: {$admin->username}\n";

// Get master kapal permissions (sesuai dengan routes)
$masterKapalPermissions = Permission::whereIn('name', [
    'master-kapal.view',
    'master-kapal.create',
    'master-kapal.edit',
    'master-kapal.delete',
    'master-kapal.print',
    'master-kapal.export'
])->get();

if ($masterKapalPermissions->count() === 0) {
    echo "❌ No master kapal permissions found! Please run add_master_kapal_permissions.php first.\n";
    exit(1);
}

echo "Found {$masterKapalPermissions->count()} master kapal permissions\n";

// Get current admin permissions
$currentPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();
$newPermissionIds = $masterKapalPermissions->pluck('id')->toArray();

// Check which permissions are new
$permissionsToAdd = array_diff($newPermissionIds, $currentPermissionIds);

if (empty($permissionsToAdd)) {
    echo "✓ Admin user already has all master kapal permissions\n";
} else {
    // Add new permissions (without removing existing ones)
    $admin->permissions()->syncWithoutDetaching($newPermissionIds);
    echo "✓ Added " . count($permissionsToAdd) . " new master kapal permissions to admin user\n";
}

// Verify permissions were added (sesuai dengan routes)
$adminPermissions = $admin->permissions()->whereIn('name', [
    'master-kapal.view',
    'master-kapal.create',
    'master-kapal.edit',
    'master-kapal.delete',
    'master-kapal.print',
    'master-kapal.export'
])->get();

echo "\nVerification - Admin now has these master kapal permissions:\n";
foreach ($adminPermissions as $permission) {
    echo "✓ {$permission->name} - {$permission->description}\n";
}

echo "\nTotal admin permissions: " . $admin->permissions()->count() . "\n";
echo "Master kapal permissions for admin: " . $adminPermissions->count() . "/6\n";

if ($adminPermissions->count() === 6) {
    echo "\n✅ Successfully added all master kapal permissions to admin user!\n";
} else {
    echo "\n⚠️  Some master kapal permissions might be missing. Please check manually.\n";
}
