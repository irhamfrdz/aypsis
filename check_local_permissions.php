<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== LOCAL DATABASE: Permission Check ===\n";

$permissions = Permission::all();
$totalPermissions = $permissions->count();

echo "📊 Total permissions: $totalPermissions\n\n";

echo "🔍 Sample permissions (first 30):\n";
foreach($permissions->take(30) as $index => $perm) {
    echo sprintf("%2d. %s\n", $index + 1, $perm->name);
}

echo "\n📋 Permission categories:\n";

// Group permissions by category
$categories = [
    'dashboard' => [],
    'master' => [],
    'operational' => [],
    'system' => [],
    'other' => []
];

foreach($permissions as $perm) {
    $name = $perm->name;

    if (strpos($name, 'dashboard') === 0) {
        $categories['dashboard'][] = $name;
    } elseif (strpos($name, 'master-') === 0) {
        $categories['master'][] = $name;
    } elseif (in_array($name, ['login', 'logout', 'profile', 'storage-local'])) {
        $categories['system'][] = $name;
    } elseif (strpos($name, 'pranota') === 0 || strpos($name, 'pembayaran') === 0 || strpos($name, 'tagihan') === 0 || strpos($name, 'permohonan') === 0 || strpos($name, 'perbaikan') === 0) {
        $categories['operational'][] = $name;
    } else {
        $categories['other'][] = $name;
    }
}

foreach($categories as $category => $perms) {
    echo "- $category: " . count($perms) . " permissions\n";
}

echo "\n🎯 Master Data Permissions (Critical for sidebar):\n";
$masterPermissions = $permissions->filter(function($perm) {
    return strpos($perm->name, 'master-') === 0;
});

$criticalMasterPerms = [
    'master-karyawan-view',
    'master-user-view',
    'master-kontainer-view',
    'master-pricelist-sewa-kontainer-view',
    'master-tujuan-view',
    'master-kegiatan-view',
    'master-permission-view',
    'master-mobil-view',
    'master-divisi-view',
    'master-cabang-view',
    'master-pekerjaan-view',
    'master-pajak-view',
    'master-bank-view',
    'master-coa-view'
];

echo "Required for sidebar: " . count($criticalMasterPerms) . "\n";
$found = 0;
foreach($criticalMasterPerms as $perm) {
    $exists = $permissions->contains('name', $perm);
    echo "- $perm: " . ($exists ? '✅' : '❌') . "\n";
    if ($exists) $found++;
}

echo "\n📈 Summary:\n";
echo "- Total permissions: $totalPermissions\n";
echo "- Master permissions: " . count($masterPermissions) . "\n";
echo "- Critical sidebar permissions found: $found/" . count($criticalMasterPerms) . "\n";

if ($found < count($criticalMasterPerms)) {
    echo "\n⚠️  WARNING: Some critical permissions are missing!\n";
    echo "This could cause Master Data menu to not appear in sidebar.\n";
}