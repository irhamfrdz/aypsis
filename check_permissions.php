<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\Permission;
use App\Models\User;

echo "=== Available Permissions ===\n";
$permissions = Permission::pluck('name')->toArray();
foreach($permissions as $permission) {
    echo "- " . $permission . "\n";
}

echo "\n=== Current User Permissions ===\n";
$user = User::find(1); // Assuming user ID 1 is admin
if($user) {
    echo "User: " . $user->name . "\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "Permissions: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";

    echo "\n=== Checking specific permissions ===\n";
    echo "master-pranota: " . ($user->can('master-pranota') ? 'YES' : 'NO') . "\n";
    echo "master-pranota-tagihan-kontainer: " . ($user->can('master-pranota-tagihan-kontainer') ? 'YES' : 'NO') . "\n";
}
