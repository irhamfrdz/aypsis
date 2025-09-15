<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '=== BACKUP PERMISSION SEBELUM CLEANUP ===' . PHP_EOL;

// Backup semua permission dan relasi user-permission
$allPermissions = Permission::with('users')->get();
$backupFile = 'permission_backup_' . date('Y-m-d_H-i-s') . '.json';

$backupData = [];
foreach ($allPermissions as $permission) {
    $backupData[] = [
        'id' => $permission->id,
        'name' => $permission->name,
        'users' => $permission->users->pluck('id')->toArray(),
        'created_at' => $permission->created_at,
        'updated_at' => $permission->updated_at,
    ];
}

file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));
echo "✓ Backup disimpan ke: $backupFile" . PHP_EOL . PHP_EOL;

// Permission yang HARUS DIPERTAHANKAN
$keepPermissions = [
    'master-karyawan',
    'master-user',
    'master-kontainer',
    'master-pricelist-sewa-kontainer',
    'master-tujuan',
    'master-kegiatan',
    'master-permission',
    'master-mobil',
    'dashboard',
    'tagihan-kontainer',
    'pranota-supir',
    'pembayaran-pranota-supir',
    'permohonan',
    'user-approval',
    'login',
    'logout',
    'admin.features',
    'storage.local',
    'admin.debug.perms',
    'admin.user-approval.index',
    'admin.user-approval.show',
    'admin.user-approval.approve',
    'admin.user-approval.reject',
    'profile.show',
    'profile.edit',
    'profile.update.account',
    'profile.update.personal',
    'profile.update.avatar',
    'profile.destroy',
    'supir.dashboard',
    'supir.checkpoint.create',
    'supir.checkpoint.store',
    'approval.dashboard',
    'approval.mass_process',
    'approval.create',
    'approval.store',
    'approval.riwayat',
];

echo '=== MEMULAI CLEANUP PERMISSION ===' . PHP_EOL;

// Cari permission yang akan dihapus
$permissionsToDelete = Permission::whereNotIn('name', $keepPermissions)->get();

echo 'Permissions yang akan dihapus: ' . $permissionsToDelete->count() . PHP_EOL;
echo 'Permissions yang dipertahankan: ' . count($keepPermissions) . PHP_EOL . PHP_EOL;

$deletedCount = 0;
$errors = [];

foreach ($permissionsToDelete as $permission) {
    try {
        // Cek apakah permission masih digunakan oleh user
        $userCount = $permission->users()->count();

        if ($userCount > 0) {
            echo "⚠ Melewati: {$permission->name} (masih digunakan oleh $userCount user)" . PHP_EOL;
            continue;
        }

        // Hapus permission
        $permission->delete();
        $deletedCount++;
        echo "✓ Menghapus: {$permission->name}" . PHP_EOL;

    } catch (Exception $e) {
        $errors[] = "Error menghapus {$permission->name}: " . $e->getMessage();
        echo "✗ Error: {$permission->name} - " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . '=== CLEANUP SELESAI ===' . PHP_EOL;
echo "Total permission dihapus: $deletedCount" . PHP_EOL;
echo "Total error: " . count($errors) . PHP_EOL;

if (!empty($errors)) {
    echo PHP_EOL . '=== ERRORS ===' . PHP_EOL;
    foreach ($errors as $error) {
        echo "✗ $error" . PHP_EOL;
    }
}

echo PHP_EOL . '=== VERIFIKASI AKHIR ===' . PHP_EOL;
$remainingPermissions = Permission::count();
echo "Permission tersisa: $remainingPermissions" . PHP_EOL;
echo "Target: " . count($keepPermissions) . " permissions" . PHP_EOL;

if ($remainingPermissions === count($keepPermissions)) {
    echo "✅ Cleanup berhasil! Jumlah permission sesuai target." . PHP_EOL;
} else {
    echo "⚠ Jumlah permission tidak sesuai. Periksa manual." . PHP_EOL;
}

echo PHP_EOL . '=== PERMISSION YANG DIPERTAHANKAN ===' . PHP_EOL;
$finalPermissions = Permission::orderBy('name')->pluck('name')->toArray();
foreach ($finalPermissions as $perm) {
    echo "✓ $perm" . PHP_EOL;
}

echo PHP_EOL . '=== PENTING ===' . PHP_EOL;
echo '1. Backup tersimpan di: ' . $backupFile . PHP_EOL;
echo '2. Test sistem untuk memastikan tidak ada yang rusak' . PHP_EOL;
echo '3. Jika ada masalah, restore dari backup dengan mengimpor file JSON' . PHP_EOL;
