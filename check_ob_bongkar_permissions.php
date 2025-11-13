<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "===========================================\n";
    echo "CEK PERMISSION OB BONGKAR\n";
    echo "===========================================\n\n";

    // Get admin role
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if (!$adminRole) {
        echo "❌ Role admin tidak ditemukan!\n";
        exit(1);
    }
    
    echo "✓ Role admin ditemukan (ID: {$adminRole->id})\n\n";

    // Check OB Bongkar permissions
    $permissions = [
        'ob-bongkar-view',
        'ob-bongkar-create',
        'ob-bongkar-edit',
        'ob-bongkar-delete',
    ];

    echo "Mengecek permissions:\n";
    echo "-------------------------------------------\n";
    
    foreach ($permissions as $permName) {
        $perm = DB::table('permissions')->where('name', $permName)->first();
        
        if ($perm) {
            echo "✓ Permission '$permName' ada (ID: {$perm->id})\n";
            
            // Check if admin role has this permission
            $hasPermission = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $perm->id)
                ->exists();
            
            if ($hasPermission) {
                echo "  → ✓ Sudah di-assign ke role admin\n";
            } else {
                echo "  → ❌ BELUM di-assign ke role admin\n";
            }
        } else {
            echo "❌ Permission '$permName' tidak ditemukan\n";
        }
    }

    echo "\n-------------------------------------------\n";
    echo "Mengecek user admin:\n";
    echo "-------------------------------------------\n";
    
    // Get admin users
    $adminUsers = DB::table('users')
        ->join('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id', $adminRole->id)
        ->select('users.id', 'users.name', 'users.email')
        ->get();
    
    foreach ($adminUsers as $user) {
        echo "✓ User: {$user->name} ({$user->email}) - ID: {$user->id}\n";
    }

    echo "\n===========================================\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
