<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing master-pranota-tagihan-kontainer permission handling\n";
echo "==========================================================\n\n";

// Test data
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->name} (ID: {$user->id})\n\n";

// Get current permissions
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($currentPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Test convertPermissionsToMatrix
echo "1. Testing convertPermissionsToMatrix with master-pranota-tagihan-kontainer:\n";

$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

// Test with master-pranota-tagihan-kontainer added
$testPermissions = array_merge($currentPermissions, ['master-pranota-tagihan-kontainer']);
$matrixResult = $method->invoke($controller, $testPermissions);

echo "Matrix result:\n";
if (isset($matrixResult['master-pranota-tagihan-kontainer'])) {
    echo "✓ master-pranota-tagihan-kontainer found in matrix:\n";
    print_r($matrixResult['master-pranota-tagihan-kontainer']);
} else {
    echo "✗ master-pranota-tagihan-kontainer NOT found in matrix\n";
    echo "Available modules in matrix:\n";
    foreach ($matrixResult as $module => $actions) {
        echo "  - $module\n";
    }
}
echo "\n";

// Test convertMatrixPermissionsToIds
echo "2. Testing convertMatrixPermissionsToIds:\n";

$method2 = $reflection->getMethod('convertMatrixPermissionsToIds');
$method2->setAccessible(true);

// Create test matrix with master-pranota-tagihan-kontainer
$testMatrix = [
    'master-pranota-tagihan-kontainer' => ['access' => '1']
];

$idsResult = $method2->invoke($controller, $testMatrix);

echo "Permission IDs found:\n";
foreach ($idsResult as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "  - ID $id: {$perm->name}\n";
    } else {
        echo "  - ID $id: NOT FOUND\n";
    }
}

$masterPranotaPermission = Permission::where('name', 'master-pranota-tagihan-kontainer')->first();
if ($masterPranotaPermission) {
    if (in_array($masterPranotaPermission->id, $idsResult)) {
        echo "✓ master-pranota-tagihan-kontainer (ID: {$masterPranotaPermission->id}) correctly converted\n";
    } else {
        echo "✗ master-pranota-tagihan-kontainer (ID: {$masterPranotaPermission->id}) NOT found in conversion result\n";
    }
} else {
    echo "✗ master-pranota-tagihan-kontainer permission not found in database\n";
}

echo "\nTest completed!\n";
