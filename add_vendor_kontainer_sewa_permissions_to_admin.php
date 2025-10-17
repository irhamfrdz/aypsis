<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inisialisasi Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Permissions yang akan dibuat
    $permissions = [
        'vendor-kontainer-sewa-view',
        'vendor-kontainer-sewa-create', 
        'vendor-kontainer-sewa-edit',
        'vendor-kontainer-sewa-delete'
    ];

    echo "Menambahkan permissions vendor kontainer sewa...\n";

    // Insert permissions
    foreach ($permissions as $permission) {
        $exists = DB::table('permissions')->where('name', $permission)->exists();
        
        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Permission '$permission' berhasil ditambahkan.\n";
        } else {
            echo "Permission '$permission' sudah ada.\n";
        }
    }

    // Cari user admin
    $adminUser = DB::table('users')->where('username', 'admin')->first();
    
    if (!$adminUser) {
        echo "User admin tidak ditemukan!\n";
        exit(1);
    }

    echo "User admin ditemukan: {$adminUser->username}\n";

    // Assign permissions ke admin
    foreach ($permissions as $permission) {
        $permissionRecord = DB::table('permissions')->where('name', $permission)->first();
        
        if ($permissionRecord) {
            $exists = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permissionRecord->id)
                ->exists();
                
            if (!$exists) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permissionRecord->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "Permission '$permission' berhasil di-assign ke admin.\n";
            } else {
                echo "Permission '$permission' sudah di-assign ke admin.\n";
            }
        }
    }

    echo "\nSetup permissions vendor kontainer sewa selesai!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}