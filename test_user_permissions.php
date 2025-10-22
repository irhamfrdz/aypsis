<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing User Permission System ===\n\n";

$admin = User::where('username', 'admin')->first();
if ($admin) {
    echo "Admin user found: {$admin->username}\n";

    // Test hasPermissionTo directly
    $hasPermDirect = $admin->hasPermissionTo('master-pelabuhan-view');
    echo "hasPermissionTo('master-pelabuhan-view'): " . ($hasPermDirect ? 'YES' : 'NO') . "\n";

    // Test can method
    $canAccess = $admin->can('master-pelabuhan-view');
    echo "can('master-pelabuhan-view'): " . ($canAccess ? 'YES' : 'NO') . "\n";

    // Check permissions collection
    $perms = $admin->permissions;
    echo "Permissions loaded: " . ($perms ? 'YES' : 'NO') . "\n";
    echo "Total permissions: " . $perms->count() . "\n";

    // Find the specific permission
    $specificPerm = $perms->where('name', 'master-pelabuhan-view')->first();
    echo "Found master-pelabuhan-view permission: " . ($specificPerm ? 'YES - ID: ' . $specificPerm->id : 'NO') . "\n";

    // Test other permissions
    echo "\n--- Testing other pelabuhan permissions ---\n";
    $pelabuhanPerms = ['master-pelabuhan-create', 'master-pelabuhan-edit', 'master-pelabuhan-delete'];
    foreach($pelabuhanPerms as $perm) {
        $hasPerm = $admin->can($perm);
        echo "can('{$perm}'): " . ($hasPerm ? 'YES' : 'NO') . "\n";
    }

} else {
    echo "Admin user not found!\n";
}
