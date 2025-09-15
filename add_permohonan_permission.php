<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::where('username', 'test4')->first();
if ($user) {
    echo 'Found user test4' . PHP_EOL;

    // Check if permohonan permission exists
    $perm = Permission::where('name', 'permohonan')->first();
    if (!$perm) {
        echo 'Creating permohonan permission...' . PHP_EOL;
        $perm = Permission::create(['name' => 'permohonan']);
        echo '✅ Permission created' . PHP_EOL;
    } else {
        echo 'Permission permohonan already exists' . PHP_EOL;
    }

    // Attach permission to user if not already attached
    if (!$user->permissions->contains('name', 'permohonan')) {
        echo 'Attaching permohonan permission to user...' . PHP_EOL;
        $user->permissions()->attach($perm->id);
        echo '✅ Permission attached successfully' . PHP_EOL;
    } else {
        echo 'User already has permohonan permission' . PHP_EOL;
    }

    // Test permission
    echo PHP_EOL;
    echo '=== PERMISSION TEST ===' . PHP_EOL;
    echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
    echo 'can("permohonan"): ' . ($user->can('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;

} else {
    echo 'User test4 not found' . PHP_EOL;
}
