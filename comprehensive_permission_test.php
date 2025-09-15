<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== COMPREHENSIVE PERMISSION MATRIX TEST ===\n\n";

// Test 1: Test dengan berbagai jenis permission
echo "1. TESTING VARIOUS PERMISSION TYPES\n";
echo "===================================\n";

$testCases = [
    'Simple Permissions' => [
        'dashboard' => ['view' => '1'],
        'login' => ['view' => '1'],
        'logout' => ['view' => '1']
    ],
    'Master Modules' => [
        'master-karyawan' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-user' => ['view' => '1', 'create' => '1', 'update' => '1'],
        'master-kontainer' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-tujuan' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-kegiatan' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-permission' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-mobil' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1'],
        'master-pricelist-sewa-kontainer' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1']
    ],
    'Complex Modules' => [
        'tagihan-kontainer' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1', 'print' => '1', 'export' => '1'],
        'pranota-supir' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1', 'print' => '1'],
        'pembayaran-pranota-supir' => ['view' => '1', 'create' => '1', 'print' => '1'], // Only these exist in DB
        'permohonan' => ['view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1', 'export' => '1', 'import' => '1'] // No print permission
    ],
    'Admin Permissions' => [
        'admin' => ['debug' => '1', 'features' => '1'], // Only these exist in DB
        'user-approval' => ['view' => '1'], // Only view exists
        'approval' => ['dashboard' => '1', 'create' => '1', 'mass_process' => '1']
    ]
];

$userController = new \App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToIdsMethod->setAccessible(true);
$convertToMatrixMethod->setAccessible(true);

$totalTests = 0;
$passedTests = 0;

foreach ($testCases as $testName => $matrixData) {
    echo "Testing: $testName\n";
    echo str_repeat("-", 50) . "\n";

    // Count total permissions in this test
    $expectedCount = 0;
    foreach ($matrixData as $module => $actions) {
        $expectedCount += count(array_filter($actions, function($value) { return $value == '1'; }));
    }

    echo "Expected permissions: $expectedCount\n";

    // Convert matrix to IDs
    $permissionIds = $convertToIdsMethod->invoke($userController, $matrixData);
    echo "Found permission IDs: " . count($permissionIds) . "\n";

    if (count($permissionIds) === $expectedCount) {
        echo "✅ Count matches\n";
        $countMatch = true;
    } else {
        echo "❌ Count mismatch\n";
        $countMatch = false;
    }

    // Show found permissions
    $foundPermissions = [];
    foreach ($permissionIds as $id) {
        $perm = Permission::find($id);
        if ($perm) {
            $foundPermissions[] = $perm->name;
        }
    }

    echo "Permissions found:\n";
    foreach ($foundPermissions as $perm) {
        echo "  ✓ $perm\n";
    }

    // Test round-trip conversion
    echo "\nRound-trip test:\n";
    $matrixBack = $convertToMatrixMethod->invoke($userController, $foundPermissions);

    $matches = 0;
    $total = 0;

    foreach ($matrixData as $module => $actions) {
        if (!isset($matrixBack[$module])) {
            echo "❌ Module $module missing in round-trip\n";
            continue;
        }

        foreach ($actions as $action => $value) {
            $total++;
            if (isset($matrixBack[$module][$action]) && $matrixBack[$module][$action] == true) {
                $matches++;
            } else {
                echo "❌ $module.$action not matching in round-trip\n";
            }
        }
    }

    $roundTripSuccess = ($matches === $total && $countMatch);
    echo "Round-trip result: $matches/$total matches\n";

    if ($roundTripSuccess) {
        echo "✅ $testName PASSED\n";
        $passedTests++;
    } else {
        echo "❌ $testName FAILED\n";
    }

    $totalTests++;
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

// Test 2: Test dengan data user real
echo "2. TESTING WITH REAL USER DATA\n";
echo "===============================\n";

$users = User::with('permissions')->take(3)->get();
foreach ($users as $user) {
    echo "Testing user: {$user->username} (ID: {$user->id})\n";

    $userPermissions = $user->permissions->pluck('name')->toArray();
    echo "User has " . count($userPermissions) . " permissions\n";

    // Convert to matrix
    $userMatrix = $convertToMatrixMethod->invoke($userController, $userPermissions);
    echo "Matrix modules: " . count($userMatrix) . "\n";

    // Convert back to IDs
    $matrixIds = $convertToIdsMethod->invoke($userController, $userMatrix);
    echo "Converted back to " . count($matrixIds) . " IDs\n";

    // Check if all original permissions are preserved
    $originalIds = $user->permissions->pluck('id')->toArray();
    sort($originalIds);
    sort($matrixIds);

    if ($originalIds === $matrixIds) {
        echo "✅ User permissions round-trip successful\n";
        $passedTests++;
    } else {
        echo "❌ User permissions round-trip failed\n";
        echo "Original IDs: " . implode(', ', $originalIds) . "\n";
        echo "Matrix IDs: " . implode(', ', $matrixIds) . "\n";
    }

    $totalTests++;
    echo "\n";
}

// Test 3: Edge cases
echo "3. TESTING EDGE CASES\n";
echo "====================\n";

$edgeCases = [
    'Empty Matrix' => [],
    'Single Permission' => ['dashboard' => ['view' => '1']],
    'Non-existent Module' => ['non-existent-module' => ['view' => '1']],
    'Mixed Valid/Invalid' => [
        'dashboard' => ['view' => '1'],
        'non-existent' => ['view' => '1'],
        'master-karyawan' => ['view' => '1']
    ]
];

foreach ($edgeCases as $caseName => $matrixData) {
    echo "Testing: $caseName\n";

    try {
        $permissionIds = $convertToIdsMethod->invoke($userController, $matrixData);
        echo "Result: " . count($permissionIds) . " permissions found\n";

        if (count($permissionIds) > 0) {
            echo "Found permissions:\n";
            foreach ($permissionIds as $id) {
                $perm = Permission::find($id);
                if ($perm) {
                    echo "  ✓ {$perm->name}\n";
                }
            }
        }

        echo "✅ Edge case handled successfully\n";
        $passedTests++;
    } catch (Exception $e) {
        echo "❌ Edge case failed: " . $e->getMessage() . "\n";
    }

    $totalTests++;
    echo "\n";
}

// Summary
echo "=== TEST SUMMARY ===\n";
echo "Total tests: $totalTests\n";
echo "Passed tests: $passedTests\n";
echo "Success rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n";

if ($passedTests === $totalTests) {
    echo "\n🎉 ALL TESTS PASSED! Permission matrix is working perfectly.\n";
} else {
    echo "\n⚠️  Some tests failed. Please review the results above.\n";
}

echo "\n=== DONE ===\n";
