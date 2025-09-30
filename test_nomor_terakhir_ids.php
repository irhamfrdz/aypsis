<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\Permission;

$controller = new UserController();

// Test matrix permissions
$testMatrix = [
    'master-nomor-terakhir' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

echo "Testing convertMatrixPermissionsToIds for master-nomor-terakhir:\n";
echo "Input matrix:\n";
print_r($testMatrix);
echo "\n";

$permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrix);

echo "Output permission IDs: " . implode(', ', $permissionIds) . "\n\n";

echo "Checking corresponding permission names:\n";
$permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo "Permission names: " . implode(', ', $permissions) . "\n";

$expectedNames = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create', 'master-nomor-terakhir-update', 'master-nomor-terakhir-delete'];
$missing = array_diff($expectedNames, $permissions);
$extra = array_diff($permissions, $expectedNames);

if (empty($missing) && empty($extra)) {
    echo "✅ All expected permissions found, no extras\n";
} else {
    echo "❌ Missing: " . implode(', ', $missing) . "\n";
    echo "❌ Extra: " . implode(', ', $extra) . "\n";
}