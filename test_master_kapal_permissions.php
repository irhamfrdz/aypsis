<?php

echo "=== TESTING MASTER KAPAL PERMISSIONS ===\n\n";

// Test conversion from permission names to matrix
$userController = new App\Http\Controllers\UserController();

// Test permission names that should be handled by master-kapal handler
$testPermissions = [
    'master-kapal-view',
    'master-kapal-create', 
    'master-kapal-update',
    'master-kapal-delete',
    'master-kapal-store',
    'master-kapal-print',
    'master-kapal-export'
];

echo "1. Testing convertPermissionsToMatrix:\n";
echo "Input permissions: " . json_encode($testPermissions) . "\n\n";

$matrixResult = $userController->testConvertPermissionsToMatrix($testPermissions);
echo "Matrix result:\n";
print_r($matrixResult);

echo "\n2. Testing convertMatrixPermissionsToIds:\n";
if (isset($matrixResult['master-kapal'])) {
    echo "Matrix input: " . json_encode($matrixResult) . "\n\n";
    
    $idsResult = $userController->testConvertMatrixPermissionsToIds($matrixResult);
    echo "Permission IDs result: " . json_encode($idsResult) . "\n\n";
    
    // Get actual permission names from IDs
    $actualPermissions = App\Models\Permission::whereIn('id', $idsResult)->pluck('name')->toArray();
    echo "Actual permission names from IDs: " . json_encode($actualPermissions) . "\n\n";
    
    // Check if we got all the permissions we expected
    $expectedPermissions = ['master-kapal-view', 'master-kapal-create', 'master-kapal-update', 'master-kapal-delete', 'master-kapal-print', 'master-kapal-export'];
    $missing = array_diff($expectedPermissions, $actualPermissions);
    $extra = array_diff($actualPermissions, $expectedPermissions);
    
    echo "Expected: " . json_encode($expectedPermissions) . "\n";
    echo "Missing permissions: " . json_encode($missing) . "\n";
    echo "Extra permissions: " . json_encode($extra) . "\n";
    
    if (empty($missing) && empty($extra)) {
        echo "✅ SUCCESS: All expected permissions are properly handled!\n";
    } else {
        echo "❌ ISSUES FOUND: Check missing/extra permissions above\n";
    }
} else {
    echo "❌ ERROR: master-kapal module not found in matrix result\n";
}

echo "\n=== TEST COMPLETE ===\n";