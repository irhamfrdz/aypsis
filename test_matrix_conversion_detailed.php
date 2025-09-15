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

// Get UserController instance and use reflection to access private method
$controller = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);

// Get user permissions
$userPermissions = $user->permissions->pluck('name')->toArray();

// Convert to matrix format
$userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $userPermissions);

echo '🔍 User Matrix Permissions for pranota-supir:' . PHP_EOL;
if (isset($userMatrixPermissions['pranota-supir'])) {
    echo '✅ Module found!' . PHP_EOL;
    echo json_encode($userMatrixPermissions['pranota-supir'], JSON_PRETTY_PRINT) . PHP_EOL;

    $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
    foreach ($actions as $action) {
        $hasAction = isset($userMatrixPermissions['pranota-supir'][$action]) && $userMatrixPermissions['pranota-supir'][$action];
        echo '  ' . ($hasAction ? '✅' : '❌') . ' ' . $action . PHP_EOL;
    }
} else {
    echo '❌ Module NOT found in matrix' . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 User Matrix Permissions for pembayaran-pranota-supir:' . PHP_EOL;
if (isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
    echo '✅ Module found!' . PHP_EOL;
    echo json_encode($userMatrixPermissions['pembayaran-pranota-supir'], JSON_PRETTY_PRINT) . PHP_EOL;

    $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
    foreach ($actions as $action) {
        $hasAction = isset($userMatrixPermissions['pembayaran-pranota-supir'][$action]) && $userMatrixPermissions['pembayaran-pranota-supir'][$action];
        echo '  ' . ($hasAction ? '✅' : '❌') . ' ' . $action . PHP_EOL;
    }
} else {
    echo '❌ Module NOT found in matrix' . PHP_EOL;
}

echo PHP_EOL;
echo '🔍 Testing specific permission conversion:' . PHP_EOL;

// Test permission: pranota-supir-view
$testPermissions1 = ['pranota-supir-view'];
$testMatrix1 = $convertToMatrixMethod->invoke($controller, $testPermissions1);
echo 'Input: pranota-supir-view' . PHP_EOL;
echo 'Output: ' . json_encode($testMatrix1, JSON_PRETTY_PRINT) . PHP_EOL;

echo PHP_EOL;

// Test permission: pembayaran-pranota-supir-view
$testPermissions2 = ['pembayaran-pranota-supir-view'];
$testMatrix2 = $convertToMatrixMethod->invoke($controller, $testPermissions2);
echo 'Input: pembayaran-pranota-supir-view' . PHP_EOL;
echo 'Output: ' . json_encode($testMatrix2, JSON_PRETTY_PRINT) . PHP_EOL;

echo PHP_EOL;
echo '💡 CONCLUSION:' . PHP_EOL;
if (isset($userMatrixPermissions['pranota-supir']) && isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
    echo '✅ Matrix conversion is working correctly!' . PHP_EOL;
    echo 'The checkboxes should be checked in the view.' . PHP_EOL;
} else {
    echo '❌ Matrix conversion is NOT working correctly.' . PHP_EOL;
    echo 'The issue is in the convertPermissionsToMatrix method.' . PHP_EOL;
}
