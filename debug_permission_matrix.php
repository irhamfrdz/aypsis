<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Get user admin
$user = User::find(1);

if (!$user) {
    echo '❌ User admin not found' . PHP_EOL;
    exit;
}

echo '👤 User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

// Get user permissions
$userPermissions = $user->permissions->pluck('name')->toArray();

echo '🔍 User has these permissions containing "pranota-supir":' . PHP_EOL;
$pranotaPerms = array_filter($userPermissions, function($perm) {
    return strpos($perm, 'pranota-supir') !== false;
});

if (empty($pranotaPerms)) {
    echo '❌ No permissions found containing "pranota-supir"' . PHP_EOL;
} else {
    foreach ($pranotaPerms as $perm) {
        echo '✅ ' . $perm . PHP_EOL;
    }
}

echo PHP_EOL;
echo '🔍 User has these permissions containing "pembayaran-pranota-supir":' . PHP_EOL;
$pembayaranPerms = array_filter($userPermissions, function($perm) {
    return strpos($perm, 'pembayaran-pranota-supir') !== false;
});

if (empty($pembayaranPerms)) {
    echo '❌ No permissions found containing "pembayaran-pranota-supir"' . PHP_EOL;
} else {
    foreach ($pembayaranPerms as $perm) {
        echo '✅ ' . $perm . PHP_EOL;
    }
}

echo PHP_EOL;
echo '🔍 Expected permission matrix format:' . PHP_EOL;
echo 'For pranota-supir module:' . PHP_EOL;
echo '  - pranota-supir-view should map to: $userMatrixPermissions["pranota-supir"]["view"] = true' . PHP_EOL;
echo '  - pranota-supir-create should map to: $userMatrixPermissions["pranota-supir"]["create"] = true' . PHP_EOL;
echo '  - etc.' . PHP_EOL;

echo PHP_EOL;
echo 'For pembayaran-pranota-supir module:' . PHP_EOL;
echo '  - pembayaran-pranota-supir-view should map to: $userMatrixPermissions["pembayaran-pranota-supir"]["view"] = true' . PHP_EOL;
echo '  - pembayaran-pranota-supir-create should map to: $userMatrixPermissions["pembayaran-pranota-supir"]["create"] = true' . PHP_EOL;
echo '  - etc.' . PHP_EOL;

echo PHP_EOL;
echo '💡 SOLUTION:' . PHP_EOL;
echo 'The issue is likely in the convertPermissionsToMatrix method in UserController.' . PHP_EOL;
echo 'It needs to properly handle permissions with dash format (pranota-supir-view)' . PHP_EOL;
echo 'and convert them to matrix format.' . PHP_EOL;
