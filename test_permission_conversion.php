<?php
require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

// Test the permission names directly
$testPermissions = [
    'user-approval',
    'user-approval-create',
    'user-approval-update',
    'user-approval-delete'
];

echo 'Testing permission lookup for user-approval permissions...' . PHP_EOL;

foreach ($testPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "✓ Found: {$permName} (ID: {$permission->id})" . PHP_EOL;
    } else {
        echo "✗ Not found: {$permName}" . PHP_EOL;
    }
}

// Test the matrix conversion logic manually
echo PHP_EOL . 'Testing matrix conversion logic...' . PHP_EOL;

$permissionsMatrix = [
    'user-approval' => [
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

$permissionIds = [];
foreach ($permissionsMatrix as $module => $actions) {
    if ($module === 'user-approval') {
        $actionMap = [
            'view' => 'user-approval',
            'create' => 'user-approval-create',
            'update' => 'user-approval-update',
            'delete' => 'user-approval-delete',
            'print' => 'user-approval-print',
            'export' => 'user-approval-export'
        ];

        foreach ($actions as $action => $value) {
            if ($value === '1' && isset($actionMap[$action])) {
                $permissionName = $actionMap[$action];
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $permissionIds[] = $permission->id;
                    echo "✓ Mapped {$module}.{$action} -> {$permissionName} (ID: {$permission->id})" . PHP_EOL;
                } else {
                    echo "✗ Permission not found: {$permissionName}" . PHP_EOL;
                }
            }
        }
    }
}

echo PHP_EOL . 'Final permission IDs: ' . json_encode($permissionIds) . PHP_EOL;
?>

    'master-tipe-akun' => [

        'view' => '1',// Bootstrap Laravel

        'create' => '1',

        'update' => '1',echo "Testing convertMatrixPermissionsToIds for master-tipe-akun...\n";$app = require_once 'bootstrap/app.php';

        'delete' => '1'

    ]$kernel = $app->make(Kernel::class);

];

$controller = new UserController();$kernel->bootstrap();

try {

    $permissionIds = $controller->testConvertMatrixPermissionsToIds($testPermissions);



    echo "Permission IDs found: " . count($permissionIds) . "\n";// Test data - simulate form submission with master-tipe-akun permissions checkedecho "Testing Permission Conversion Methods\n";

    echo "IDs: " . implode(', ', $permissionIds) . "\n";

$testPermissions = [echo "=====================================\n\n";

    if (count($permissionIds) > 0) {

        echo "✅ SUCCESS: Permission conversion working correctly!\n";    'master-tipe-akun' => [

    } else {

        echo "❌ FAILED: No permission IDs found\n";        'view' => '1',// Test convertPermissionsToMatrix

    }

} catch (Exception $e) {        'create' => '1',echo "1. Testing convertPermissionsToMatrix:\n";

    echo "❌ ERROR: " . $e->getMessage() . "\n";

}        'update' => '1',



echo "Done!\n";        'delete' => '1'$testPermissions = [

    ]    'dashboard-view',

];    'master-karyawan',

    'master.karyawan.index',

try {    'master-user-view',

    $permissionIds = $controller->testConvertMatrixPermissionsToIds($testPermissions);    'pembayaran.create',

    'laporan.export'

    echo "Permission IDs found: " . count($permissionIds) . "\n";];

    echo "IDs: " . implode(', ', $permissionIds) . "\n";

$userController = new App\Http\Controllers\UserController();

    if (count($permissionIds) > 0) {$reflection = new ReflectionClass($userController);

        echo "✅ SUCCESS: Permission conversion working correctly!\n";$method = $reflection->getMethod('convertPermissionsToMatrix');

    } else {$method->setAccessible(true);

        echo "❌ FAILED: No permission IDs found\n";

    }$matrixResult = $method->invoke($userController, $testPermissions);

} catch (Exception $e) {

    echo "❌ ERROR: " . $e->getMessage() . "\n";echo "Input permissions:\n";

}print_r($testPermissions);

echo "\nMatrix result:\n";

echo "Done!\n";print_r($matrixResult);
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
