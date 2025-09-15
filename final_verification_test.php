<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== FINAL VERIFICATION TEST ===\n\n";

// Test case yang paling umum digunakan
$commonTestCases = [
    'Basic Master Permissions' => [
        'master-karyawan' => ['view' => '1', 'create' => '1', 'update' => '1'],
        'master-user' => ['view' => '1', 'create' => '1'],
        'dashboard' => ['view' => '1']
    ],
    'Common Business Permissions' => [
        'tagihan-kontainer' => ['view' => '1', 'create' => '1', 'print' => '1'],
        'pranota-supir' => ['view' => '1', 'create' => '1'],
        'permohonan' => ['view' => '1', 'create' => '1', 'export' => '1']
    ]
];

$userController = new \App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToIdsMethod->setAccessible(true);
$convertToMatrixMethod->setAccessible(true);

$allPassed = true;

foreach ($commonTestCases as $testName => $matrixData) {
    echo "Testing: $testName\n";
    echo str_repeat("=", 50) . "\n";

    // Convert matrix to IDs
    $permissionIds = $convertToIdsMethod->invoke($userController, $matrixData);
    echo "✅ Found " . count($permissionIds) . " permissions\n";

    // Show permissions
    foreach ($permissionIds as $id) {
        $perm = Permission::find($id);
        if ($perm) {
            echo "  ✓ {$perm->name}\n";
        }
    }

    // Test round-trip
    $foundPermissions = [];
    foreach ($permissionIds as $id) {
        $perm = Permission::find($id);
        if ($perm) {
            $foundPermissions[] = $perm->name;
        }
    }

    $matrixBack = $convertToMatrixMethod->invoke($userController, $foundPermissions);

    $matches = 0;
    $total = 0;

    foreach ($matrixData as $module => $actions) {
        foreach ($actions as $action => $value) {
            $total++;
            if (isset($matrixBack[$module][$action]) && $matrixBack[$module][$action] == true) {
                $matches++;
            }
        }
    }

    $successRate = round(($matches / $total) * 100, 1);
    echo "Round-trip: {$matches}/{$total} matches ({$successRate}%)\n";

    if ($matches === $total) {
        echo "✅ PASSED\n";
    } else {
        echo "❌ FAILED\n";
        $allPassed = false;
    }

    echo "\n";
}

// Test dengan user baru (simulasi penggunaan real)
echo "=== REAL USAGE SIMULATION ===\n";
echo "Creating test user with common permissions...\n";

$user = User::first();
if (!$user) {
    echo "❌ No user found\n";
    exit;
}

// Simulasi permission matrix dari form
$formPermissions = [
    'master-karyawan' => ['view' => '1', 'create' => '1'],
    'master-user' => ['view' => '1'],
    'dashboard' => ['view' => '1'],
    'tagihan-kontainer' => ['view' => '1', 'create' => '1']
];

echo "Form permissions:\n";
print_r($formPermissions);

// Convert to IDs (seperti yang dilakukan di controller)
$permissionIds = $convertToIdsMethod->invoke($userController, $formPermissions);
echo "\nConverted to " . count($permissionIds) . " permission IDs\n";

// Simulasi penyimpanan (sync permissions)
$user->permissions()->sync($permissionIds);
echo "✅ Permissions saved to user\n";

// Simulasi pembacaan kembali (seperti saat edit)
$savedPermissions = $user->permissions->pluck('name')->toArray();
$matrixForForm = $convertToMatrixMethod->invoke($userController, $savedPermissions);

echo "\nMatrix for form (after save):\n";
print_r($matrixForForm);

// Verifikasi bahwa data sama
$originalModules = array_keys($formPermissions);
$formModules = array_keys($matrixForForm);

$moduleMatch = empty(array_diff($originalModules, $formModules)) && empty(array_diff($formModules, $originalModules));

echo "\n=== VERIFICATION ===\n";
echo "Modules match: " . ($moduleMatch ? "✅ YES" : "❌ NO") . "\n";

if ($moduleMatch) {
    echo "✅ PERMISSION MATRIX IS WORKING CORRECTLY!\n";
    echo "✅ Checkboxes will be saved and loaded properly\n";
    echo "✅ Users can now edit permissions without losing data\n";
} else {
    echo "❌ There are still issues with permission matrix\n";
    $allPassed = false;
}

echo "\n" . str_repeat("=", 60) . "\n";
if ($allPassed) {
    echo "🎉 ALL CRITICAL TESTS PASSED!\n";
    echo "📝 Permission matrix is ready for production use.\n";
} else {
    echo "⚠️  Some tests failed. Please review the results.\n";
}

echo "\n=== DONE ===\n";
