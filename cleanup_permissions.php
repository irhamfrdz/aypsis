<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permissions = Permission::all()->pluck('name')->toArray();

// Permission yang HARUS DIPERTAHANKAN (berdasarkan penggunaan di sidebar dan routes)
$keepPermissions = [
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

// Permission yang BISA DIHAPUS (duplikat atau tidak terpakai)
$deletePermissions = [];

// Cari permission yang tidak ada di daftar keep
foreach ($permissions as $permission) {
    if (!in_array($permission, $keepPermissions)) {
        $deletePermissions[] = $permission;
    }
}

echo '=== RENCANA CLEANUP PERMISSION ===' . PHP_EOL;
echo 'Total permissions saat ini: ' . count($permissions) . PHP_EOL;
echo 'Permissions yang akan dipertahankan: ' . count($keepPermissions) . PHP_EOL;
echo 'Permissions yang akan dihapus: ' . count($deletePermissions) . PHP_EOL;
echo 'Penghematan: ' . count($deletePermissions) . ' permissions (' . round(count($deletePermissions) / count($permissions) * 100, 1) . '%)' . PHP_EOL . PHP_EOL;

echo '=== PERMISSION YANG AKAN DIPERTAHANKAN ===' . PHP_EOL;
foreach ($keepPermissions as $perm) {
    echo "✓ $perm" . PHP_EOL;
}

echo PHP_EOL;
echo '=== SAMPLE PERMISSION YANG AKAN DIHAPUS (20 pertama) ===' . PHP_EOL;
$sampleDelete = array_slice($deletePermissions, 0, 20);
foreach ($sampleDelete as $perm) {
    echo "✗ $perm" . PHP_EOL;
}
if (count($deletePermissions) > 20) {
    echo "... dan " . (count($deletePermissions) - 20) . " permission lainnya" . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION DUPLIKAT YANG AKAN DIHAPUS ===' . PHP_EOL;
$duplicatePatterns = [
    'master.karyawan' => 'master-karyawan',
    'master.user' => 'master-user',
    'master.kontainer' => 'master-kontainer',
    'tagihan-kontainer' => 'tagihan-kontainer',
    'pranota-supir' => 'pranota-supir',
    'pembayaran-pranota-supir' => 'pembayaran-pranota-supir',
    'permohonan' => 'permohonan',
    'user-approval' => 'user-approval',
    'dashboard' => 'dashboard',
];

foreach ($duplicatePatterns as $detailPrefix => $simpleVersion) {
    $duplicates = array_filter($deletePermissions, function($perm) use ($detailPrefix) {
        return strpos($perm, $detailPrefix . '.') === 0;
    });

    if (!empty($duplicates)) {
        echo "Duplikat dari '$simpleVersion':" . PHP_EOL;
        foreach ($duplicates as $dup) {
            echo "  - $dup" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

echo '=== RINGKASAN AKHIR ===' . PHP_EOL;
echo 'Total permissions saat ini: ' . count($permissions) . PHP_EOL;
echo 'Permissions essential: ' . count($keepPermissions) . PHP_EOL;
echo 'Permissions redundant: ' . count($deletePermissions) . PHP_EOL;
echo 'Final count: ' . count($keepPermissions) . ' permissions' . PHP_EOL;
echo 'Penghematan: ' . count($deletePermissions) . ' permissions' . PHP_EOL;
echo PHP_EOL;

echo '=== PERINGATAN ===' . PHP_EOL;
echo '⚠️  PASTIKAN UNTUK BACKUP DATABASE SEBELUM MENJALANKAN CLEANUP!' . PHP_EOL;
echo '⚠️  TEST SISTEM SETELAH CLEANUP UNTUK MEMASTIKAN TIDAK ADA YANG RUSAK!' . PHP_EOL;
echo PHP_EOL;

echo 'Apakah Anda ingin melanjutkan dengan cleanup? (y/n): ';
$answer = trim(fgets(STDIN));

if (strtolower($answer) === 'y') {
    echo PHP_EOL . '=== MEMULAI CLEANUP PERMISSION ===' . PHP_EOL;

    $deletedCount = 0;
    foreach ($deletePermissions as $permissionName) {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            // Hapus relasi user-permission terlebih dahulu
            $permission->users()->detach();
            $permission->delete();
            $deletedCount++;
            echo "✓ Menghapus: $permissionName" . PHP_EOL;
        }
    }

    echo PHP_EOL . "=== CLEANUP SELESAI ===" . PHP_EOL;
    echo "Total permission dihapus: $deletedCount" . PHP_EOL;
    echo "Permission tersisa: " . (count($permissions) - $deletedCount) . PHP_EOL;
} else {
    echo PHP_EOL . 'Cleanup dibatalkan.' . PHP_EOL;
}
