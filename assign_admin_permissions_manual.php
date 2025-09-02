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
use Illuminate\Support\Facades\DB;

// Get admin user (assuming user ID 1 is admin)
$admin = User::find(1);
$permissionName = 'master-pranota';

if ($admin) {
    echo "Admin user: " . $admin->name . "\n";

    // Get permission ID
    $permission = Permission::where('name', $permissionName)->first();

    if ($permission) {
        echo "Permission ID: " . $permission->id . "\n";

        // Check if relationship already exists
        $exists = DB::table('user_permissions')
            ->where('user_id', $admin->id)
            ->where('permission_id', $permission->id)
            ->exists();

        if (!$exists) {
            // Insert the relationship
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Added '$permissionName' permission to admin\n";
        } else {
            echo "Admin already has '$permissionName' permission\n";
        }

        // Reload admin permissions
        $admin = $admin->fresh();
        $admin->load('permissions');

        echo "\nAdmin permissions:\n";
        foreach ($admin->permissions as $perm) {
            echo "- " . $perm->name . "\n";
        }

        // Test hasPermissionTo method
        echo "\nTesting hasPermissionTo('master-pranota'): " . ($admin->hasPermissionTo('master-pranota') ? 'YES' : 'NO') . "\n";

    } else {
        echo "Permission '$permissionName' not found!\n";
    }
} else {
    echo "Admin user not found!\n";
}

echo "\nCompleted!\n";
