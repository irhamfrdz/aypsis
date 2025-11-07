<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Menambahkan Permissions Uang Jalan ke User Admin ===\n\n";

try {
    // 1. Buat permissions baru untuk uang jalan
    $permissions = [
        'uang-jalan-view',
        'uang-jalan-create', 
        'uang-jalan-update',
        'uang-jalan-delete'
    ];

    echo "Membuat permissions uang jalan...\n";
    
    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        if (!$perm) {
            DB::table('permissions')->insert([
                'name' => $permName,
                'description' => 'Permission untuk ' . str_replace('-', ' ', $permName),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Permission '$permName' dibuat\n";
        } else {
            echo "ℹ️ Permission '$permName' sudah ada (ID: {$perm->id})\n";
        }
    }

    // 2. Ambil user admin
    $adminUser = DB::table('users')->where('username', 'admin')->first();

    if (!$adminUser) {
        echo "❌ User 'admin' tidak ditemukan\n";
        exit;
    }

    echo "\n✅ User admin ditemukan: {$adminUser->username} (ID: {$adminUser->id})\n";

    // 3. Berikan semua permissions uang jalan ke user admin
    echo "\nMemberikan permissions ke user admin...\n";
    
    foreach ($permissions as $permName) {
        // Ambil permission ID
        $permission = DB::table('permissions')->where('name', $permName)->first();
        
        if ($permission) {
            // Check if user already has this permission
            $hasPermission = DB::table('user_permissions')
                ->where('permission_id', $permission->id)
                ->where('user_id', $adminUser->id)
                ->exists();

            if (!$hasPermission) {
                DB::table('user_permissions')->insert([
                    'permission_id' => $permission->id,
                    'user_id' => $adminUser->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "✅ Permission '$permName' diberikan ke user admin\n";
            } else {
                echo "ℹ️ User admin sudah memiliki permission '$permName'\n";
            }
        } else {
            echo "❌ Permission '$permName' tidak ditemukan\n";
        }
    }

    // 4. Verifikasi
    echo "\n=== Verifikasi ===\n";
    $userPermissions = DB::table('user_permissions')
        ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->where('permissions.name', 'like', 'uang-jalan%')
        ->pluck('permissions.name');
        
    echo "Permissions uang jalan yang dimiliki user admin:\n";
    foreach ($userPermissions as $permission) {
        echo "   - {$permission}\n";
    }
    
    echo "\n🎉 Semua permissions uang jalan berhasil diberikan ke user admin!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}