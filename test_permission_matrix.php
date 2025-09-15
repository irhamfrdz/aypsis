<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\UserController;
use App\Models\User;

// Create a UserController instance
$controller = new UserController();

// Test permissions array
$testPermissions = [
    'master.karyawan.index',
    'dashboard-view',
    'master-pranota-tagihan-kontainer',
    'admin.debug.perms',
    'profile.show',
    'supir.dashboard',
    'approval.mass_process',
    'tagihan-kontainer-view',
    'permohonan-create',
    'user-approval',
    'storage.local',
    'login'
];

echo "Testing convertPermissionsToMatrix method:\n";
echo "Input permissions: " . json_encode($testPermissions, JSON_PRETTY_PRINT) . "\n\n";

// Use reflection to access the private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

// Call the method
$result = $method->invoke($controller, $testPermissions);

echo "Output matrix: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

echo "Test completed successfully!\n";
