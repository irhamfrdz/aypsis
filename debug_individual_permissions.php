<?php

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== DEBUG: Specific Permission Processing ===\n\n";

// Test specific permission processing
$testPermissions = [
    'pranota-rit-kenek-view',
    'pranota-rit-kenek-create',
    'pranota-rit-view',
    'pranota-rit-create'
];

$userController = new UserController();

foreach ($testPermissions as $perm) {
    echo "Testing: {$perm}\n";
    
    $result = $userController->testConvertPermissionsToMatrix([$perm]);
    
    echo "Result:\n";
    foreach ($result as $module => $actions) {
        echo "  Module: {$module}\n";
        foreach ($actions as $action => $state) {
            echo "    {$action}: " . ($state ? 'true' : 'false') . "\n";
        }
    }
    echo "\n";
}

echo "=== DEBUG COMPLETE ===\n";