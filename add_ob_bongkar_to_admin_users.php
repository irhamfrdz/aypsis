<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "TAMBAH PERMISSION OB BONGKAR KE USER ADMIN\n";
echo "===========================================\n\n";

try {
    // 1. Cek users dengan role admin
    echo "1. Mencari user dengan role 'admin':\n";
    $adminUsers = DB::table('users')
        ->where('role', 'admin')
        ->get();
    
    if ($adminUsers->isEmpty()) {
        echo "❌ Tidak ada user dengan role admin!\n";
        exit(1);
    }
    
    foreach ($adminUsers as $user) {
        echo "  ✓ User ID: {$user->id}, Username: {$user->username}, Role: {$user->role}\n";
    }
    echo "\n";
    
    // 2. Cek permissions OB Bongkar
    echo "2. Permissions OB Bongkar:\n";
    $permissions = [
        'ob-bongkar-view',
        'ob-bongkar-create',
        'ob-bongkar-edit',
        'ob-bongkar-delete',
    ];
    
    $permissionIds = [];
    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        if ($perm) {
            $permissionIds[] = $perm->id;
            echo "  ✓ {$permName} (ID: {$perm->id})\n";
        } else {
            echo "  ❌ {$permName} tidak ditemukan!\n";
        }
    }
    echo "\n";
    
    // 3. Tambahkan permissions ke setiap user admin
    echo "3. Menambahkan permissions ke user admin:\n";
    foreach ($adminUsers as $user) {
        echo "  User: {$user->username} (ID: {$user->id})\n";
        
        foreach ($permissionIds as $permId) {
            $permName = DB::table('permissions')->where('id', $permId)->value('name');
            
            // Cek apakah sudah ada
            $exists = DB::table('permission_user')
                ->where('user_id', $user->id)
                ->where('permission_id', $permId)
                ->exists();
            
            if (!$exists) {
                DB::table('permission_user')->insert([
                    'user_id' => $user->id,
                    'permission_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "    ✓ Ditambahkan: {$permName}\n";
            } else {
                echo "    → Sudah ada: {$permName}\n";
            }
        }
        echo "\n";
    }
    
    echo "===========================================\n";
    echo "SELESAI!\n";
    echo "===========================================\n";
    echo "\nSilakan:\n";
    echo "1. Logout dari aplikasi\n";
    echo "2. Login kembali\n";
    echo "3. Menu OB Bongkar akan muncul\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
