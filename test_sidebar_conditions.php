<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo 'Admin user not found!' . PHP_EOL;
    exit;
}

Auth::login($user);

echo '=== REAL-TIME SIDEBAR CONDITION TEST ===' . PHP_EOL;

// Simulate the exact conditions from app.blade.php
$isAdmin = true; // method_exists($user, 'hasRole') && $user->hasRole('admin');
echo 'Is admin: ' . ($isAdmin ? 'YES' : 'NO') . PHP_EOL;

// Check master permissions (from line 175-195)
$hasMasterPermissions = $user && (
    $user->can('master-karyawan-view') ||
    $user->can('master-user-view') ||
    $user->can('master-kontainer-view') ||
    $user->can('master-pricelist-sewa-kontainer-view') ||
    $user->can('master-tujuan-view') ||
    $user->can('master-kegiatan-view') ||
    $user->can('master-permission-view') ||
    $user->can('master-mobil-view') ||
    $user->can('master-divisi-view') ||
    $user->can('master-cabang-view') ||
    $user->can('master-pekerjaan-view') ||
    $user->can('master-pajak-view') ||
    $user->can('master-bank-view') ||
    $user->can('master-coa-view') ||
    $user->can('master-vendor-bengkel-view') ||
    $user->can('master-kode-nomor-view')
);

echo 'Has master permissions: ' . ($hasMasterPermissions ? 'YES' : 'NO') . PHP_EOL;

// Show master section condition (from line 197)
$showMasterSection = $isAdmin || $hasMasterPermissions;
echo 'Show master section (dropdown): ' . ($showMasterSection ? 'YES' : 'NO') . PHP_EOL;

// Alternative condition (from line 362)
$alternativeCondition = $user && (
    $user->can('master-karyawan-view') ||
    $user->can('master-user-view') ||
    $user->can('master-kontainer-view') ||
    $user->can('master-tujuan-view') ||
    $user->can('master-kegiatan-view') ||
    $user->can('master-permission-view') ||
    $user->can('master-mobil-view') ||
    $user->can('master-divisi-view') ||
    $user->can('master-pajak-view') ||
    $user->can('master-pricelist-sewa-kontainer-view') ||
    $user->can('master-bank-view') ||
    $user->can('master-coa-view') ||
    $user->can('master-vendor-bengkel-view') ||
    $user->can('master-kode-nomor-view')
);

echo 'Alternative condition (direct): ' . ($alternativeCondition ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL;
echo '=== CONCLUSION ===' . PHP_EOL;
if ($showMasterSection) {
    echo '✅ Dropdown Master Data section WILL be shown' . PHP_EOL;
    echo '✅ Menu Kode Nomor should be visible in dropdown' . PHP_EOL;
} elseif ($alternativeCondition) {
    echo '✅ Alternative Master Data section WILL be shown' . PHP_EOL;
    echo '❌ But menu Kode Nomor is NOT in alternative section!' . PHP_EOL;
} else {
    echo '❌ NO Master Data section will be shown' . PHP_EOL;
}

echo PHP_EOL;
echo '=== SPECIFIC PERMISSION CHECK ===' . PHP_EOL;
echo 'master-kode-nomor-view: ' . ($user->can('master-kode-nomor-view') ? 'YES' : 'NO') . PHP_EOL;
echo 'master-vendor-bengkel-view: ' . ($user->can('master-vendor-bengkel-view') ? 'YES' : 'NO') . PHP_EOL;
echo 'master-vendor-bengkel.view: ' . ($user->can('master-vendor-bengkel.view') ? 'YES' : 'NO') . PHP_EOL;
