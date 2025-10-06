<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "User admin not found!\n";
    exit(1);
}

echo "User admin permissions for pranota-kontainer-sewa:\n";
$perms = $user->permissions()->where('name', 'LIKE', 'pranota-kontainer-sewa-%')->pluck('name');
foreach ($perms as $perm) {
    echo "- {$perm}\n";
}

echo "\nSpecific checks:\n";
echo "Has pranota-kontainer-sewa-edit: " . ($user->hasPermissionTo('pranota-kontainer-sewa-edit') ? 'YES' : 'NO') . "\n";
echo "Has pranota-kontainer-sewa-update: " . ($user->hasPermissionTo('pranota-kontainer-sewa-update') ? 'YES' : 'NO') . "\n";