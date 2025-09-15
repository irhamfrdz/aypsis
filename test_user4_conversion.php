<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing permission conversion for user test4\n";
echo "===========================================\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "User test4 not found!\n";
    exit;
}

echo "User test4 found (ID: {$user->id})\n\n";

// Get user permissions
$userPermissions = $user->permissions()->select('id', 'name')->get();
$permissionNames = $userPermissions->pluck('name')->toArray();

echo "User permissions:\n";
foreach ($permissionNames as $name) {
    echo "  $name\n";
}

echo "\n";

// Test convertPermissionsToMatrix
$userController = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$matrixResult = $method->invoke($userController, $permissionNames);

echo "Matrix conversion result:\n";
print_r($matrixResult);

echo "\n";

// Check if tagihan-kontainer is in the matrix
if (isset($matrixResult['tagihan-kontainer'])) {
    echo "✓ tagihan-kontainer found in matrix:\n";
    print_r($matrixResult['tagihan-kontainer']);
} else {
    echo "✗ tagihan-kontainer NOT found in matrix\n";
}

// Check if master-pranota-tagihan-kontainer would be in matrix
if (isset($matrixResult['master-pranota-tagihan-kontainer'])) {
    echo "✓ master-pranota-tagihan-kontainer found in matrix:\n";
    print_r($matrixResult['master-pranota-tagihan-kontainer']);
} else {
    echo "✗ master-pranota-tagihan-kontainer NOT found in matrix\n";
}
