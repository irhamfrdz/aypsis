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

echo '=== FIXING USER TEST4 EDIT PERMISSION ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'BEFORE - Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

// Check if user has the wrong format permission
$wrongPermission = Permission::where('name', 'permohonan-edit')->first();
$correctPermission = Permission::where('name', 'permohonan.edit')->first();

if (!$wrongPermission) {
    echo PHP_EOL . '❌ Wrong permission permohonan-edit not found in database' . PHP_EOL;
    exit;
}

if (!$correctPermission) {
    echo PHP_EOL . '❌ Correct permission permohonan.edit not found in database' . PHP_EOL;
    exit;
}

echo PHP_EOL . 'Replacing permission:' . PHP_EOL;
echo '- REMOVE: ' . $wrongPermission->name . ' (ID: ' . $wrongPermission->id . ')' . PHP_EOL;
echo '- ADD: ' . $correctPermission->name . ' (ID: ' . $correctPermission->id . ')' . PHP_EOL;

// Remove old permission and add new permission
$user->permissions()->detach($wrongPermission->id);
$user->permissions()->attach($correctPermission->id);

echo PHP_EOL . '✅ Permission updated successfully!' . PHP_EOL;

echo PHP_EOL . 'AFTER - Updated permissions:' . PHP_EOL;
$user->refresh(); // Refresh to get updated permissions
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL . '=== VERIFICATION ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan.edit"): ' . ($user->hasPermissionTo('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan-edit"): ' . ($user->hasPermissionTo('permohonan-edit') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL . '🎉 User test4 should now be able to access edit permohonan menu!' . PHP_EOL;
