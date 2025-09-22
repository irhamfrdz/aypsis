<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "Menambahkan permissions untuk Pricelist CAT...\n";

try {
    // Permissions yang perlu ditambahkan
    $permissions = [
        'master-pricelist-cat-view',
        'master-pricelist-cat-create',
        'master-pricelist-cat-update',
        'master-pricelist-cat-delete'
    ];

    $inserted = 0;
    foreach ($permissions as $permission) {
        // Cek apakah permission sudah ada
        $exists = DB::table('permissions')->where('name', $permission)->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'description' => 'Permission untuk ' . str_replace('master-pricelist-cat-', '', $permission) . ' pricelist CAT',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Permission '$permission' berhasil ditambahkan\n";
            $inserted++;
        } else {
            echo "- Permission '$permission' sudah ada, dilewati\n";
        }
    }

    echo "\nTotal permissions yang ditambahkan: $inserted\n";

    // Assign permissions ke admin user (ID 1 atau user pertama)
    echo "\nMengassign permissions ke admin user...\n";

    $adminUser = DB::table('users')->find(1); // Ambil user dengan ID 1
    if (!$adminUser) {
        // Jika tidak ada ID 1, ambil user pertama
        $adminUser = DB::table('users')->first();
    }

    if ($adminUser) {
        echo "Mengassign permissions ke user: {$adminUser->username} (ID: {$adminUser->id})\n";
        $assigned = 0;
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->where('name', $permission)->value('id');

            if ($permissionId) {
                $exists = DB::table('user_permissions')
                    ->where('user_id', $adminUser->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $adminUser->id,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "✓ Permission '$permission' diassign ke user\n";
                    $assigned++;
                } else {
                    echo "- Permission '$permission' sudah diassign ke user\n";
                }
            }
        }
        echo "\nTotal permissions yang diassign ke user: $assigned\n";
    } else {
        echo "Tidak ada user ditemukan!\n";
    }

    // Clear cache permissions
    echo "\nMembersihkan cache permissions...\n";
    Artisan::call('permission:cache-reset');
    echo "Cache permissions berhasil dibersihkan\n";

    echo "\n✅ Proses selesai! Permissions Pricelist CAT telah berhasil ditambahkan.\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
