<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔍 Testing user test4 permission update\n";
echo "=======================================\n\n";

$user = User::where('username', 'test4')->first();
if ($user) {
    echo "User test4 permissions before update:\n";
    foreach ($user->permissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
    echo "\n";

    // Update permissions - only give the correct ones, remove legacy
    $controller = new \App\Http\Controllers\UserController();
    $newPermissionIds = $controller->testConvertMatrixPermissionsToIds(['master-karyawan' => ['view' => '1']]);

    // Keep dashboard permission, replace karyawan permissions
    $currentPermissions = $user->permissions->pluck('id')->toArray();
    $dashboardPermission = \App\Models\Permission::where('name', 'dashboard')->first();

    $finalPermissions = [$dashboardPermission->id];
    $finalPermissions = array_merge($finalPermissions, $newPermissionIds);

    $user->permissions()->sync($finalPermissions);

    echo "User test4 permissions after update:\n";
    foreach ($user->permissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
    echo "\n";

    // Test permission checks
    echo "Permission checks:\n";
    echo "Can master-karyawan.view: " . ($user->can('master-karyawan.view') ? 'YES' : 'NO') . "\n";
    echo "Can master.karyawan.show: " . ($user->can('master.karyawan.show') ? 'YES' : 'NO') . "\n";

    echo "\n✅ User test4 updated successfully!\n";
} else {
    echo "❌ User test4 not found\n";
}

?>
