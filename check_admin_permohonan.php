<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo 'User admin permissions:' . PHP_EOL;
    foreach ($user->permissions as $perm) {
        echo '- ' . $perm->name . PHP_EOL;
    }
    echo PHP_EOL;
    echo 'Has permohonan-memo-update: ' . ($user->hasPermissionTo('permohonan-memo-update') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has permohonan-edit: ' . ($user->hasPermissionTo('permohonan-edit') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User admin not found' . PHP_EOL;
}