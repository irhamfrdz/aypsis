<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo 'User test4 not found' . PHP_EOL;
    exit(1);
}

echo '=== PERMISSION CHECK FOR USER test4 ===' . PHP_EOL;
echo 'User permissions: ' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo 'Permission checks:' . PHP_EOL;
echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionLike("permohonan"): ' . ($user->hasPermissionLike('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'can("permohonan"): ' . ($user->can('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo 'Specific permohonan permissions:' . PHP_EOL;
echo 'hasPermissionTo("permohonan.view"): ' . ($user->hasPermissionTo('permohonan.view') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
