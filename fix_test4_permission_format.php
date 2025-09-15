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

echo '=== FIXING USER TEST4 PERMISSION FORMAT ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'BEFORE - Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

// Find the permissions to replace
$oldPermission = Permission::where('name', 'permohonan-create')->first();
$newPermission = Permission::where('name', 'permohonan.create')->first();

if (!$oldPermission) {
    echo PHP_EOL . '❌ Old permission permohonan-create not found in database' . PHP_EOL;
    exit;
}

if (!$newPermission) {
    echo PHP_EOL . '❌ New permission permohonan.create not found in database' . PHP_EOL;
    exit;
}

echo PHP_EOL . 'Replacing permission:' . PHP_EOL;
echo '- REMOVE: ' . $oldPermission->name . ' (ID: ' . $oldPermission->id . ')' . PHP_EOL;
echo '- ADD: ' . $newPermission->name . ' (ID: ' . $newPermission->id . ')' . PHP_EOL;

// Remove old permission and add new permission
$user->permissions()->detach($oldPermission->id);
$user->permissions()->attach($newPermission->id);

echo PHP_EOL . '✅ Permission updated successfully!' . PHP_EOL;

echo PHP_EOL . 'AFTER - Updated permissions:' . PHP_EOL;
$user->refresh(); // Refresh to get updated permissions
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL . '=== VERIFICATION ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan-create"): ' . ($user->hasPermissionTo('permohonan-create') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL . '🎉 User test4 should now be able to access "Buat Permohonan" menu!' . PHP_EOL;
