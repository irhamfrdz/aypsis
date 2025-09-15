<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing permission update process\n";
echo "=================================\n\n";

// Test data - simulate form submission
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate permission matrix data from form
$testMatrixData = [
    'tagihan-kontainer' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'approve' => '1',
        'print' => '1',
        'export' => '1'
    ],
    'master-pranota-tagihan-kontainer' => [
        'access' => '1'
    ]
];

echo "Test Matrix Data (simulating form submission):\n";
print_r($testMatrixData);
echo "\n";

// Test convertMatrixPermissionsToIds
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

$permissionIds = $method->invoke($controller, $testMatrixData);

echo "Converted Permission IDs:\n";
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "  - ID {$id}: {$perm->name}\n";
    } else {
        echo "  - ID {$id}: NOT FOUND\n";
    }
}
echo "\n";

// Check if user currently has these permissions
$currentPermissionIds = $user->permissions->pluck('id')->toArray();
echo "User current permission IDs: " . implode(', ', $currentPermissionIds) . "\n";
echo "New permission IDs to sync: " . implode(', ', $permissionIds) . "\n\n";

// Simulate the sync operation
$user->permissions()->sync($permissionIds);
echo "✅ Permissions synced successfully\n\n";

// Verify the sync worked
$user->refresh();
$newPermissionIds = $user->permissions->pluck('id')->toArray();
echo "User permissions after sync: " . implode(', ', $newPermissionIds) . "\n\n";

// Test convertPermissionsToMatrix with the new permissions
$method2 = $reflection->getMethod('convertPermissionsToMatrix');
$method2->setAccessible(true);

$newPermissionNames = $user->permissions->pluck('name')->toArray();
$matrixResult = $method2->invoke($controller, $newPermissionNames);

echo "Matrix result after sync:\n";
if (isset($matrixResult['tagihan-kontainer'])) {
    echo "✓ tagihan-kontainer found:\n";
    print_r($matrixResult['tagihan-kontainer']);
} else {
    echo "✗ tagihan-kontainer NOT found in matrix\n";
}

if (isset($matrixResult['master-pranota-tagihan-kontainer'])) {
    echo "✓ master-pranota-tagihan-kontainer found:\n";
    print_r($matrixResult['master-pranota-tagihan-kontainer']);
} else {
    echo "✗ master-pranota-tagihan-kontainer NOT found in matrix\n";
}

echo "\nTest completed!\n";
