<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Get admin role ID
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if (!$adminRole) {
        echo "Role admin tidak ditemukan!\n";
        exit(1);
    }

    // Define OB Bongkar permissions
    $permissions = [
        'ob-bongkar-view',
        'ob-bongkar-create',
        'ob-bongkar-edit',
        'ob-bongkar-delete',
    ];

    $addedCount = 0;
    $existingCount = 0;

    foreach ($permissions as $permissionName) {
        // Check if permission exists
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if (!$permission) {
            // Create permission if not exists
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Permission '$permissionName' berhasil dibuat (ID: $permissionId)\n";
        } else {
            $permissionId = $permission->id;
            echo "✓ Permission '$permissionName' sudah ada (ID: $permissionId)\n";
        }

        // Check if role already has this permission
        $roleHasPermission = DB::table('permission_role')
            ->where('role_id', $adminRole->id)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$roleHasPermission) {
            // Add permission to admin role
            DB::table('permission_role')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ]);
            echo "  → Ditambahkan ke role admin\n";
            $addedCount++;
        } else {
            echo "  → Sudah ada di role admin\n";
            $existingCount++;
        }
    }

    echo "\n";
    echo "===========================================\n";
    echo "RINGKASAN:\n";
    echo "===========================================\n";
    echo "Total permissions: " . count($permissions) . "\n";
    echo "Ditambahkan ke admin: $addedCount\n";
    echo "Sudah ada sebelumnya: $existingCount\n";
    echo "===========================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
