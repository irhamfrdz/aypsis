<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding Missing Pergerakan Kapal Permissions ===\n\n";

$missingPermissions = [
    [
        'name' => 'pergerakan-kapal-approve',
        'description' => 'Permission to approve pergerakan kapal records'
    ],
    [
        'name' => 'pergerakan-kapal-print',
        'description' => 'Permission to print pergerakan kapal records'
    ],
    [
        'name' => 'pergerakan-kapal-export',
        'description' => 'Permission to export pergerakan kapal data'
    ]
];

foreach ($missingPermissions as $permission) {
    // Check if permission already exists
    $exists = DB::table('permissions')
        ->where('name', $permission['name'])
        ->exists();

    if ($exists) {
        echo "✓ Permission '{$permission['name']}' already exists\n";
        continue;
    }

    // Insert new permission
    DB::table('permissions')->insert([
        'name' => $permission['name'],
        'description' => $permission['description'],
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "✓ Added permission: {$permission['name']}\n";
}

echo "\n=== Verification ===\n";
$pergerakanPermissions = DB::table('permissions')
    ->where('name', 'like', 'pergerakan-kapal%')
    ->orderBy('name')
    ->get(['id', 'name', 'description']);

echo "Total Pergerakan Kapal permissions: " . $pergerakanPermissions->count() . "\n\n";

foreach ($pergerakanPermissions as $perm) {
    echo "  [{$perm->id}] {$perm->name} - {$perm->description}\n";
}

// Assign new permissions to admin users
echo "\n=== Assigning to Admin Users ===\n";

$adminUsers = DB::table('users')
    ->where('username', 'like', '%admin%')
    ->orWhere('username', 'like', '%superadmin%')
    ->get();

$newPermissionIds = DB::table('permissions')
    ->whereIn('name', ['pergerakan-kapal-approve', 'pergerakan-kapal-print', 'pergerakan-kapal-export'])
    ->pluck('id')
    ->toArray();

foreach ($adminUsers as $user) {
    foreach ($newPermissionIds as $permissionId) {
        // Check if already assigned
        $exists = DB::table('permission_user')
            ->where('user_id', $user->id)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$exists) {
            DB::table('permission_user')->insert([
                'user_id' => $user->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Assigned permission ID {$permissionId} to user: {$user->username}\n";
        }
    }
}

echo "\n✅ Script completed successfully!\n";
