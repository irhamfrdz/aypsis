<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;

echo "=== TESTING PERMISSION SAVE ACCURACY ===\n\n";

// Test case: Simulate form submission with specific permissions checked
$testMatrix = [
    'dashboard' => ['view' => '1'],
    'master-karyawan' => ['view' => '1', 'create' => '1'],
    'tagihan-kontainer' => ['view' => '1', 'create' => '1', 'update' => '1']
];

$userController = new UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

$permissionIds = $method->invoke($userController, $testMatrix);

echo "Input matrix:\n";
print_r($testMatrix);
echo "\n";

echo "Permissions found (" . count($permissionIds) . "):\n";
foreach ($permissionIds as $id) {
    $perm = \App\Models\Permission::find($id);
    if ($perm) {
        echo "  ✓ {$perm->name}\n";
    }
}

echo "\nExpected: 5 permissions (dashboard + 2 karyawan + 3 tagihan)\n";
echo "Actual: " . count($permissionIds) . " permissions\n";

if (count($permissionIds) === 5) {
    echo "✅ Count matches expected!\n";
} else {
    echo "❌ Count mismatch\n";
}
