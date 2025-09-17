<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== Checking Divisi Permissions ===\n\n";

// Check what divisi permissions exist
$divisiPerms = Permission::where('name', 'like', '%divisi%')->get();
echo "Divisi permissions in database:\n";
foreach($divisiPerms as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\n=== Checking Admin User Permissions ===\n";

// Check admin user permissions
$user = User::where('username', 'admin')->first();
if ($user) {
    echo "Admin user permissions for divisi:\n";
    $userDivisiPerms = $user->permissions()->where('name', 'like', '%divisi%')->get();
    foreach($userDivisiPerms as $perm) {
        echo "- {$perm->name}\n";
    }

    // Check specific permissions
    echo "\nSpecific permission checks:\n";
    echo "master-divisi.view: " . ($user->hasPermissionTo('master-divisi.view') ? 'YES' : 'NO') . "\n";
    echo "master-divisi.create: " . ($user->hasPermissionTo('master-divisi.create') ? 'YES' : 'NO') . "\n";
    echo "master-divisi.update: " . ($user->hasPermissionTo('master-divisi.update') ? 'YES' : 'NO') . "\n";
    echo "master-divisi.delete: " . ($user->hasPermissionTo('master-divisi.delete') ? 'YES' : 'NO') . "\n";
} else {
    echo "Admin user not found!\n";
}

echo "\n=== Checking Routes Middleware ===\n";
echo "Route middleware expects: master-divisi.view\n";
echo "But our script gave: master-divisi.view (with dots)\n";
