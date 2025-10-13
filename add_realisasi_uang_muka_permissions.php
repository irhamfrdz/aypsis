<?php

/**
 * Script untuk menambahkan permissions untuk Realisasi Uang Muka
 * File: add_realisasi_uang_muka_permissions.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "=== Menambahkan Permissions untuk Realisasi Uang Muka ===\n\n";

    $permissions = [
        [
            'name' => 'realisasi-uang-muka-view',
            'display_name' => 'View Realisasi Uang Muka',
            'description' => 'Dapat melihat daftar dan detail realisasi uang muka',
            'category' => 'Realisasi Uang Muka'
        ],
        [
            'name' => 'realisasi-uang-muka-create',
            'display_name' => 'Create Realisasi Uang Muka',
            'description' => 'Dapat membuat realisasi uang muka baru',
            'category' => 'Realisasi Uang Muka'
        ],
        [
            'name' => 'realisasi-uang-muka-edit',
            'display_name' => 'Edit Realisasi Uang Muka',
            'description' => 'Dapat mengedit dan menyetujui realisasi uang muka',
            'category' => 'Realisasi Uang Muka'
        ],
        [
            'name' => 'realisasi-uang-muka-delete',
            'display_name' => 'Delete Realisasi Uang Muka',
            'description' => 'Dapat menghapus realisasi uang muka',
            'category' => 'Realisasi Uang Muka'
        ]
    ];

    $addedPermissions = [];

    foreach ($permissions as $permissionData) {
        // Check if permission already exists
        $existingPermission = Permission::where('name', $permissionData['name'])->first();

        if (!$existingPermission) {
            $permission = Permission::create($permissionData);
            $addedPermissions[] = $permission;
            echo "✓ Permission '{$permissionData['name']}' berhasil ditambahkan\n";
        } else {
            echo "- Permission '{$permissionData['name']}' sudah ada\n";
        }
    }

    // Assign permissions to admin users
    echo "\n=== Assign Permissions ke User Admin ===\n";

    $adminUsers = User::where('role', 'admin')
                     ->orWhere('username', 'admin')
                     ->orWhere('username', 'like', '%admin%')
                     ->get();

    if ($adminUsers->count() > 0) {
        foreach ($adminUsers as $admin) {
            $assignedCount = 0;
            foreach ($addedPermissions as $permission) {
                // Check if user already has permission
                $existingPermission = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->where('permission_id', $permission->id)
                    ->first();

                if (!$existingPermission) {
                    // Assign permission
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $assignedCount++;
                }
            }

            if ($assignedCount > 0) {
                echo "✓ {$assignedCount} permissions berhasil di-assign ke admin: {$admin->username}\n";
            } else {
                echo "- Admin {$admin->username} sudah memiliki semua permissions\n";
            }
        }
    } else {
        echo "! Tidak ada user admin yang ditemukan\n";
        echo "  Silakan assign permissions manual ke user yang membutuhkan\n";
    }

    // Summary
    echo "\n=== Summary ===\n";
    echo "Total permissions ditambahkan: " . count($addedPermissions) . "\n";
    echo "Admin users yang di-update: " . $adminUsers->count() . "\n";

    DB::commit();
    echo "\n✅ Semua permissions untuk Realisasi Uang Muka berhasil ditambahkan!\n";

} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
