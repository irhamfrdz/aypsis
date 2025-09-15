<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== COMPREHENSIVE PERMISSION DEBUGGING ===\n\n";

// Get all permissions from database
$allPermissions = Permission::all()->pluck('name')->toArray();
echo "Total permissions in database: " . count($allPermissions) . "\n\n";

// Create UserController instance
$userController = new App\Http\Controllers\UserController();

// Use reflection to access private methods
$reflection = new ReflectionClass($userController);
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');

// Make methods accessible
$convertToMatrixMethod->setAccessible(true);
$convertToIdsMethod->setAccessible(true);

// Test each permission individually
$failedConversions = [];
$successfulConversions = [];

echo "Testing individual permission conversions...\n";
foreach ($allPermissions as $permissionName) {
    try {
    // Test convertPermissionsToMatrix (pass array of permission names)
    $matrixResult = $convertToMatrixMethod->invoke($userController, [$permissionName]);

    // Test convertMatrixPermissionsToIds (round-trip test) - pass the matrix directly
    $idsResult = $convertToIdsMethod->invoke($userController, $matrixResult);

        // Check if the original permission ID is in the result
        $originalPermission = Permission::where('name', $permissionName)->first();
        if ($originalPermission && in_array($originalPermission->id, $idsResult)) {
            $successfulConversions[] = $permissionName;
        } else {
            $failedConversions[] = [
                'permission' => $permissionName,
                'matrix_result' => $matrixResult,
                'ids_result' => $idsResult,
                'original_id' => $originalPermission ? $originalPermission->id : 'NOT_FOUND'
            ];
        }
    } catch (Exception $e) {
        $failedConversions[] = [
            'permission' => $permissionName,
            'error' => $e->getMessage()
        ];
    }
}

echo "\n=== RESULTS ===\n";
echo "Successful conversions: " . count($successfulConversions) . "\n";
echo "Failed conversions: " . count($failedConversions) . "\n\n";

if (!empty($failedConversions)) {
    echo "=== FAILED PERMISSIONS ===\n";
    foreach ($failedConversions as $failed) {
        echo "Permission: " . $failed['permission'] . "\n";
        if (isset($failed['error'])) {
            echo "  Error: " . $failed['error'] . "\n";
        } else {
            echo "  Matrix result: " . json_encode($failed['matrix_result']) . "\n";
            echo "  IDs result: " . json_encode($failed['ids_result']) . "\n";
            echo "  Original ID: " . $failed['original_id'] . "\n";
        }
        echo "\n";
    }
}

// Test with a sample user to see real-world issues
echo "=== TESTING WITH SAMPLE USER ===\n";
$sampleUser = User::with('permissions')->first();
if ($sampleUser) {
    echo "Testing with user: " . $sampleUser->username . "\n";
    $userPermissions = $sampleUser->permissions->pluck('name')->toArray();

    echo "User has " . count($userPermissions) . " permissions\n";

    // Test conversion
    try {
    // For user permissions, $userPermissions is already an array of names
    $matrixResult = $convertToMatrixMethod->invoke($userController, $userPermissions);
    // Pass matrix directly to convertMatrixPermissionsToIds
    $idsResult = $convertToIdsMethod->invoke($userController, $matrixResult);

        $originalIds = $sampleUser->permissions->pluck('id')->toArray();
        $missingIds = array_diff($originalIds, $idsResult);
        $extraIds = array_diff($idsResult, $originalIds);

        echo "Original permission count: " . count($originalIds) . "\n";
        echo "Converted permission count: " . count($idsResult) . "\n";
        echo "Missing permissions: " . count($missingIds) . "\n";
        echo "Extra permissions: " . count($extraIds) . "\n";

        if (!empty($missingIds)) {
            echo "Missing permission IDs: " . implode(', ', $missingIds) . "\n";
            foreach ($missingIds as $missingId) {
                $perm = Permission::find($missingId);
                if ($perm) {
                    echo "  - " . $perm->name . " (ID: $missingId)\n";
                }
            }
        }

        if (!empty($extraIds)) {
            echo "Extra permission IDs: " . implode(', ', $extraIds) . "\n";
            foreach ($extraIds as $extraId) {
                $perm = Permission::find($extraId);
                if ($perm) {
                    echo "  - " . $perm->name . " (ID: $extraId)\n";
                }
            }
        }

    } catch (Exception $e) {
        echo "Error testing with user: " . $e->getMessage() . "\n";
    }
} else {
    echo "No users found in database\n";
}

echo "\n=== DEBUGGING COMPLETE ===\n";
