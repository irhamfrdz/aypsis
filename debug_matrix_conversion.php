<?php

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== FOCUSED DEBUG: convertPermissionsToMatrix for pranota-rit-kenek ===\n\n";

// Get admin user
$adminUser = User::where('username', 'admin')->first();

if ($adminUser) {
    echo "Admin user found (ID: {$adminUser->id})\n\n";
    
    // Get current permission names
    $currentPermissionNames = $adminUser->permissions->pluck('name')->toArray();
    
    // Filter for pranota-rit related permissions
    $pranotaRitPermissions = array_filter($currentPermissionNames, function($name) {
        return strpos($name, 'pranota-rit') === 0;
    });
    
    echo "Current pranota-rit permissions for admin:\n";
    foreach ($pranotaRitPermissions as $perm) {
        echo "   - {$perm}\n";
    }
    echo "\n";
    
    // Create UserController and test conversion with just pranota-rit-kenek permissions
    $userController = new UserController();
    
    $kenekOnlyPermissions = array_filter($currentPermissionNames, function($name) {
        return strpos($name, 'pranota-rit-kenek-') === 0;
    });
    
    echo "Testing conversion with only pranota-rit-kenek permissions:\n";
    foreach ($kenekOnlyPermissions as $perm) {
        echo "   - {$perm}\n";
    }
    echo "\n";
    
    $matrixResult = $userController->testConvertPermissionsToMatrix($kenekOnlyPermissions);
    
    echo "Matrix result:\n";
    print_r($matrixResult);
    
    if (isset($matrixResult['pranota-rit-kenek'])) {
        echo "\npranota-rit-kenek matrix found:\n";
        foreach ($matrixResult['pranota-rit-kenek'] as $action => $state) {
            $status = $state ? 'CHECKED' : 'UNCHECKED';
            echo "   {$action}: {$status}\n";
        }
    } else {
        echo "\nERROR: No pranota-rit-kenek matrix found!\n";
    }
    
    // Test with the full permission list
    echo "\n--- Testing with ALL admin permissions ---\n";
    $fullMatrixResult = $userController->testConvertPermissionsToMatrix($currentPermissionNames);
    
    if (isset($fullMatrixResult['pranota-rit-kenek'])) {
        echo "pranota-rit-kenek matrix found in full result:\n";
        foreach ($fullMatrixResult['pranota-rit-kenek'] as $action => $state) {
            $status = $state ? 'CHECKED' : 'UNCHECKED';
            echo "   {$action}: {$status}\n";
        }
    } else {
        echo "ERROR: No pranota-rit-kenek matrix found in full result!\n";
    }
}

echo "\n=== DEBUG COMPLETE ===\n";