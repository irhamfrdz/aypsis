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

echo '=== SIDEBAR LOGIC SIMULATION ===' . PHP_EOL;

// Simulate the sidebar logic from app.blade.php
$isAdmin = true; // Assuming admin user
echo 'Is admin: ' . ($isAdmin ? 'YES' : 'NO') . PHP_EOL;

// Check if user has any master data permissions
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
    $user->can('master-vendor-bengkel.view') ||
    $user->can('master-kode-nomor-view')
);

echo 'Has master permissions: ' . ($hasMasterPermissions ? 'YES' : 'NO') . PHP_EOL;

// Show master section if user is admin OR has any master permissions
$showMasterSection = $isAdmin || $hasMasterPermissions;
echo 'Show master section: ' . ($showMasterSection ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL;
echo '=== INDIVIDUAL PERMISSION CHECKS ===' . PHP_EOL;
$permissions = [
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
    'master-coa-view',
    'master-vendor-bengkel.view',
    'master-kode-nomor-view'
];

foreach ($permissions as $perm) {
    $hasPerm = $user->can($perm);
    echo $perm . ': ' . ($hasPerm ? 'YES' : 'NO') . PHP_EOL;
}
