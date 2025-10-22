<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use App\Models\User;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Master Pelabuhan Permissions ===\n\n";

// Check if permissions exist
$permissions = Permission::where('name', 'LIKE', '%pelabuhan%')->get(['name']);
echo "Found " . $permissions->count() . " pelabuhan permissions:\n";
foreach($permissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n=== Checking Admin User Permissions ===\n\n";

// Check admin user
$admin = User::where('username', 'admin')->first();
if ($admin) {
    echo "Admin user found: {$admin->username}\n";

    $adminPerms = $admin->permissions()->where('name', 'LIKE', '%pelabuhan%')->get(['name']);
    echo "Admin has {$adminPerms->count()} pelabuhan permissions:\n";
    foreach($adminPerms as $perm) {
        echo "- {$perm->name}\n";
    }

    // Check specific permission
    $hasViewPerm = $admin->permissions()->where('name', 'master-pelabuhan-view')->exists();
    echo "\nDoes admin have master-pelabuhan-view permission? " . ($hasViewPerm ? 'YES' : 'NO') . "\n";

    // Check if User model has can method
    if (method_exists($admin, 'can')) {
        $canAccess = $admin->can('master-pelabuhan-view');
        echo "Can admin access via can() method? " . ($canAccess ? 'YES' : 'NO') . "\n";
    }

} else {
    echo "Admin user not found!\n";
}

// Check all permissions in database
echo "\n=== All Master Pelabuhan Related Permissions ===\n";
$allPermissions = Permission::all(['name']);
foreach($allPermissions as $perm) {
    if (strpos($perm->name, 'pelabuhan') !== false) {
        echo "- {$perm->name}\n";
    }
}
