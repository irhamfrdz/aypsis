<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo 'Has permohonan-memo-delete: ' . ($user->hasPermissionTo('permohonan-memo-delete') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has permohonan-memo-print: ' . ($user->hasPermissionTo('permohonan-memo-print') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has permohonan: ' . ($user->hasPermissionTo('permohonan') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User not found';
}