<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;

echo "=== TESTING PRANOTA PERMISSION MAPPING ===\n\n";

$controller = new UserController();

// Test 1: Convert permissions to matrix format
echo "1. Testing convertPermissionsToMatrix:\n";
$testPermissions = [
    'pranota-kontainer-sewa-create',
    'pranota-tagihan-kontainer.create',
    'pranota-kontainer-sewa-view',
    'pranota-tagihan-kontainer.view',
    'pranota-tagihan-kontainer.approve'
];

$matrix = $controller->testConvertPermissionsToMatrix($testPermissions);

foreach ($testPermissions as $perm) {
    echo "   Input: $perm\n";
}

echo "\n   Matrix Output:\n";
foreach ($matrix as $module => $actions) {
    echo "   Module: $module\n";
    foreach ($actions as $action => $value) {
        echo "     - $action: " . ($value ? 'true' : 'false') . "\n";
    }
}

// Test 2: Convert matrix back to permission IDs
echo "\n2. Testing convertMatrixPermissionsToIds:\n";
$testMatrix = [
    'pranota-kontainer-sewa' => [
        'create' => true,
        'view' => true
    ],
    'pranota-tagihan-kontainer' => [
        'create' => true,
        'view' => true,
        'approve' => true
    ]
];

$permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrix);

echo "   Input Matrix:\n";
foreach ($testMatrix as $module => $actions) {
    echo "   Module: $module\n";
    foreach ($actions as $action => $value) {
        echo "     - $action: " . ($value ? 'true' : 'false') . "\n";
    }
}

echo "\n   Permission IDs Output:\n";
$permissions = DB::table('permissions')->whereIn('id', $permissionIds)->get();
foreach ($permissions as $perm) {
    echo "   ✓ ID {$perm->id}: {$perm->name}\n";
}

echo "\n3. Expected Permission Names:\n";
echo "   ✓ pranota-kontainer-sewa-create\n";
echo "   ✓ pranota-kontainer-sewa-view\n";
echo "   ✓ pranota-tagihan-kontainer.create\n";
echo "   ✓ pranota-tagihan-kontainer.view\n";
echo "   ✓ pranota-tagihan-kontainer.approve\n";

echo "\n=== TEST COMPLETED ===\n";