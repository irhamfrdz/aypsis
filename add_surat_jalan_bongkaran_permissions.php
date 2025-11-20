<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

echo "=== Menambahkan Permission Surat Jalan Bongkaran ke Admin ===\n\n";

try {
    // Daftar permissions untuk Surat Jalan Bongkaran
    $permissions = [
        'surat-jalan-bongkaran-view',
        'surat-jalan-bongkaran-create',
        'surat-jalan-bongkaran-edit',
        'surat-jalan-bongkaran-delete',
    ];

    $createdPermissions = [];
    
    // Buat atau ambil permissions
    foreach ($permissions as $permissionName) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName],
            ['guard_name' => 'web']
        );
        $createdPermissions[] = $permission;
        echo "✓ Permission '{$permissionName}' tersedia\n";
    }

    echo "\n";

    // Ambil role admin
    $adminRole = Role::where('name', 'admin')->first();
    
    if (!$adminRole) {
        echo "✗ Role 'admin' tidak ditemukan!\n";
        echo "  Mencoba mencari role Administrator...\n";
        $adminRole = Role::where('name', 'Administrator')->first();
    }

    if ($adminRole) {
        echo "✓ Role admin ditemukan: {$adminRole->name}\n\n";
        
        // Assign permissions ke role admin
        foreach ($createdPermissions as $permission) {
            if (!$adminRole->permissions()->where('permission_id', $permission->id)->exists()) {
                $adminRole->permissions()->attach($permission->id);
                echo "✓ Permission '{$permission->name}' ditambahkan ke role {$adminRole->name}\n";
            } else {
                echo "  Permission '{$permission->name}' sudah ada di role {$adminRole->name}\n";
            }
        }
    } else {
        echo "✗ Role admin tidak ditemukan!\n";
    }

    echo "\n";

    // Assign permissions langsung ke semua user admin
    $adminUsers = User::whereHas('roles', function($query) {
        $query->whereIn('name', ['admin', 'Administrator']);
    })->get();

    if ($adminUsers->count() > 0) {
        echo "Menambahkan permissions ke " . $adminUsers->count() . " user admin:\n";
        
        foreach ($adminUsers as $user) {
            foreach ($createdPermissions as $permission) {
                if (!$user->permissions()->where('permission_id', $permission->id)->exists()) {
                    $user->permissions()->attach($permission->id);
                    echo "✓ Permission '{$permission->name}' ditambahkan ke user: {$user->username}\n";
                } else {
                    echo "  Permission '{$permission->name}' sudah ada untuk user: {$user->username}\n";
                }
            }
        }
    } else {
        echo "⚠ Tidak ada user dengan role admin ditemukan\n";
        echo "  Mencoba menambahkan ke user dengan ID 1...\n\n";
        
        $user = User::find(1);
        if ($user) {
            foreach ($createdPermissions as $permission) {
                if (!$user->permissions()->where('permission_id', $permission->id)->exists()) {
                    $user->permissions()->attach($permission->id);
                    echo "✓ Permission '{$permission->name}' ditambahkan ke user: {$user->username}\n";
                } else {
                    echo "  Permission '{$permission->name}' sudah ada untuk user: {$user->username}\n";
                }
            }
        }
    }

    echo "\n=== Selesai ===\n";
    echo "✓ Semua permission Surat Jalan Bongkaran berhasil ditambahkan!\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
