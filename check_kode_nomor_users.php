<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "Checking kode nomor permissions...\n";

$usersWithPermission = User::whereHas('permissions', function($q) {
    $q->where('name', 'master-kode-nomor-view');
})->get();

echo "Users with master-kode-nomor-view permission: " . $usersWithPermission->count() . "\n";

if ($usersWithPermission->count() > 0) {
    foreach ($usersWithPermission as $user) {
        echo "- " . $user->username . "\n";
    }
} else {
    echo "No users found with this permission.\n";
}

echo "\nChecking if permission exists...\n";
$permission = Permission::where('name', 'master-kode-nomor-view')->first();
if ($permission) {
    echo "Permission exists: " . $permission->name . "\n";
} else {
    echo "Permission does not exist!\n";
}
