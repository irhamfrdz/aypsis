<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Updating Master Tujuan Kegiatan Utama permissions to use Master Tujuan permissions...\n";

    // Remove old tujuan-kegiatan-utama permissions from admin user
    $oldPermissions = [
        'master-tujuan-kegiatan-utama-view',
        'master-tujuan-kegiatan-utama-create',
        'master-tujuan-kegiatan-utama-update',
        'master-tujuan-kegiatan-utama-delete',
        'master-tujuan-kegiatan-utama-export',
        'master-tujuan-kegiatan-utama-print',
        'master-tujuan-kegiatan-utama.view',
        'master-tujuan-kegiatan-utama.create',
        'master-tujuan-kegiatan-utama.update',
        'master-tujuan-kegiatan-utama.delete',
        'master-tujuan-kegiatan-utama.export',
        'master-tujuan-kegiatan-utama.print'
    ];

    foreach ($oldPermissions as $permissionName) {
        // Find permission
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        if ($permission) {
            // Remove from admin user
            DB::table('user_permissions')
                ->where('user_id', 1)
                ->where('permission_id', $permission->id)
                ->delete();
            echo "Removed old permission: {$permissionName}\n";
        }
    }

    // Ensure admin has master-tujuan permissions
    $tujuanPermissions = [
        'master-tujuan-view',
        'master-tujuan-create',
        'master-tujuan-update',
        'master-tujuan-delete',
        'master-tujuan-export',
        'master-tujuan-print'
    ];

    foreach ($tujuanPermissions as $permissionName) {
        // Find or create permission
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        if (!$permission) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Created permission: {$permissionName}\n";
        } else {
            $permissionId = $permission->id;
            echo "Permission exists: {$permissionName}\n";
        }

        // Check if admin already has this permission
        $exists = DB::table('user_permissions')
            ->where('user_id', 1)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$exists) {
            DB::table('user_permissions')->insert([
                'user_id' => 1,
                'permission_id' => $permissionId
            ]);
            echo "Assigned to admin: {$permissionName}\n";
        } else {
            echo "Admin already has: {$permissionName}\n";
        }
    }

    // Clean up old permissions from permissions table (optional)
    foreach ($oldPermissions as $permissionName) {
        DB::table('permissions')->where('name', $permissionName)->delete();
        echo "Deleted old permission from table: {$permissionName}\n";
    }

    echo "\n✅ Master Tujuan Kegiatan Utama now uses the same permissions as Master Tujuan!\n";
    echo "Admin user permissions updated successfully.\n";

    // Show current admin permissions related to tujuan
    echo "\nCurrent admin permissions related to 'tujuan':\n";
    $adminPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', 1)
        ->where('permissions.name', 'like', '%tujuan%')
        ->pluck('permissions.name')
        ->toArray();

    foreach ($adminPermissions as $perm) {
        echo "- {$perm}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
