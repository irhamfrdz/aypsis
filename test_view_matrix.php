<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing permission matrix display in edit view\n";
echo "===============================================\n\n";

// Test data - user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Get current permissions
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($currentPermissions as $perm) {
    echo "  - $perm\n";
}
echo "\n";

// Test convertPermissionsToMatrix
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$matrixResult = $method->invoke($controller, $currentPermissions);

echo "Matrix result (what gets sent to view):\n";
foreach ($matrixResult as $module => $actions) {
    echo "Module: $module\n";
    if (is_array($actions)) {
        foreach ($actions as $action => $value) {
            echo "  - $action: " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "  - Value: $actions\n";
    }
    echo "\n";
}

// Check specific permissions we're interested in
echo "Checking specific permissions:\n";

if (isset($matrixResult['tagihan-kontainer'])) {
    echo "✓ tagihan-kontainer found in matrix:\n";
    print_r($matrixResult['tagihan-kontainer']);
} else {
    echo "✗ tagihan-kontainer NOT found in matrix\n";
}

if (isset($matrixResult['master-pranota-tagihan-kontainer'])) {
    echo "✓ master-pranota-tagihan-kontainer found in matrix:\n";
    print_r($matrixResult['master-pranota-tagihan-kontainer']);
} else {
    echo "✗ master-pranota-tagihan-kontainer NOT found in matrix\n";
}

echo "\n";

// Test what the view would see
echo "What the view would see for tagihan-kontainer checkboxes:\n";
if (isset($matrixResult['tagihan-kontainer'])) {
    $actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];
    foreach ($actions as $action) {
        $checked = isset($matrixResult['tagihan-kontainer'][$action]) && $matrixResult['tagihan-kontainer'][$action];
        echo "  permissions[tagihan-kontainer][$action]: " . ($checked ? 'CHECKED' : 'unchecked') . "\n";
    }
}

echo "\nWhat the view would see for master-pranota-tagihan-kontainer checkbox:\n";
if (isset($matrixResult['master-pranota-tagihan-kontainer'])) {
    $checked = isset($matrixResult['master-pranota-tagihan-kontainer']['access']) && $matrixResult['master-pranota-tagihan-kontainer']['access'];
    echo "  permissions[master-pranota-tagihan-kontainer][access]: " . ($checked ? 'CHECKED' : 'unchecked') . "\n";
} else {
    echo "  permissions[master-pranota-tagihan-kontainer][access]: NOT FOUND\n";
}

echo "\nTest completed!\n";
