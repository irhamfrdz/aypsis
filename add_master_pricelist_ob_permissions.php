<?php

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    // Data permissions untuk Master Pricelist OB
    $permissions = [
        'master-pricelist-ob-view',
        'master-pricelist-ob-create',
        'master-pricelist-ob-update',
        'master-pricelist-ob-delete'
    ];

    foreach ($permissions as $permissionName) {
        // Cek apakah permission sudah ada
        $exists = DB::table('permissions')
                    ->where('name', $permissionName)
                    ->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Permission '{$permissionName}' berhasil ditambahkan.\n";
        } else {
            echo "- Permission '{$permissionName}' sudah ada.\n";
        }
    }

    // Tambahkan permissions ke role admin (id = 1)
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if ($adminRole) {
        $permissionIds = DB::table('permissions')
                          ->whereIn('name', $permissions)
                          ->pluck('id')
                          ->toArray();

        foreach ($permissionIds as $permissionId) {
            // Cek apakah role_has_permissions sudah ada
            $roleHasPermission = DB::table('role_has_permissions')
                                  ->where('role_id', $adminRole->id)
                                  ->where('permission_id', $permissionId)
                                  ->exists();

            if (!$roleHasPermission) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId
                ]);
                
                $permissionName = DB::table('permissions')->where('id', $permissionId)->value('name');
                echo "✓ Permission '{$permissionName}' berhasil ditambahkan ke role admin.\n";
            }
        }
    }

    DB::commit();
    echo "\n🎉 Semua permissions Master Pricelist OB berhasil ditambahkan!\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
}