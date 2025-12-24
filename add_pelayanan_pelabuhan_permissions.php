<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    DB::beginTransaction();
    
    $now = Carbon::now();
    
    // Permissions untuk Master Pelayanan Pelabuhan
    $permissions = [
        [
            'name' => 'master-pelayanan-pelabuhan-view',
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-create',
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-edit',
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-delete',
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ];
    
    echo "Menambahkan permissions untuk Master Pelayanan Pelabuhan...\n";
    
    foreach ($permissions as $permission) {
        // Cek apakah permission sudah ada
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();
        
        if ($exists) {
            echo "⚠ Permission '{$permission['name']}' sudah ada, skip.\n";
            continue;
        }
        
        // Insert permission
        $permissionId = DB::table('permissions')->insertGetId($permission);
        echo "✓ Permission '{$permission['name']}' berhasil ditambahkan (ID: {$permissionId})\n";
    }
    
    // Assign permissions ke role admin
    echo "\nMengassign permissions ke role admin...\n";
    
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if ($adminRole) {
        foreach ($permissions as $permission) {
            $permissionRecord = DB::table('permissions')
                ->where('name', $permission['name'])
                ->first();
            
            if ($permissionRecord) {
                // Cek apakah role_has_permissions sudah ada
                $roleHasPermission = DB::table('role_has_permissions')
                    ->where('permission_id', $permissionRecord->id)
                    ->where('role_id', $adminRole->id)
                    ->exists();
                
                if (!$roleHasPermission) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permissionRecord->id,
                        'role_id' => $adminRole->id,
                    ]);
                    echo "✓ Permission '{$permission['name']}' di-assign ke role admin\n";
                } else {
                    echo "⚠ Permission '{$permission['name']}' sudah di-assign ke role admin\n";
                }
            }
        }
    } else {
        echo "⚠ Role admin tidak ditemukan!\n";
    }
    
    DB::commit();
    
    echo "\n✓ Semua permissions berhasil ditambahkan dan di-assign ke role admin!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
