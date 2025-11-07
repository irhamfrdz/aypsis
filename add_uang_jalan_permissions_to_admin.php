<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Script untuk menambahkan permissions uang jalan ke user admin

try {
    DB::beginTransaction();
    
    // 1. Buat permissions baru untuk uang jalan
    $permissions = [
        [
            'name' => 'uang-jalan-view',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'uang-jalan-create',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'uang-jalan-update',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'uang-jalan-delete',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->where('guard_name', $permission['guard_name'])
            ->exists();
        
        if (!$exists) {
            DB::table('permissions')->insert($permission);
            echo "✅ Permission '{$permission['name']}' berhasil dibuat\n";
        } else {
            echo "ℹ️ Permission '{$permission['name']}' sudah ada\n";
        }
    }

    // 2. Ambil user admin berdasarkan username
    $adminUser = DB::table('users')
        ->where('username', 'admin')
        ->first();

    if (!$adminUser) {
        echo "❌ User 'admin' tidak ditemukan\n";
        DB::rollBack();
        return;
    }

    echo "✅ User admin ditemukan: {$adminUser->username} (ID: {$adminUser->id})\n";

    // 3. Berikan semua permissions uang jalan ke user admin
    $permissionNames = ['uang-jalan-view', 'uang-jalan-create', 'uang-jalan-update', 'uang-jalan-delete'];
    
    foreach ($permissionNames as $permissionName) {
        // Ambil permission ID
        $permission = DB::table('permissions')
            ->where('name', $permissionName)
            ->where('guard_name', 'web')
            ->first();

        if ($permission) {
            // Check if user already has this permission
            $hasPermission = DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->where('model_type', 'App\\Models\\User')
                ->where('model_id', $adminUser->id)
                ->exists();

            if (!$hasPermission) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $adminUser->id
                ]);
                echo "✅ Permission '{$permissionName}' diberikan ke user admin\n";
            } else {
                echo "ℹ️ User admin sudah memiliki permission '{$permissionName}'\n";
            }
        } else {
            echo "❌ Permission '{$permissionName}' tidak ditemukan\n";
        }
    }

    // 4. Bersihkan cache permissions (jika menggunakan Spatie Permission)
    if (class_exists('Spatie\\Permission\\PermissionRegistrar')) {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        echo "✅ Permission cache dibersihkan\n";
    }

    DB::commit();
    echo "\n🎉 Semua permissions uang jalan berhasil diberikan ke user admin!\n";
    echo "\nPermissions yang diberikan:\n";
    foreach ($permissionNames as $permission) {
        echo "   - {$permission}\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}