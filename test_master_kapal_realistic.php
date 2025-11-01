<?php

echo "=== TESTING MASTER KAPAL PERMISSIONS (REALISTIC) ===\n\n";

// Test conversion from permission names to matrix
$userController = new App\Http\Controllers\UserController();

// Test dengan permission yang benar-benar ada di database
$testPermissions = [
    'master-kapal.view',
    'master-kapal.create', 
    'master-kapal.edit',
    'master-kapal.delete'
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
    
    // Check roundtrip
    echo "3. Testing roundtrip (permissions -> matrix -> ids -> permissions):\n";
    echo "Original permissions: " . json_encode($testPermissions) . "\n";
    echo "Final permissions: " . json_encode($actualPermissions) . "\n";
    
    $originalSet = array_unique($testPermissions);
    $finalSet = array_unique($actualPermissions);
    sort($originalSet);
    sort($finalSet);
    
    if ($originalSet === $finalSet) {
        echo "✅ PERFECT ROUNDTRIP: Original and final permissions match exactly!\n";
    } else {
        $missing = array_diff($originalSet, $finalSet);
        $extra = array_diff($finalSet, $originalSet);
        echo "⚠️ ROUNDTRIP ISSUES:\n";
        echo "Missing: " . json_encode($missing) . "\n";
        echo "Extra: " . json_encode($extra) . "\n";
    }
} else {
    echo "❌ ERROR: master-kapal module not found in matrix result\n";
}

echo "\n=== TEST COMPLETE ===\n";