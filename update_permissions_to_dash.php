<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Updating Pergerakan Kapal Permissions to Use Dashes ===\n\n";

// 1. Create new permissions with dashes
$newPermissions = [
    ['name' => 'pergerakan-kapal-view', 'description' => 'View pergerakan kapal'],
    ['name' => 'pergerakan-kapal-create', 'description' => 'Create pergerakan kapal'],
    ['name' => 'pergerakan-kapal-update', 'description' => 'Update pergerakan kapal'],
    ['name' => 'pergerakan-kapal-delete', 'description' => 'Delete pergerakan kapal'],
];

echo "1. Creating new permissions with dashes:\n";
foreach ($newPermissions as $permissionData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permissionData['name']],
        ['description' => $permissionData['description']]
    );
    echo "   ✓ {$permissionData['name']}\n";
}

// 2. Get admin user
$admin = User::where('username', 'admin')->first();
if (!$admin) {
    $admin = User::where('role', 'admin')->first();
}

if ($admin) {
    echo "\n2. Assigning new permissions to admin user:\n";

    foreach ($newPermissions as $permissionData) {
        $permission = Permission::where('name', $permissionData['name'])->first();

        // Check if user already has this permission
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $admin->id)
            ->where('permission_id', $permission->id)
            ->exists();

        if (!$hasPermission) {
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "   ✓ Assigned {$permissionData['name']}\n";
        } else {
            echo "   ◯ Already has {$permissionData['name']}\n";
        }
    }

    // 3. Remove old permissions with dots (optional - keeping for compatibility)
    echo "\n3. Old permissions with dots will be kept for compatibility\n";

} else {
    echo "\n❌ Admin user not found!\n";
}

echo "\n✅ Update completed! Now routes will work with dash format permissions.\n";
