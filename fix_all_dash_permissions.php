<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo '=== FIXING REMAINING DASH FORMAT PERMISSIONS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'BEFORE - Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

// Check for any remaining dash format permissions
$userPerms = $user->permissions->pluck('name')->toArray();
$dashPermissions = array_filter($userPerms, function($perm) {
    return str_contains($perm, '-') && str_contains($perm, 'permohonan');
});

if (empty($dashPermissions)) {
    echo PHP_EOL . '✅ No dash format permissions found - all permissions are already in correct format!' . PHP_EOL;
    exit;
}

echo PHP_EOL . 'Found dash format permissions that need to be replaced:' . PHP_EOL;
foreach ($dashPermissions as $dashPerm) {
    echo '- ' . $dashPerm . PHP_EOL;
}

echo PHP_EOL . 'Replacing permissions:' . PHP_EOL;

// Replace each dash permission with dot equivalent
foreach ($dashPermissions as $dashPerm) {
    $dotPerm = str_replace('-', '.', $dashPerm);

    $oldPermission = Permission::where('name', $dashPerm)->first();
    $newPermission = Permission::where('name', $dotPerm)->first();

    if ($oldPermission && $newPermission) {
        echo '- ' . $dashPerm . ' → ' . $dotPerm . PHP_EOL;

        // Remove old permission and add new permission
        $user->permissions()->detach($oldPermission->id);
        $user->permissions()->attach($newPermission->id);
    } else {
        echo '❌ Could not find permissions: ' . $dashPerm . ' or ' . $dotPerm . PHP_EOL;
    }
}

echo PHP_EOL . '✅ All permissions updated successfully!' . PHP_EOL;

echo PHP_EOL . 'AFTER - Updated permissions:' . PHP_EOL;
$user->refresh(); // Refresh to get updated permissions
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL . '=== FINAL VERIFICATION ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.edit"): ' . ($user->hasPermissionTo('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan-create"): ' . ($user->hasPermissionTo('permohonan-create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan-edit"): ' . ($user->hasPermissionTo('permohonan-edit') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL . '🎉 All user test4 permissions are now in the correct format!' . PHP_EOL;
