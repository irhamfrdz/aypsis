<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Http\Controllers\UserController;
use ReflectionClass;

$user = User::with('permissions')->find(1);
if ($user) {
    $permissions = $user->permissions->pluck('name')->toArray();
    echo 'User permissions: ' . implode(', ', $permissions) . PHP_EOL;

    $controller = new UserController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertPermissionsToMatrix');
    $method->setAccessible(true);
    $matrix = $method->invoke($controller, $permissions);
    echo 'Matrix result: ' . json_encode($matrix, JSON_PRETTY_PRINT) . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
