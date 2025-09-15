<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permissions = Permission::all()->pluck('name')->toArray();

// Permission yang HARUS dipertahankan (digunakan di sidebar dan routes)
$essentialPermissions = [
    // Master permissions (digunakan di sidebar)
    'master-karyawan',
    'master-user',
    'master-kontainer',
    'master-pricelist-sewa-kontainer',
    'master-tujuan',
    'master-kegiatan',
    'master-permission',
    'master-mobil',

    // Main feature permissions
    'dashboard',
    'tagihan-kontainer',
    'pranota-supir',
    'pembayaran-pranota-supir',
    'permohonan',
    'user-approval',

    // Basic permissions
    'login',
    'logout',

    // Admin permissions
    'admin.features',
    'storage.local',
    'admin.debug.perms',
    'admin.user-approval.index',
    'admin.user-approval.show',
    'admin.user-approval.approve',
    'admin.user-approval.reject',

    // Profile permissions
    'profile.show',
    'profile.edit',
    'profile.update.account',
    'profile.update.personal',
    'profile.update.avatar',
    'profile.destroy',

    // Supir permissions
    'supir.dashboard',
    'supir.checkpoint.create',
    'supir.checkpoint.store',

    // Approval permissions
    'approval.dashboard',
    'approval.mass_process',
    'approval.create',
    'approval.store',
    'approval.riwayat',
];

// Permission yang BISA dihapus (duplikat atau tidak digunakan)
$permissionsToDelete = [];

// Cari permission duplikat berdasarkan pola
foreach ($permissions as $permission) {
    // Hapus permission detail yang duplikat dengan permission sederhana
    if (preg_match('/^(master|tagihan|pranota|pembayaran|permohonan|user)\./', $permission)) {
        $permissionsToDelete[] = $permission;
    }

    // Hapus permission lama yang sudah tidak digunakan
    if (in_array($permission, [
        'master-pranota-tagihan-kontainer', // Sudah diganti dengan tagihan-kontainer
        'master-pranota', // Duplikat
        'master-pembayaran-pranota-supir', // Duplikat
        'master-permohonan', // Duplikat
    ])) {
        $permissionsToDelete[] = $permission;
    }
}

// Permission yang akan dipertahankan
$permissionsToKeep = array_diff($permissions, $permissionsToDelete);

echo '=== ANALISIS PERMISSION CLEANUP ===' . PHP_EOL;
echo 'Total permissions saat ini: ' . count($permissions) . PHP_EOL;
echo 'Permissions yang akan dipertahankan: ' . count($permissionsToKeep) . PHP_EOL;
echo 'Permissions yang akan dihapus: ' . count($permissionsToDelete) . PHP_EOL;
echo 'Penghematan: ' . (count($permissions) - count($permissionsToKeep)) . ' permissions' . PHP_EOL . PHP_EOL;

echo '=== PERMISSION YANG AKAN DIPERTAHANKAN ===' . PHP_EOL;
foreach ($permissionsToKeep as $perm) {
    echo "✓ $perm" . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION YANG AKAN DIHAPUS ===' . PHP_EOL;
foreach ($permissionsToDelete as $perm) {
    echo "✗ $perm" . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION ESSENTIAL (WAJIB DIPERTAHANKAN) ===' . PHP_EOL;
foreach ($essentialPermissions as $perm) {
    if (in_array($perm, $permissions)) {
        echo "🔒 $perm" . PHP_EOL;
    } else {
        echo "⚠ MISSING: $perm" . PHP_EOL;
    }
}

echo PHP_EOL;
echo '=== RINGKASAN ===' . PHP_EOL;
echo 'Total permissions: ' . count($permissions) . PHP_EOL;
echo 'Essential permissions: ' . count($essentialPermissions) . PHP_EOL;
echo 'Permissions to keep: ' . count($permissionsToKeep) . PHP_EOL;
echo 'Permissions to delete: ' . count($permissionsToDelete) . PHP_EOL;
echo 'Final count after cleanup: ' . count($permissionsToKeep) . PHP_EOL;
echo 'Space saved: ' . (count($permissions) - count($permissionsToKeep)) . ' permissions (' . round((count($permissions) - count($permissionsToKeep)) / count($permissions) * 100, 1) . '%)' . PHP_EOL;
