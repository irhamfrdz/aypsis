<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== TESTING PERMISSION MATRIX CONVERSION ===\n\n";

// Create a UserController instance
$userController = new UserController();

// Use reflection to access private methods
$reflection = new ReflectionClass($userController);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');

$convertToMatrixMethod->setAccessible(true);
$convertToIdsMethod->setAccessible(true);

// Test with a simple permission set
$testPermissions = [
    'master.karyawan.index',
    'dashboard-view',
    'master-pranota-tagihan-kontainer',
    'admin.debug.perms',
    'profile.show',
    'user-approval'
];

echo "Original permissions: " . json_encode($testPermissions, JSON_PRETTY_PRINT) . "\n\n";

// Step 1: Convert to matrix
$matrixResult = $convertToMatrixMethod->invoke($userController, $testPermissions);
echo "Matrix result: " . json_encode($matrixResult, JSON_PRETTY_PRINT) . "\n\n";

// Step 2: Convert back to IDs
$idsResult = $convertToIdsMethod->invoke($userController, [$matrixResult]);
echo "IDs result: " . json_encode($idsResult, JSON_PRETTY_PRINT) . "\n\n";

// Step 3: Verify round-trip conversion
$originalIds = [];
foreach ($testPermissions as $permName) {
    $perm = \App\Models\Permission::where('name', $permName)->first();
    if ($perm) {
        $originalIds[] = $perm->id;
    }
}

echo "Original IDs: " . json_encode($originalIds, JSON_PRETTY_PRINT) . "\n";
echo "Converted IDs: " . json_encode($idsResult, JSON_PRETTY_PRINT) . "\n";

$missing = array_diff($originalIds, $idsResult);
$extra = array_diff($idsResult, $originalIds);

echo "Missing IDs: " . json_encode(array_values($missing), JSON_PRETTY_PRINT) . "\n";
echo "Extra IDs: " . json_encode(array_values($extra), JSON_PRETTY_PRINT) . "\n";

if (empty($missing) && empty($extra)) {
    echo "\n✅ ROUND-TRIP CONVERSION SUCCESSFUL!\n";
} else {
    echo "\n❌ ROUND-TRIP CONVERSION FAILED!\n";
}
