<?php

/**
 * Script untuk menambahkan permission Checkpoint Kontainer Keluar
 * 
 * Jalankan dengan: php add_checkpoint_kontainer_keluar_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "=== Menambahkan Permission Checkpoint Kontainer Keluar ===\n\n";

try {
    DB::beginTransaction();
    
    // Definisi permission untuk Checkpoint Kontainer Keluar
    $permissions = [
        [
            'name' => 'checkpoint-kontainer-keluar-view',
            'description' => 'Melihat Checkpoint Kontainer Keluar'
        ],
        [
            'name' => 'checkpoint-kontainer-keluar-create',
            'description' => 'Memproses Checkpoint Kontainer Keluar'
        ],
        [
            'name' => 'checkpoint-kontainer-keluar-delete',
            'description' => 'Membatalkan Checkpoint Kontainer Keluar'
        ],
    ];
    
    $createdCount = 0;
    $skippedCount = 0;
    
    foreach ($permissions as $perm) {
        $existing = Permission::where('name', $perm['name'])->first();
        
        if ($existing) {
            echo "⏭️  Permission '{$perm['name']}' sudah ada, dilewati.\n";
            $skippedCount++;
            continue;
        }
        
        Permission::create([
            'name' => $perm['name'],
            'description' => $perm['description'],
            'guard_name' => 'web'
        ]);
        
        echo "✅ Permission '{$perm['name']}' berhasil ditambahkan.\n";
        $createdCount++;
    }
    
    // Assign ke role admin
    $adminRole = Role::where('name', 'admin')->first();
    
    if ($adminRole) {
        echo "\n--- Menambahkan permission ke role Admin ---\n";
        
        foreach ($permissions as $perm) {
            $permission = Permission::where('name', $perm['name'])->first();
            
            if ($permission) {
                // Cek apakah relasi sudah ada
                $exists = DB::table('permission_role')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                    
                if (!$exists) {
                    $adminRole->permissions()->attach($permission->id);
                    echo "✅ Permission '{$perm['name']}' ditambahkan ke role Admin.\n";
                } else {
                    echo "⏭️  Permission '{$perm['name']}' sudah ada di role Admin.\n";
                }
            }
        }
    }
    
    // Assign ke role operational jika ada
    $operationalRole = Role::where('name', 'operational')->first();
    
    if ($operationalRole) {
        echo "\n--- Menambahkan permission ke role Operational ---\n";
        
        foreach ($permissions as $perm) {
            $permission = Permission::where('name', $perm['name'])->first();
            
            if ($permission) {
                // Cek apakah relasi sudah ada
                $exists = DB::table('permission_role')
                    ->where('role_id', $operationalRole->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                    
                if (!$exists) {
                    $operationalRole->permissions()->attach($permission->id);
                    echo "✅ Permission '{$perm['name']}' ditambahkan ke role Operational.\n";
                } else {
                    echo "⏭️  Permission '{$perm['name']}' sudah ada di role Operational.\n";
                }
            }
        }
    }
    
    DB::commit();
    
    echo "\n=== Selesai ===\n";
    echo "Permission dibuat: {$createdCount}\n";
    echo "Permission dilewati (sudah ada): {$skippedCount}\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
