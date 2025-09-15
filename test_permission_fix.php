<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== TEST PERBAIKAN PERMISSION MATRIX ===\n\n";

// Ambil user pertama untuk testing
$user = User::first();
if (!$user) {
    echo "❌ Tidak ada user ditemukan\n";
    exit;
}

echo "User: {$user->username} (ID: {$user->id})\n";
echo "Current permissions: " . $user->permissions->count() . "\n\n";

// Simulasi data dari form (seperti yang dikirim dari edit.blade.php)
$sampleMatrixData = [
    'dashboard' => [
        'view' => '1'
    ],
    'master-karyawan' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ],
    'tagihan-kontainer' => [
        'view' => '1',
        'create' => '1'
    ]
];

echo "=== SAMPLE MATRIX DATA ===\n";
print_r($sampleMatrixData);
echo "\n";

// Test konversi matrix ke IDs dengan method yang sudah diperbaiki
$userController = new \App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

echo "=== CONVERTING MATRIX TO IDS (AFTER FIX) ===\n";
$permissionIds = $method->invoke($userController, $sampleMatrixData);

echo "Permission IDs found: " . count($permissionIds) . "\n";
$foundPermissions = [];
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        $foundPermissions[] = $perm->name;
        echo "✓ {$perm->name} (ID: {$perm->id})\n";
    } else {
        echo "❌ Permission ID {$id} tidak ditemukan\n";
    }
}

echo "\n=== TESTING ROUND-TRIP CONVERSION ===\n";
// Test konversi balik dari permission names ke matrix
$matrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$matrixMethod->setAccessible(true);
$matrixResult = $matrixMethod->invoke($userController, $foundPermissions);

echo "Matrix result:\n";
print_r($matrixResult);

echo "\n=== VERIFICATION ===\n";
// Verifikasi bahwa data matrix yang dihasilkan sesuai dengan input
$matches = 0;
$total = 0;

foreach ($sampleMatrixData as $module => $actions) {
    if (!isset($matrixResult[$module])) {
        echo "❌ Module $module tidak ditemukan di hasil\n";
        continue;
    }

    foreach ($actions as $action => $value) {
        $total++;
        if (isset($matrixResult[$module][$action]) && $matrixResult[$module][$action] === true) {
            $matches++;
            echo "✅ $module.$action cocok\n";
        } else {
            echo "❌ $module.$action tidak cocok\n";
        }
    }
}

echo "\nMatch rate: $matches/$total (" . round(($matches/$total)*100, 1) . "%)\n";

if ($matches === $total) {
    echo "🎉 ROUND-TRIP CONVERSION BERHASIL!\n";
} else {
    echo "⚠️  Ada beberapa permission yang tidak cocok\n";
}

echo "\n=== DONE ===\n";
