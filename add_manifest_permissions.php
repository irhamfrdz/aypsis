<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $permissions = [
        [
            'name' => 'manifest-view',
            'description' => 'Akses Melihat Manifest',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'manifest-create',
            'description' => 'Akses Membuat Manifest',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'manifest-edit',
            'description' => 'Akses Edit Manifest',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'manifest-delete',
            'description' => 'Akses Hapus Manifest',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];

    foreach ($permissions as $permission) {
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();

        if (!$exists) {
            DB::table('permissions')->insert($permission);
            echo "✓ Permission '{$permission['name']}' berhasil ditambahkan\n";
        } else {
            echo "- Permission '{$permission['name']}' sudah ada\n";
        }
    }

    // Tambahkan permissions ke role admin
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if ($adminRole) {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['manifest-view', 'manifest-create', 'manifest-edit', 'manifest-delete'])
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            $exists = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                DB::table('permission_role')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $adminRole->id,
                ]);
            }
        }
        echo "\n✓ Permissions berhasil ditambahkan ke role admin\n";
    }

    echo "\n=== SELESAI ===\n";
    echo "Total 4 permissions manifest telah ditambahkan:\n";
    echo "1. manifest-view\n";
    echo "2. manifest-create\n";
    echo "3. manifest-edit\n";
    echo "4. manifest-delete\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
