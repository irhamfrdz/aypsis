<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== DETAIL ANALISIS PERMISSION MASTER ===\n";

$masterPerms = Permission::where('name', 'like', 'master-%')->get();

// Kelompokkan berdasarkan sub-module
$masterGroups = [];
foreach ($masterPerms as $perm) {
    // master-karyawan-view -> karyawan
    $parts = explode('-', $perm->name);
    if (count($parts) >= 2) {
        $subModule = $parts[1];
        if (!isset($masterGroups[$subModule])) {
            $masterGroups[$subModule] = [];
        }
        $masterGroups[$subModule][] = $perm->name;
    }
}

echo "Sub-module dalam master:\n";
foreach ($masterGroups as $subModule => $perms) {
    echo "- $subModule: " . count($perms) . " permissions\n";
}

echo "\n=== CONTOH PERMISSION PER SUB-MODULE ===\n";
foreach ($masterGroups as $subModule => $perms) {
    echo "$subModule: " . implode(', ', array_slice($perms, 0, 3)) . (count($perms) > 3 ? '...' : '') . "\n";
}

echo "\n=== PERMISSION LAINNYA (BUKAN MASTER) ===\n";

// Hitung permission non-master
$nonMasterPerms = Permission::where('name', 'not like', 'master-%')->count();
echo "Total permission non-master: $nonMasterPerms\n";

// Kelompokkan permission non-master
$otherPatterns = [
    'tagihan-' => 'Tagihan',
    'pranota-' => 'Pranota',
    'pembayaran-' => 'Pembayaran',
    'perbaikan-' => 'Perbaikan',
    'admin-' => 'Admin',
    'profile-' => 'Profile',
    'supir-' => 'Supir',
    'approval-' => 'Approval',
    'permohonan-' => 'Permohonan',
    'user-' => 'User',
    'daftar-' => 'Daftar'
];

foreach ($otherPatterns as $pattern => $label) {
    $count = Permission::where('name', 'like', $pattern . '%')->count();
    echo "$label permissions: $count\n";
}

echo "\n=== APAKAH ADA DUPLICATE PERMISSION? ===\n";
$duplicateNames = Permission::select('name')
    ->groupBy('name')
    ->havingRaw('COUNT(*) > 1')
    ->get();

echo "Permission names yang duplikat: " . $duplicateNames->count() . "\n";
if ($duplicateNames->count() > 0) {
    foreach ($duplicateNames as $dup) {
        $count = Permission::where('name', $dup->name)->count();
        echo "- {$dup->name}: $count kali\n";
    }
}

echo "\n=== PERMISSION PALING BARU (ID TERTINGGI) ===\n";
$latestPerms = Permission::orderBy('id', 'desc')->limit(10)->get();
foreach ($latestPerms as $perm) {
    echo "{$perm->id}: {$perm->name}\n";
}
