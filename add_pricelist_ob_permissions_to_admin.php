<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Menambahkan permissions untuk Master Pricelist OB ke Admin...\n";

try {
    // Permissions yang perlu ditambahkan
    $permissions = [
        'master-pricelist-ob-view',
        'master-pricelist-ob-create', 
        'master-pricelist-ob-update',
        'master-pricelist-ob-delete'
    ];

    $inserted = 0;
    foreach ($permissions as $permission) {
        // Cek apakah permission sudah ada
        $exists = DB::table('permissions')->where('name', $permission)->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'description' => 'Permission untuk ' . str_replace('master-pricelist-ob-', '', $permission) . ' master pricelist OB',
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

    // Assign permissions ke admin user
    echo "\nMengassign permissions ke admin user...\n";

    // Cari user admin berdasarkan username atau role
    $adminUsers = DB::table('users')->where('username', 'admin')->get();
    
    if ($adminUsers->isEmpty()) {
        // Jika tidak ada user dengan username admin, cari user dengan ID 1
        $adminUsers = DB::table('users')->where('id', 1)->get();
    }

    if ($adminUsers->isEmpty()) {
        // Jika masih tidak ada, ambil user pertama
        $adminUsers = DB::table('users')->take(1)->get();
    }

    $assigned = 0;
    foreach ($adminUsers as $adminUser) {
        foreach ($permissions as $permission) {
            // Get permission ID
            $permissionRecord = DB::table('permissions')->where('name', $permission)->first();
            if (!$permissionRecord) {
                echo "! Permission '$permission' tidak ditemukan dalam database\n";
                continue;
            }

            // Cek apakah user sudah memiliki permission
            $hasPermission = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permissionRecord->id)
                ->exists();

            if (!$hasPermission) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permissionRecord->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "✓ Permission '$permission' berhasil diassign ke user {$adminUser->username} (ID: {$adminUser->id})\n";
                $assigned++;
            } else {
                echo "- User {$adminUser->username} sudah memiliki permission '$permission', dilewati\n";
            }
        }
    }

    echo "\nTotal permissions yang diassign: $assigned\n";
    echo "\n=== SELESAI ===\n";
    echo "Permissions Master Pricelist OB berhasil ditambahkan dan diassign ke admin!\n";

    // Menampilkan summary permissions yang tersedia
    echo "\nPermissions Master Pricelist OB yang tersedia:\n";
    foreach ($permissions as $permission) {
        echo "- $permission\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}