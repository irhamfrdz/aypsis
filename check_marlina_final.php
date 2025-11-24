<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CHECKING MARLINA USER PERMISSIONS ===\n\n";
    
    // Find Marlina user
    $marlina = DB::table('users')
        ->leftJoin('karyawans', 'users.karyawan_id', '=', 'karyawans.id')
        ->select('users.id', 'users.username', 'users.status', 'karyawans.nama_lengkap', 'karyawans.divisi')
        ->where('users.username', 'marlina')
        ->first();
    
    if (!$marlina) {
        echo "❌ User marlina tidak ditemukan!\n";
        exit;
    }
    
    echo "✅ User ditemukan:\n";
    echo "   - Name: {$marlina->nama_lengkap}\n";
    echo "   - Username: {$marlina->username}\n";
    echo "   - Status: {$marlina->status}\n";
    echo "   - Divisi: {$marlina->divisi}\n";
    echo "   - User ID: {$marlina->id}\n\n";
    
    // Check user permissions
    echo "=== CHECKING PERMISSIONS ===\n";
    $permissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $marlina->id)
        ->select('permissions.name as permission_name')
        ->get();
    
    if ($permissions->count() > 0) {
        echo "Total permissions: " . $permissions->count() . "\n\n";
        
        // Check specifically for orders permissions
        $orderPermissions = $permissions->filter(function($perm) {
            return stripos($perm->permission_name, 'orders') !== false;
        });
        
        echo "=== ORDERS PERMISSIONS ===\n";
        if ($orderPermissions->count() > 0) {
            echo "✅ Marlina memiliki " . $orderPermissions->count() . " orders permissions:\n";
            foreach($orderPermissions as $perm) {
                echo "   - {$perm->permission_name}\n";
            }
            echo "\n✅ MARLINA DAPAT MENGAKSES MENU ORDERS\n\n";
        } else {
            echo "❌ Marlina TIDAK memiliki orders permissions\n\n";
        }
        
        // Show all permissions for reference
        echo "=== ALL PERMISSIONS ===\n";
        foreach($permissions as $perm) {
            echo "   - {$perm->permission_name}\n";
        }
        
    } else {
        echo "❌ Marlina tidak memiliki permissions sama sekali!\n";
    }
    
    // Check if orders permissions exist in the system
    echo "\n=== AVAILABLE ORDERS PERMISSIONS IN SYSTEM ===\n";
    $availableOrdersPerms = DB::table('permissions')
        ->where('name', 'LIKE', '%orders%')
        ->select('name')
        ->get();
        
    if ($availableOrdersPerms->count() > 0) {
        foreach($availableOrdersPerms as $perm) {
            echo "   - {$perm->name}\n";
        }
    } else {
        echo "   No orders permissions found in system\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END CHECK ===\n";