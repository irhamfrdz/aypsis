<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Testing Permission Conversion Methods\n";
echo "=====================================\n\n";

// Test convertPermissionsToMatrix
echo "1. Testing convertPermissionsToMatrix:\n";

$testPermissions = [
    'dashboard-view',
    'master-karyawan',
    'master.karyawan.index',
    'master-user-view',
    'pembayaran.create',
    'laporan.export'
];

$userController = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$matrixResult = $method->invoke($userController, $testPermissions);

echo "Input permissions:\n";
print_r($testPermissions);
echo "\nMatrix result:\n";
print_r($matrixResult);
echo "\n";

// Test convertMatrixPermissionsToIds
echo "2. Testing convertMatrixPermissionsToIds:\n";

$testMatrix = [
    'dashboard' => ['view' => '1'],
    'master-karyawan' => ['view' => '1', 'create' => '1'],
    'master-user' => ['view' => '1', 'update' => '1'],
    'pembayaran' => ['create' => '1'],
    'laporan' => ['export' => '1']
];

$method2 = $reflection->getMethod('convertMatrixPermissionsToIds');
$method2->setAccessible(true);

$idsResult = $method2->invoke($userController, $testMatrix);

echo "Input matrix:\n";
print_r($testMatrix);
echo "\nPermission IDs found:\n";
print_r($idsResult);

// Show which permissions were found
if (!empty($idsResult)) {
    $foundPermissions = Permission::whereIn('id', $idsResult)->pluck('name', 'id');
    echo "\nFound permissions:\n";
    foreach ($foundPermissions as $id => $name) {
        echo "  ID $id: $name\n";
    }
} else {
    echo "\nNo permissions found!\n";
}

echo "\n";

// Test convertSimplePermissionsToIds
echo "3. Testing convertSimplePermissionsToIds:\n";

$method3 = $reflection->getMethod('convertSimplePermissionsToIds');
$method3->setAccessible(true);

$simpleIdsResult = $method3->invoke($userController, $testPermissions);

echo "Input simple permissions:\n";
print_r($testPermissions);
echo "\nSimple permission IDs found:\n";
print_r($simpleIdsResult);

// Show which permissions were found
if (!empty($simpleIdsResult)) {
    $foundSimplePermissions = Permission::whereIn('id', $simpleIdsResult)->pluck('name', 'id');
    echo "\nFound simple permissions:\n";
    foreach ($foundSimplePermissions as $id => $name) {
        echo "  ID $id: $name\n";
    }
} else {
    echo "\nNo simple permissions found!\n";
}

echo "\nTest completed!\n";
