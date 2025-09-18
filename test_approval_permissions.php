<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;
use ReflectionClass;

$user = User::with('permissions')->find(10);
if ($user) {
    $permissions = $user->permissions->pluck('name')->toArray();
    echo 'User permissions: ' . implode(', ', $permissions) . PHP_EOL;

    $controller = new UserController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('convertPermissionsToMatrix');
    $method->setAccessible(true);
    $matrix = $method->invoke($controller, $permissions);
    echo 'Matrix result: ' . json_encode($matrix, JSON_PRETTY_PRINT) . PHP_EOL;

    // Test convertMatrixPermissionsToIds
    $method2 = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method2->setAccessible(true);
    $ids = $method2->invoke($controller, $matrix);
    echo 'Converted back to IDs: ' . json_encode($ids, JSON_PRETTY_PRINT) . PHP_EOL;

    // Check if approval permissions are in the result
    $approvalPerms = array_filter($permissions, function($perm) {
        return strpos($perm, 'approval-') === 0;
    });
    echo 'Original approval permissions: ' . implode(', ', $approvalPerms) . PHP_EOL;

} else {
    echo 'User not found' . PHP_EOL;
}
