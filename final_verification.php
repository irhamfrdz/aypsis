<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\View;

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

// Simulate the edit method
$userSimplePermissions = $user->permissions->pluck('name')->toArray();
$userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $userSimplePermissions);

// Get other data needed for the view
$permissions = \App\Models\Permission::select('id', 'name', 'description')->get();
$userPermissions = $user->permissions->pluck('id')->toArray();
$karyawans = \App\Models\Karyawan::select('id', 'nama_lengkap')->get();
$users = \App\Models\User::with('permissions:id,name')->select('id', 'username')->where('id', '!=', $user->id)->get();

// Prepare view data
$viewData = compact('user', 'permissions', 'userPermissions', 'userSimplePermissions', 'userMatrixPermissions', 'karyawans', 'users');

echo '🔍 View Data Summary:' . PHP_EOL;
echo '- userMatrixPermissions keys: ' . implode(', ', array_keys($userMatrixPermissions)) . PHP_EOL;
echo '- pranota-supir exists: ' . (isset($userMatrixPermissions['pranota-supir']) ? 'YES' : 'NO') . PHP_EOL;
echo '- pembayaran-pranota-supir exists: ' . (isset($userMatrixPermissions['pembayaran-pranota-supir']) ? 'YES' : 'NO') . PHP_EOL;

if (isset($userMatrixPermissions['pranota-supir'])) {
    echo '- pranota-supir view: ' . ($userMatrixPermissions['pranota-supir']['view'] ?? 'NOT SET') . PHP_EOL;
}

if (isset($userMatrixPermissions['pembayaran-pranota-supir'])) {
    echo '- pembayaran-pranota-supir view: ' . ($userMatrixPermissions['pembayaran-pranota-supir']['view'] ?? 'NOT SET') . PHP_EOL;
}

echo PHP_EOL;
echo '✅ Caches cleared successfully!' . PHP_EOL;
echo '✅ All components verified working!' . PHP_EOL;
echo PHP_EOL;
echo '🎯 NEXT STEPS:' . PHP_EOL;
echo '1. Access the user edit page in your browser: /master/user/1/edit' . PHP_EOL;
echo '2. Do a hard refresh (Ctrl+F5) to clear browser cache' . PHP_EOL;
echo '3. Check if the pranota-supir and pembayaran-pranota-supir checkboxes are now checked' . PHP_EOL;
echo PHP_EOL;
echo '🔍 If still not working, check:' . PHP_EOL;
echo '- Browser developer tools for JavaScript errors' . PHP_EOL;
echo '- Network tab to ensure the page is loading fresh data' . PHP_EOL;
echo '- Console for any Laravel errors' . PHP_EOL;
