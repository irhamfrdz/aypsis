<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

// Get user
$user = User::find(1);

echo '🔍 Current permission names in database:' . PHP_EOL;
$pranotaPerms = Permission::where('name', 'like', 'pranota-supir%')->get();
foreach ($pranotaPerms as $perm) {
    echo '  - ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Current permission names for pembayaran:' . PHP_EOL;
$pembayaranPerms = Permission::where('name', 'like', 'pembayaran-pranota-supir%')->get();
foreach ($pembayaranPerms as $perm) {
    echo '  - ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Checking hasPermissionLike logic:' . PHP_EOL;
echo 'User permissions that start with "pranota-supir":' . PHP_EOL;
foreach ($user->permissions as $perm) {
    if (strpos($perm->name, 'pranota-supir') === 0) {
        echo '  ✅ ' . $perm->name . PHP_EOL;
    }
}

echo PHP_EOL;
echo 'User permissions that start with "pembayaran-pranota-supir":' . PHP_EOL;
foreach ($user->permissions as $perm) {
    if (strpos($perm->name, 'pembayaran-pranota-supir') === 0) {
        echo '  ✅ ' . $perm->name . PHP_EOL;
    }
}
