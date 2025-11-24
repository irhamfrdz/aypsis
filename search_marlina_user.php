<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== SEARCHING FOR USER MARLINA ===\n\n";
    
    // Search in karyawan table first
    echo "1. Searching in karyawan table:\n";
    $karyawanResults = DB::table('karyawans')->where('nama_lengkap', 'LIKE', '%marlina%')->get();
    
    if ($karyawanResults->count() > 0) {
        foreach($karyawanResults as $k) {
            echo "   - Found: {$k->nama_lengkap} (ID: {$k->id}, Divisi: {$k->divisi})\n";
        }
    } else {
        echo "   - No karyawan with 'marlina' found\n";
        
        // Show all karyawan names containing 'mar' or similar
        echo "\n   Searching for similar names (containing 'mar'):\n";
        $similarResults = DB::table('karyawans')->where('nama_lengkap', 'LIKE', '%mar%')->get();
        foreach($similarResults as $s) {
            echo "     * {$s->nama_lengkap} (ID: {$s->id})\n";
        }
    }
    
    // Search in users table
    echo "\n2. Searching in users table:\n";
    $users = DB::table('users')
        ->leftJoin('karyawans', 'users.karyawan_id', '=', 'karyawans.id')
        ->select('users.id', 'users.username', 'users.status', 'karyawans.nama_lengkap', 'karyawans.divisi')
        ->get();
    
    echo "   All users:\n";
    foreach($users as $user) {
        $name = $user->nama_lengkap ?? $user->username;
        echo "     - {$name} (Username: {$user->username}, Status: {$user->status})\n";
        
        // Check if this could be Marlina
        if (stripos($name, 'marlina') !== false || stripos($name, 'mar') !== false) {
            echo "       *** POSSIBLE MATCH FOR MARLINA ***\n";
            
            // Check permissions for this user
            echo "       Checking permissions...\n";
            
            // Check roles
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->pluck('roles.name');
                
            if ($roles->count() > 0) {
                echo "       Roles: " . $roles->implode(', ') . "\n";
            } else {
                echo "       No roles assigned\n";
            }
            
            // Check orders permissions
            $ordersPermissions = DB::table('model_has_roles')
                ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->where('permissions.name', 'LIKE', 'orders-%')
                ->pluck('permissions.name');
                
            if ($ordersPermissions->count() > 0) {
                echo "       Orders Permissions: " . $ordersPermissions->implode(', ') . "\n";
                echo "       ✅ CAN ACCESS ORDERS MENU\n";
            } else {
                echo "       ❌ NO ORDERS PERMISSIONS\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END SEARCH ===\n";