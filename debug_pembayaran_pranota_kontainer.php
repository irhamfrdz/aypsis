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

// Test pembayaran pranota kontainer permissions
echo "=== TESTING PEMBAYARAN PRANOTA KONTAINER PERMISSIONS ===\n\n";

// Get all pembayaran pranota tagihan kontainer permissions
$pembayaranPermissions = Permission::where('name', 'like', 'pembayaran-pranota-tagihan-kontainer.%')
    ->pluck('name')
    ->toArray();

echo "Found pembayaran pranota tagihan kontainer permissions:\n";
foreach ($pembayaranPermissions as $perm) {
    echo "- $perm\n";
}
echo "\n";

// Test conversion methods
$userController = new UserController();
$reflection = new ReflectionClass($userController);

// Test convertPermissionsToMatrix
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);
$matrixResult = $convertToMatrixMethod->invoke($userController, $pembayaranPermissions); // Remove extra array wrapper

echo "Debug: Input permissions to convertPermissionsToMatrix:\n";
foreach ($pembayaranPermissions as $perm) {
    echo "- $perm\n";
    $parts = explode('.', $perm);
    echo "  Parts: " . implode(' | ', $parts) . "\n";
    echo "  Count: " . count($parts) . "\n";
    if (count($parts) >= 5) {
        echo "  Check: parts[0]='pembayaran', parts[1]='pranota', parts[2]='tagihan', parts[3]='kontainer', parts[4]='" . $parts[4] . "'\n";
    }
}
echo "\n";

echo "Matrix conversion result:\n";
print_r($matrixResult);
echo "\n";

// Test convertMatrixPermissionsToIds
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
$convertToIdsMethod->setAccessible(true);
$idsResult = $convertToIdsMethod->invoke($userController, $matrixResult); // Remove extra array wrapper

echo "IDs conversion result:\n";
print_r($idsResult);
echo "\n";

// Check if round-trip conversion works
$convertedPermissions = Permission::whereIn('id', $idsResult)->pluck('name')->toArray();
echo "Round-trip converted permissions:\n";
foreach ($convertedPermissions as $perm) {
    echo "- $perm\n";
}
echo "\n";

// Check for missing permissions
$missingPermissions = array_diff($pembayaranPermissions, $convertedPermissions);
if (!empty($missingPermissions)) {
    echo "❌ MISSING PERMISSIONS IN ROUND-TRIP:\n";
    foreach ($missingPermissions as $missing) {
        echo "- $missing\n";
    }
} else {
    echo "✅ All permissions preserved in round-trip conversion\n";
}

echo "\n=== END TEST ===\n";
