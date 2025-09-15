<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

// Get user admin
$user = User::find(1);

if (!$user) {
    echo '❌ User admin not found' . PHP_EOL;
    exit;
}

echo '👤 User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

// Permission yang perlu ditambahkan dengan format dash
$missingPermissions = [
    'pranota-supir-view',
    'pembayaran-pranota-supir-view',
    'pembayaran-pranota-supir-create',
    'pembayaran-pranota-supir-update',
    'pembayaran-pranota-supir-delete',
    'pembayaran-pranota-supir-approve',
    'pembayaran-pranota-supir-print',
    'pembayaran-pranota-supir-export'
];

echo '🔍 Checking and adding missing permissions:' . PHP_EOL;

$addedPermissions = [];
foreach ($missingPermissions as $permName) {
    // Check if permission exists in database
    $permission = Permission::where('name', $permName)->first();

    if (!$permission) {
        echo '  ❌ Permission not found in database: ' . $permName . PHP_EOL;
        continue;
    }

    // Check if user already has this permission
    $hasPermission = $user->permissions()->where('name', $permName)->exists();

    if (!$hasPermission) {
        // Add permission to user
        $user->permissions()->attach($permission->id);
        $addedPermissions[] = $permName;
        echo '  ✅ Added permission: ' . $permName . PHP_EOL;
    } else {
        echo '  ℹ️  User already has permission: ' . $permName . PHP_EOL;
    }
}

echo PHP_EOL;
echo '📊 Summary:' . PHP_EOL;
echo '  Added permissions: ' . count($addedPermissions) . PHP_EOL;

if (!empty($addedPermissions)) {
    echo '  ✅ Successfully added permissions: ' . implode(', ', $addedPermissions) . PHP_EOL;
} else {
    echo '  ℹ️  No permissions were added (user already has them or they don\'t exist in database)' . PHP_EOL;
}

echo PHP_EOL;
echo '🔄 Refreshing user permissions...' . PHP_EOL;
$user->refresh();

echo '📋 Current user permissions for pranota-supir:' . PHP_EOL;
$pranotaPerms = $user->permissions->filter(function($perm) {
    return strpos($perm->name, 'pranota-supir') === 0;
});
foreach ($pranotaPerms as $perm) {
    echo '  - ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '📋 Current user permissions for pembayaran-pranota-supir:' . PHP_EOL;
$pembayaranPerms = $user->permissions->filter(function($perm) {
    return strpos($perm->name, 'pembayaran-pranota-supir') === 0;
});
foreach ($pembayaranPerms as $perm) {
    echo '  - ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '✅ Permission fix completed!' . PHP_EOL;
