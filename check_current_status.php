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
echo '🔐 Total permissions: ' . $user->permissions->count() . PHP_EOL;
echo PHP_EOL;

// Check specific pranota permissions
$pranotaPermissions = $user->permissions->pluck('name')->filter(function($perm) {
    return strpos($perm, 'pranota-supir') !== false || strpos($perm, 'pembayaran-pranota-supir') !== false;
});

echo '🔍 Pranota-related permissions:' . PHP_EOL;
foreach ($pranotaPermissions as $perm) {
    echo '  ✅ ' . $perm . PHP_EOL;
}

echo PHP_EOL;

// Test the matrix conversion
$controller = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);

$userSimplePermissions = $user->permissions->pluck('name')->toArray();
$userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $userSimplePermissions);

echo '🔍 Matrix conversion results:' . PHP_EOL;
echo '  pranota-supir exists: ' . (isset($userMatrixPermissions['pranota-supir']) ? '✅ YES' : '❌ NO') . PHP_EOL;
echo '  pembayaran-pranota-supir exists: ' . (isset($userMatrixPermissions['pembayaran-pranota-supir']) ? '✅ YES' : '❌ NO') . PHP_EOL;

if (isset($userMatrixPermissions['pranota-supir'])) {
    echo '  pranota-supir view: ' . ($userMatrixPermissions['pranota-supir']['view'] ?? 'NOT SET') . PHP_EOL;
}

if (isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
    echo '  pembayaran-pranota-supir view: ' . ($userMatrixPermissions['pembayaran-pranota-supir']['view'] ?? 'NOT SET') . PHP_EOL;
}

echo PHP_EOL;
echo '💡 CONCLUSION:' . PHP_EOL;
if (isset($userMatrixPermissions['pranota-supir']) && isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
    echo '✅ Permissions are properly configured and should be checked in the UI' . PHP_EOL;
    echo 'If checkboxes are not checked, try:' . PHP_EOL;
    echo '1. Hard refresh browser (Ctrl+F5)' . PHP_EOL;
    echo '2. Clear browser cache' . PHP_EOL;
    echo '3. Check if you are editing the correct user' . PHP_EOL;
} else {
    echo '❌ Permission matrix conversion is not working properly' . PHP_EOL;
}
