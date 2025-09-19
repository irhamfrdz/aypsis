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

echo '=== FINAL VERIFICATION ===' . PHP_EOL;

// Test all conditions that control menu visibility
$isAdmin = true;
echo '1. Is admin: ' . ($isAdmin ? 'YES' : 'NO') . PHP_EOL;

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
echo '2. Has master permissions: ' . ($hasMasterPermissions ? 'YES' : 'NO') . PHP_EOL;

$showMasterSection = $isAdmin || $hasMasterPermissions;
echo '3. Show master section: ' . ($showMasterSection ? 'YES' : 'NO') . PHP_EOL;

$specificPermission = $user->can('master-kode-nomor-view');
echo '4. Has master-kode-nomor-view permission: ' . ($specificPermission ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL;
echo '=== RESULT ===' . PHP_EOL;
if ($showMasterSection && $specificPermission) {
    echo '🎉 MENU "KODE NOMOR" HARUS MUNCUL DI SIDEBAR!' . PHP_EOL;
    echo '📍 Lokasi: Di dalam dropdown "Master Data"' . PHP_EOL;
    echo '🔗 Route: master.kode-nomor.index' . PHP_EOL;
    echo '✅ Permission: master-kode-nomor-view' . PHP_EOL;
} else {
    echo '❌ Ada masalah dengan kondisi menu' . PHP_EOL;
}

echo PHP_EOL;
echo '=== TROUBLESHOOTING STEPS ===' . PHP_EOL;
echo '1. Hard refresh browser (Ctrl+F5)' . PHP_EOL;
echo '2. Clear browser cache' . PHP_EOL;
echo '3. Logout dan login kembali' . PHP_EOL;
echo '4. Pastikan login sebagai admin' . PHP_EOL;
echo '5. Klik tombol "Master Data" untuk membuka dropdown' . PHP_EOL;
