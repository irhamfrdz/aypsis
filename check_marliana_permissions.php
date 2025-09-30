<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::where('username', 'marliana')->first();
if (!$user) {
    echo 'User marliana not found' . PHP_EOL;
    exit;
}

$permissions = $user->permissions->pluck('name')->toArray();
echo 'User marliana permissions:' . PHP_EOL;
foreach ($permissions as $perm) {
    echo '- ' . $perm . PHP_EOL;
}

echo PHP_EOL . 'Checking specific permissions:' . PHP_EOL;
echo 'tagihan-perbaikan-kontainer-view: ' . ($user->can('tagihan-perbaikan-kontainer-view') ? 'YES' : 'NO') . PHP_EOL;
echo 'pranota-perbaikan-kontainer-create: ' . ($user->can('pranota-perbaikan-kontainer-create') ? 'YES' : 'NO') . PHP_EOL;
