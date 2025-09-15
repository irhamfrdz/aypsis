<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Restoring missing permissions for user test4\n";
echo "===========================================\n\n";

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Current permissions
echo "Current permissions:\n";
foreach($user->permissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Permissions to restore
$permissionsToRestore = [
    'tagihan-kontainer-view' => 265,
    'tagihan-kontainer-create' => 266,
    'tagihan-kontainer-update' => 267,
    'tagihan-kontainer-delete' => 268,
    'tagihan-kontainer-approve' => 269,
    'tagihan-kontainer-print' => 270,
    'tagihan-kontainer-export' => 271,
    'master-pranota-tagihan-kontainer' => 133
];

$permissionIds = [];
echo "Checking permissions to restore:\n";
foreach ($permissionsToRestore as $name => $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "  ✅ $name (ID: $id) exists in database\n";
        $permissionIds[] = $id;
    } else {
        echo "  ❌ $name (ID: $id) NOT FOUND in database\n";
    }
}
echo "\n";

// Add the missing permissions to current permissions
$currentPermissionIds = $user->permissions->pluck('id')->toArray();
$allPermissionIds = array_unique(array_merge($currentPermissionIds, $permissionIds));

echo "Current permission IDs: " . implode(', ', $currentPermissionIds) . "\n";
echo "Permission IDs to add: " . implode(', ', $permissionIds) . "\n";
echo "Final permission IDs: " . implode(', ', $allPermissionIds) . "\n\n";

// Sync permissions
$user->permissions()->sync($allPermissionIds);
echo "✅ Permissions synced successfully\n\n";

// Verify
$user->refresh();
echo "Final permissions after sync:\n";
foreach($user->permissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}

echo "\nTest completed!\n";
