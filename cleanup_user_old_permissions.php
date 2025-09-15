<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧹 Pembersihan Permission Lama dari User Permissions\n";
echo "==================================================\n\n";

// Mapping permission lama ke permission baru
$permissionMapping = [
    'master.karyawan.index' => 'master-karyawan.view',
    'master.karyawan.create' => 'master-karyawan.create',
    'master.karyawan.edit' => 'master-karyawan.update',
    'master.karyawan.destroy' => 'master-karyawan.delete',
    'master.user.index' => 'master-user.view',
    'master.user.show' => 'master-user.view',
];

$users = User::with('permissions')->get();
$removedCount = 0;

foreach ($users as $user) {
    echo "👤 Memproses user: {$user->username}\n";

    $oldPermissions = [];
    $newPermissions = [];

    foreach ($user->permissions as $permission) {
        if (isset($permissionMapping[$permission->name])) {
            $oldPermissions[] = $permission->name;
            $newPermissions[] = $permissionMapping[$permission->name];
        }
    }

    if (!empty($oldPermissions)) {
        echo "   Permission lama: " . implode(', ', $oldPermissions) . "\n";
        echo "   Permission baru: " . implode(', ', array_unique($newPermissions)) . "\n";

        // Hapus permission lama dari user
        DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->whereIn('permission_id', function($query) use ($oldPermissions) {
                $query->select('id')
                      ->from('permissions')
                      ->whereIn('name', $oldPermissions);
            })
            ->delete();

        // Pastikan user memiliki permission baru
        foreach (array_unique($newPermissions) as $newPerm) {
            $permId = DB::table('permissions')->where('name', $newPerm)->value('id');
            if ($permId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => $user->id, 'permission_id' => $permId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $removedCount += count($oldPermissions);
        echo "   ✅ Permission lama dihapus, permission baru ditambahkan\n";
    } else {
        echo "   ✅ Tidak ada permission lama\n";
    }
    echo "\n";
}

// Sekarang hapus permission lama yang tidak digunakan lagi
echo "🗑️  Menghapus permission lama yang tidak terpakai:\n";

$oldPermissionsToDelete = array_keys($permissionMapping);
foreach ($oldPermissionsToDelete as $oldPerm) {
    $usageCount = DB::table('user_permissions')
        ->where('permission_id', function($query) use ($oldPerm) {
            $query->select('id')->from('permissions')->where('name', $oldPerm);
        })
        ->count();

    if ($usageCount == 0) {
        $permId = DB::table('permissions')->where('name', $oldPerm)->value('id');
        if ($permId) {
            DB::table('permissions')->where('id', $permId)->delete();
            echo "   ✅ DELETED: {$oldPerm} (ID: {$permId})\n";
        }
    } else {
        echo "   ⚠️  SKIP: {$oldPerm} - Masih digunakan oleh {$usageCount} user\n";
    }
}

echo "\n📊 Ringkasan:\n";
echo "   - Permission lama dihapus dari users: {$removedCount}\n";
echo "   - Total permissions tersisa: " . DB::table('permissions')->count() . "\n";

// Verifikasi akhir
$remainingOldPermissions = DB::table('permissions')
    ->where('name', 'like', 'master.%')
    ->where('name', 'not like', 'master-%')
    ->count();

if ($remainingOldPermissions == 0) {
    echo "✅ Semua permission sudah menggunakan format baru (dash)\n";
} else {
    echo "⚠️  Masih ada {$remainingOldPermissions} permission dengan format lama\n";
}

echo "\n🎉 Pembersihan user permissions selesai!\n";
