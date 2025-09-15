<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

// Get user admin
$user = User::find(1);

if (!$user) {
    echo '❌ User admin not found' . PHP_EOL;
    exit;
}

echo '👤 User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

// Get UserController instance
$controller = new UserController();

// Get user simple permissions
$userSimplePermissions = $user->permissions->pluck('name')->toArray();

echo '🔍 User Simple Permissions:' . PHP_EOL;
foreach ($userSimplePermissions as $perm) {
    echo '  - ' . $perm . PHP_EOL;
}
echo PHP_EOL;

// Convert to matrix format
$userMatrixPermissions = $controller->convertPermissionsToMatrix($userSimplePermissions);

echo '🔍 User Matrix Permissions:' . PHP_EOL;
echo json_encode($userMatrixPermissions, JSON_PRETTY_PRINT) . PHP_EOL;
echo PHP_EOL;

// Check specific modules
$modulesToCheck = ['pranota-supir', 'pembayaran-pranota-supir'];

foreach ($modulesToCheck as $module) {
    echo '🔍 Checking module: ' . $module . PHP_EOL;

    if (isset($userMatrixPermissions[$module])) {
        echo '  ✅ Module found in matrix' . PHP_EOL;
        echo '  📋 Actions: ' . json_encode($userMatrixPermissions[$module]) . PHP_EOL;

        $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
        foreach ($actions as $action) {
            $hasAction = isset($userMatrixPermissions[$module][$action]) && $userMatrixPermissions[$module][$action];
            echo '    ' . ($hasAction ? '✅' : '❌') . ' ' . $action . PHP_EOL;
        }
    } else {
        echo '  ❌ Module NOT found in matrix' . PHP_EOL;
    }

    echo PHP_EOL;
}

// Check what permissions user has that contain the module names
echo '🔍 Permissions containing module names:' . PHP_EOL;
foreach ($modulesToCheck as $module) {
    echo 'Module: ' . $module . PHP_EOL;
    $matchingPerms = array_filter($userSimplePermissions, function($perm) use ($module) {
        return strpos($perm, $module) !== false;
    });

    if (!empty($matchingPerms)) {
        foreach ($matchingPerms as $perm) {
            echo '  ✅ ' . $perm . PHP_EOL;
        }
    } else {
        echo '  ❌ No matching permissions found' . PHP_EOL;
    }
    echo PHP_EOL;
}
