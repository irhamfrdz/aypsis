<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧹 Pembersihan Permission Duplikat\n";
echo "=================================\n\n";

// Permission yang akan dihapus (format lama dengan titik)
$permissionsToDelete = [
    // Master Karyawan - format lama
    'master.karyawan.print',
    'master.karyawan.print.single',
    'master.karyawan.import',
    'master.karyawan.import.store',
    'master.karyawan.export',
    'master.karyawan.index',
    'master.karyawan.create',
    'master.karyawan.store',
    'master.karyawan.show',
    'master.karyawan.edit',
    'master.karyawan.update',
    'master.karyawan.destroy',

    // Master User - format lama
    'master.user.index',
    'master.user.create',
    'master.user.store',
    'master.user.show',
    'master.user.edit',
    'master.user.update',
    'master.user.destroy',

    // Master Kontainer - format lama
    'master.kontainer.index',
    'master.kontainer.create',
    'master.kontainer.store',
    'master.kontainer.show',
    'master.kontainer.edit',
    'master.kontainer.update',
    'master.kontainer.destroy',

    // Master Tujuan - format lama
    'master.tujuan.index',
    'master.tujuan.create',
    'master.tujuan.store',
    'master.tujuan.show',
    'master.tujuan.edit',
    'master.tujuan.update',
    'master.tujuan.destroy',

    // Master Kegiatan - format lama
    'master.kegiatan.index',
    'master.kegiatan.create',
    'master.kegiatan.store',
    'master.kegiatan.show',
    'master.kegiatan.edit',
    'master.kegiatan.update',
    'master.kegiatan.destroy',
    'master.kegiatan.template',
    'master.kegiatan.import',

    // Master Permission - format lama
    'master.permission.index',
    'master.permission.create',
    'master.permission.store',
    'master.permission.show',
    'master.permission.edit',
    'master.permission.update',
    'master.permission.destroy',

    // Master Mobil - format lama
    'master.mobil.index',
    'master.mobil.create',
    'master.mobil.store',
    'master.mobil.show',
    'master.mobil.edit',
    'master.mobil.update',
    'master.mobil.destroy',

    // Master Pricelist - format lama
    'master.pricelist-sewa-kontainer.index',
    'master.pricelist-sewa-kontainer.create',
    'master.pricelist-sewa-kontainer.store',
    'master.pricelist-sewa-kontainer.show',
    'master.pricelist-sewa-kontainer.edit',
    'master.pricelist-sewa-kontainer.update',
    'master.pricelist-sewa-kontainer.destroy',

    // Permission tunggal lama yang sudah tidak digunakan
    'master-karyawan', // ID: 1 - sudah digantikan dengan permission detail
    'master-user',     // ID: 2
    'master-kontainer', // ID: 3
    'master-tujuan',   // ID: 9
    'master-kegiatan', // ID: 10
    'master-permission', // ID: 11
    'master-mobil',    // ID: 12
    'master-pricelist-sewa-kontainer', // ID: 13
];

echo "Permission yang akan dihapus:\n";
$deletedCount = 0;

// Transfer permission lama ke permission baru untuk user yang masih menggunakannya
echo "\n🔄 Transfer permission lama ke permission baru:\n";

$permissionMapping = [
    // Master Karyawan
    'master.karyawan.index' => 'master-karyawan.view',
    'master.karyawan.create' => 'master-karyawan.create',
    'master.karyawan.edit' => 'master-karyawan.update',
    'master.karyawan.update' => 'master-karyawan.update',
    'master.karyawan.destroy' => 'master-karyawan.delete',
    'master.karyawan.print' => 'master-karyawan.print',
    'master.karyawan.export' => 'master-karyawan.export',

    // Master User
    'master.user.index' => 'master-user.view',
    'master.user.create' => 'master-user.create',
    'master.user.show' => 'master-user.view',
    'master.user.edit' => 'master-user.update',
    'master.user.update' => 'master-user.update',
    'master.user.destroy' => 'master-user.delete',
];

foreach ($permissionMapping as $oldPerm => $newPerm) {
    $oldPermission = Permission::where('name', $oldPerm)->first();
    $newPermission = Permission::where('name', $newPerm)->first();

    if ($oldPermission && $newPermission) {
        $users = $oldPermission->users;
        foreach ($users as $user) {
            if (!$user->permissions->contains('name', $newPerm)) {
                $user->permissions()->attach($newPermission->id);
                echo "  ✅ Transfer: {$user->username} - {$oldPerm} → {$newPerm}\n";
            }
        }
    }
}

echo "\n🗑️  Menghapus permission lama:\n";

foreach ($permissionsToDelete as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        // Cek apakah permission ini masih digunakan oleh user
        $userCount = $permission->users()->count();
        if ($userCount > 0) {
            echo "⚠️  SKIP: {$permName} (ID: {$permission->id}) - Masih digunakan oleh {$userCount} user\n";
        } else {
            $permission->delete();
            echo "✅ DELETED: {$permName} (ID: {$permission->id})\n";
            $deletedCount++;
        }
    } else {
        echo "❌ NOT FOUND: {$permName}\n";
    }
}

echo "\n📊 Ringkasan:\n";
echo "  - Permission dihapus: {$deletedCount}\n";
echo "  - Permission dilewati: " . (count($permissionsToDelete) - $deletedCount) . "\n\n";

// Verifikasi permission yang tersisa
echo "Permission master-* yang tersisa:\n";
$remainingMasterPerms = Permission::where('name', 'like', 'master-%')->get();
foreach ($remainingMasterPerms as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}

echo "\n🎉 Pembersihan permission selesai!\n";
echo "Sistem sekarang menggunakan permission yang konsisten dengan format dash.\n";
