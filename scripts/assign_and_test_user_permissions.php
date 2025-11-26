<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$controller = app(App\Http\Controllers\UserController::class);

$username = 'anggi';
$user = User::where('username', $username)->first();
if (!$user) {
    echo "User $username not found.\n";
    exit(1);
}

// Remove any existing kelola-bbm permissions
$user->permissions()->detach(App\Models\Permission::where('name','like','master-kelola-bbm%')->pluck('id')->toArray());

$matrix = ['master-kelola-bbm' => ['view' => 1],
           'master-pricelist-uang-jalan-batam' => ['view' => 1]];
$permissionIds = $controller->testConvertMatrixPermissionsToIds($matrix);

$user->permissions()->syncWithoutDetaching($permissionIds);

$perms = $user->permissions()->where('name','like','master-kelola-bbm%')->orWhere('name','like','master-pricelist-uang-jalan-batam%')->pluck('name')->toArray();
print_r($perms);

// Convert user permission names to matrix for verification
$controller = app(App\Http\Controllers\UserController::class);
$matrix = $controller->testConvertPermissionsToMatrix($perms);
print_r($matrix);

echo "Done\n";