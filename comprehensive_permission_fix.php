<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧹 Comprehensive Permission Cleanup for User test4\n";
echo "=================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "👤 Processing user: {$user->username} (ID: {$user->id})\n\n";

// Comprehensive mapping of old to new permission format
$permissionMapping = [
    // Master Data
    'master.karyawan.index' => 'master-karyawan.view',
    'master.karyawan.create' => 'master-karyawan.create',
    'master.karyawan.edit' => 'master-karyawan.update',
    'master.karyawan.destroy' => 'master-karyawan.delete',

    'master.user.index' => 'master-user.view',
    'master.user.create' => 'master-user.create',
    'master.user.edit' => 'master-user.update',
    'master.user.destroy' => 'master-user.delete',

    'master.kontainer.index' => 'master-kontainer.view',
    'master.kontainer.create' => 'master-kontainer.create',
    'master.kontainer.edit' => 'master-kontainer.update',
    'master.kontainer.destroy' => 'master-kontainer.delete',

    'master.tujuan.index' => 'master-tujuan.view',
    'master.tujuan.create' => 'master-tujuan.create',
    'master.tujuan.edit' => 'master-tujuan.update',
    'master.tujuan.destroy' => 'master-tujuan.delete',

    'master.kegiatan.index' => 'master-kegiatan.view',
    'master.kegiatan.create' => 'master-kegiatan.create',
    'master.kegiatan.edit' => 'master-kegiatan.update',
    'master.kegiatan.destroy' => 'master-kegiatan.delete',

    'master.permission.index' => 'master-permission.view',
    'master.permission.create' => 'master-permission.create',
    'master.permission.edit' => 'master-permission.update',
    'master.permission.destroy' => 'master-permission.delete',

    'master.mobil.index' => 'master-mobil.view',
    'master.mobil.create' => 'master-mobil.create',
    'master.mobil.edit' => 'master-mobil.update',
    'master.mobil.destroy' => 'master-mobil.delete',
];

$oldPermissions = [];
$newPermissions = [];

// Get current permissions
$currentPermissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions:\n";
foreach ($currentPermissions as $perm) {
    echo "  - {$perm}\n";
}
echo "\n";

// Process each mapping
foreach ($permissionMapping as $oldPerm => $newPerm) {
    if (in_array($oldPerm, $currentPermissions)) {
        $oldPermissions[] = $oldPerm;
        $newPermissions[] = $newPerm;

        // Remove old permission
        $oldPermission = Permission::where('name', $oldPerm)->first();
        if ($oldPermission) {
            $user->permissions()->detach($oldPermission->id);
            echo "❌ Removed old permission: {$oldPerm}\n";
        } else {
            echo "⚠️  Old permission not found: {$oldPerm}\n";
        }

        // Add new permission (if not already has it)
        if (!$user->hasPermissionTo($newPerm)) {
            $newPermission = Permission::where('name', $newPerm)->first();
            if ($newPermission) {
                $user->permissions()->attach($newPermission->id);
                echo "✅ Added new permission: {$newPerm}\n";
            } else {
                echo "⚠️  New permission not found in database: {$newPerm}\n";
            }
        } else {
            echo "⚠️  Already has new permission: {$newPerm}\n";
        }
    }
}

echo "\n📊 Summary:\n";
echo "  - Old permissions removed: " . count($oldPermissions) . "\n";
echo "  - New permissions added: " . count($newPermissions) . "\n";

if (count($oldPermissions) > 0) {
    echo "\n🔄 Permission conversions:\n";
    for ($i = 0; $i < count($oldPermissions); $i++) {
        echo "  - {$oldPermissions[$i]} → {$newPermissions[$i]}\n";
    }
}

// Verify final permissions
echo "\n✅ Final permissions for user test4:\n";
$user->refresh();
$finalPermissions = $user->permissions->pluck('name')->toArray();
foreach ($finalPermissions as $perm) {
    echo "  - {$perm}\n";
}

echo "\n🎉 Permission cleanup completed for user test4!\n";
echo "Now the Master Data menu should appear in the sidebar.\n";
