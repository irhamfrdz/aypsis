<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Permissions untuk Pricelist Uang Jalan Batam
    $permissions = [
        'master-pricelist-uang-jalan-batam-view',
        'master-pricelist-uang-jalan-batam-create',
        'master-pricelist-uang-jalan-batam-edit',
        'master-pricelist-uang-jalan-batam-delete',
    ];

    echo "=== Menambahkan Permissions Pricelist Uang Jalan Batam ===\n\n";

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')->where('name', $permission)->first();
        
        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Permission '$permission' berhasil ditambahkan\n";
        } else {
            echo "⚠ Permission '$permission' sudah ada\n";
        }
    }

    echo "\n=== Menambahkan Permissions ke User Admin ===\n\n";

    // Get admin user
    $admin = DB::table('users')->where('username', 'admin')->first();

    if ($admin) {
        foreach ($permissions as $permission) {
            $perm = DB::table('permissions')->where('name', $permission)->first();
            
            if ($perm) {
                // Check if user already has this permission
                $hasPermission = DB::table('user_permissions')
                    ->where('permission_id', $perm->id)
                    ->where('user_id', $admin->id)
                    ->first();

                if (!$hasPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $perm->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    echo "✓ Permission '$permission' berhasil ditambahkan ke admin\n";
                } else {
                    echo "⚠ Admin sudah memiliki permission '$permission'\n";
                }
            }
        }
        
        echo "\n✓ Semua permissions berhasil ditambahkan ke user admin\n";
    } else {
        echo "✗ User admin tidak ditemukan!\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Selesai ===\n";
