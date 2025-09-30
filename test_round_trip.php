<?php
require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

// Test the full round trip: matrix -> permissions -> matrix
$permissionsMatrix = [
    'user-approval' => [
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

echo '=== TESTING USER-APPROVAL PERMISSION ROUND TRIP ===' . PHP_EOL;
echo 'Original matrix: ' . json_encode($permissionsMatrix) . PHP_EOL;

// Use reflection to access private methods
$controller = new UserController();
$reflection = new ReflectionClass($controller);

// Step 1: Convert matrix to permission IDs
$convertMatrixMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
$convertMatrixMethod->setAccessible(true);
$permissionIds = $convertMatrixMethod->invoke($controller, $permissionsMatrix);

echo 'Permission IDs: ' . json_encode($permissionIds) . PHP_EOL;

// Step 2: Get permission names from IDs
$permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo 'Permission names: ' . json_encode($permissions) . PHP_EOL;

// Step 3: Convert back to matrix
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);
$userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $permissions);
echo 'Converted back to matrix: ' . json_encode($userMatrixPermissions) . PHP_EOL;

// Check if round trip worked
$originalCreate = $permissionsMatrix['user-approval']['create'] ?? false;
$originalUpdate = $permissionsMatrix['user-approval']['update'] ?? false;
$originalDelete = $permissionsMatrix['user-approval']['delete'] ?? false;

$roundTripCreate = $userMatrixPermissions['user-approval']['create'] ?? false;
$roundTripUpdate = $userMatrixPermissions['user-approval']['update'] ?? false;
$roundTripDelete = $userMatrixPermissions['user-approval']['delete'] ?? false;

echo PHP_EOL . '=== ROUND TRIP VERIFICATION ===' . PHP_EOL;
echo "Create: Original=$originalCreate, RoundTrip=$roundTripCreate - " . ($originalCreate == $roundTripCreate ? '✅ PASS' : '❌ FAIL') . PHP_EOL;
echo "Update: Original=$originalUpdate, RoundTrip=$roundTripUpdate - " . ($originalUpdate == $roundTripUpdate ? '✅ PASS' : '❌ FAIL') . PHP_EOL;
echo "Delete: Original=$originalDelete, RoundTrip=$roundTripDelete - " . ($originalDelete == $roundTripDelete ? '✅ PASS' : '❌ FAIL') . PHP_EOL;

$allPass = ($originalCreate == $roundTripCreate) && ($originalUpdate == $roundTripUpdate) && ($originalDelete == $roundTripDelete);
echo PHP_EOL . 'Overall result: ' . ($allPass ? '✅ ALL TESTS PASSED' : '❌ SOME TESTS FAILED') . PHP_EOL;
?>
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
