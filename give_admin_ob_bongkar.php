<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Memberikan permission OB Bongkar ke user admin...\n\n";
    
    // Cek role admin
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    if (!$adminRole) {
        echo "ERROR: Role admin tidak ditemukan!\n";
        exit(1);
    }
    
    // Cek user admin
    $adminUser = DB::table('users')->where('username', 'admin')->first();
    if (!$adminUser) {
        echo "ERROR: User admin tidak ditemukan!\n";
        exit(1);
    }
    
    // Assign role admin ke user admin
    $hasRole = DB::table('role_user')
        ->where('user_id', $adminUser->id)
        ->where('role_id', $adminRole->id)
        ->exists();
    
    if (!$hasRole) {
        DB::table('role_user')->insert([
            'user_id' => $adminUser->id,
            'role_id' => $adminRole->id,
        ]);
        echo "✓ Role admin di-assign ke user\n";
    }
    
    // Assign permissions OB Bongkar ke role admin
    $permissions = ['ob-bongkar-view', 'ob-bongkar-create', 'ob-bongkar-edit', 'ob-bongkar-delete'];
    
    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        
        if ($perm) {
            $exists = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $perm->id)
                ->exists();
            
            if (!$exists) {
                DB::table('permission_role')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $perm->id,
                ]);
            }
            echo "✓ {$permName}\n";
        }
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Logout dan login kembali untuk melihat menu OB Bongkar.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
