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

// Create UserController instance
$controller = new UserController();

// Use reflection to access private method
$reflection = new ReflectionClass($controller);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);

// Call the edit method logic
try {
    $userSimplePermissions = $user->permissions->pluck('name')->toArray();
    $userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $userSimplePermissions);

    echo '🔍 Data that would be sent to view:' . PHP_EOL;
    echo 'userSimplePermissions count: ' . count($userSimplePermissions) . PHP_EOL;
    echo 'userMatrixPermissions keys: ' . implode(', ', array_keys($userMatrixPermissions)) . PHP_EOL;
    echo PHP_EOL;

    echo '🔍 Checking pranota-supir in userMatrixPermissions:' . PHP_EOL;
    if (isset($userMatrixPermissions['pranota-supir'])) {
        echo '✅ pranota-supir found in matrix' . PHP_EOL;
        echo 'View condition: isset($userMatrixPermissions[\'pranota-supir\'][\'view\']) && $userMatrixPermissions[\'pranota-supir\'][\'view\']' . PHP_EOL;
        echo 'Result: ' . (isset($userMatrixPermissions['pranota-supir']['view']) && $userMatrixPermissions['pranota-supir']['view'] ? 'TRUE (checkbox should be checked)' : 'FALSE (checkbox should NOT be checked)') . PHP_EOL;
    } else {
        echo '❌ pranota-supir NOT found in matrix' . PHP_EOL;
    }

    echo PHP_EOL;
    echo '🔍 Checking pembayaran-pranota-supir in userMatrixPermissions:' . PHP_EOL;
    if (isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
        echo '✅ pembayaran-pranota-supir found in matrix' . PHP_EOL;
        echo 'View condition: isset($userMatrixPermissions[\'pembayaran-pranota-supir\'][\'view\']) && $userMatrixPermissions[\'pembayaran-pranota-supir\'][\'view\']' . PHP_EOL;
        echo 'Result: ' . (isset($userMatrixPermissions['pembayaran-pranota-supir']['view']) && $userMatrixPermissions['pembayaran-pranota-supir']['view'] ? 'TRUE (checkbox should be checked)' : 'FALSE (checkbox should NOT be checked)') . PHP_EOL;
    } else {
        echo '❌ pembayaran-pranota-supir NOT found in matrix' . PHP_EOL;
    }

    echo PHP_EOL;
    echo '🔍 Sample permissions from user:' . PHP_EOL;
    $pranotaPermissions = array_filter($userSimplePermissions, function($perm) {
        return strpos($perm, 'pranota-supir') !== false || strpos($perm, 'pembayaran-pranota-supir') !== false;
    });
    echo 'Pranota-related permissions: ' . implode(', ', $pranotaPermissions) . PHP_EOL;

    echo PHP_EOL;
    echo '💡 CONCLUSION:' . PHP_EOL;
    $pranotaChecked = isset($userMatrixPermissions['pranota-supir']['view']) && $userMatrixPermissions['pranota-supir']['view'];
    $pembayaranChecked = isset($userMatrixPermissions['pembayaran-pranota-supir']['view']) && $userMatrixPermissions['pembayaran-pranota-supir']['view'];

    if ($pranotaChecked && $pembayaranChecked) {
        echo '✅ Both checkboxes SHOULD be checked in the view!' . PHP_EOL;
        echo 'If they are not checked, there might be a caching issue or the view is not using the correct variable.' . PHP_EOL;
    } else {
        echo '❌ One or both checkboxes should NOT be checked.' . PHP_EOL;
        echo 'This indicates a problem with the permission matrix conversion.' . PHP_EOL;
    }

} catch (Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . PHP_EOL;
}
