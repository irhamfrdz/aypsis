<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== DETAILED PERMISSION ANALYSIS ===\n\n";

// Define expected permissions from seeder
$expectedModules = [
    'dashboard' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-karyawan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-user' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-tujuan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-kegiatan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-permission' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-mobil' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'master-pricelist-sewa-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'tagihan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'permohonan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
    'user-approval' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
];

echo "Expected permissions per module:\n";
$totalExpected = 0;
foreach ($expectedModules as $module => $actions) {
    $count = count($actions);
    $totalExpected += $count;
    echo "- $module: $count actions\n";
}
echo "Total expected Accurate-style permissions: $totalExpected\n\n";

// Check what we actually have
$existingPermissions = Permission::pluck('name')->toArray();
$foundAccuratePermissions = [];

foreach ($expectedModules as $module => $actions) {
    foreach ($actions as $action) {
        $permissionName = $module . '-' . $action;
        if (in_array($permissionName, $existingPermissions)) {
            $foundAccuratePermissions[] = $permissionName;
        }
    }
}

echo "Found Accurate-style permissions: " . count($foundAccuratePermissions) . "\n";
echo "Sample found permissions:\n";
foreach (array_slice($foundAccuratePermissions, 0, 20) as $perm) {
    echo "- $perm\n";
}

$missingPermissions = [];
foreach ($expectedModules as $module => $actions) {
    foreach ($actions as $action) {
        $permissionName = $module . '-' . $action;
        if (!in_array($permissionName, $existingPermissions)) {
            $missingPermissions[] = $permissionName;
        }
    }
}

echo "\nMissing permissions: " . count($missingPermissions) . "\n";
if (!empty($missingPermissions)) {
    echo "Sample missing permissions:\n";
    foreach (array_slice($missingPermissions, 0, 20) as $perm) {
        echo "- $perm\n";
    }
}

// Check for duplicates or variations
echo "\n=== CHECKING FOR VARIATIONS ===\n";
$variations = [];
foreach ($expectedModules as $module => $actions) {
    foreach ($actions as $action) {
        $permissionName = $module . '-' . $action;

        // Look for similar permissions
        $similar = array_filter($existingPermissions, function($existing) use ($module, $action) {
            return strpos($existing, $module) !== false && strpos($existing, $action) !== false;
        });

        if (!empty($similar) && !in_array($permissionName, $existingPermissions)) {
            $variations[$permissionName] = $similar;
        }
    }
}

if (!empty($variations)) {
    echo "Found variations of expected permissions:\n";
    foreach ($variations as $expected => $found) {
        echo "- Expected: $expected\n";
        echo "  Found: " . implode(', ', $found) . "\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Expected: $totalExpected Accurate-style permissions\n";
echo "Found: " . count($foundAccuratePermissions) . " Accurate-style permissions\n";
echo "Missing: " . count($missingPermissions) . " permissions\n";

if (count($foundAccuratePermissions) === $totalExpected) {
    echo "✅ All expected permissions are present!\n";
} else {
    echo "❌ Some permissions are missing. Need to run seeder again.\n";
}
