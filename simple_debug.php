<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;
use ReflectionClass;

// Test simple permission conversion
echo "=== SIMPLE TEST ===\n\n";

$userController = new UserController();
$reflection = new ReflectionClass($userController);

// Test with simple permission
$testPermission = ['pembayaran-pranota-tagihan-kontainer.index'];
echo "Test permission: " . implode(', ', $testPermission) . "\n";
echo "Type of first element: " . gettype($testPermission[0]) . "\n";
echo "Value: '" . $testPermission[0] . "'\n";
echo "Length: " . strlen($testPermission[0]) . "\n\n";

// Call method using reflection
try {
    $convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
    $convertToMatrixMethod->setAccessible(true);
    $matrixResult = $convertToMatrixMethod->invoke($userController, $testPermission); // Remove extra array wrapper
    echo "Matrix result:\n";
    print_r($matrixResult);
    echo "\n";
} catch (Exception $e) {
    echo "Error calling convertPermissionsToMatrix: " . $e->getMessage() . "\n";
}

// Test convert back to IDs
try {
    $convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
    $convertToIdsMethod->setAccessible(true);
    $idsResult = $convertToIdsMethod->invoke($userController, [$matrixResult ?? []]);
    echo "IDs result:\n";
    print_r($idsResult);
    echo "\n";
} catch (Exception $e) {
    echo "Error calling convertMatrixPermissionsToIds: " . $e->getMessage() . "\n";
}

echo "=== END SIMPLE TEST ===\n";
