<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "========================================\n";
    echo "Menambahkan Permission Print Pranota OB\n";
    echo "========================================\n\n";
    
    // Define pranota-ob-print permission
    $permissionName = 'pranota-ob-print';
    
    // Check if permission exists
    $permission = DB::table('permissions')->where('name', $permissionName)->first();
    
    if (!$permission) {
        // Create permission
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => $permissionName,
            'description' => 'Pranota Ob Print',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Permission '{$permissionName}' berhasil dibuat (ID: {$permissionId})\n\n";
    } else {
        $permissionId = $permission->id;
        echo "✓ Permission '{$permissionName}' sudah ada (ID: {$permissionId})\n\n";
    }
    
    // Get all admin users (you can modify this to target specific users)
    $adminUsers = DB::table('users')
        ->whereIn('username', ['admin', 'administrator'])
        ->orWhere('email', 'LIKE', '%admin%')
        ->get();
    
    if ($adminUsers->isEmpty()) {
        // If no admin found, get user with ID 1
        $adminUsers = DB::table('users')->where('id', 1)->get();
    }
    
    echo "Menambahkan permission ke users:\n";
    echo "----------------------------------------\n";
    
    $addedCount = 0;
    $existingCount = 0;
    
    foreach ($adminUsers as $user) {
        // Check if user already has this permission
        $existingUserPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $permissionId)
            ->first();
        
        if (!$existingUserPermission) {
            // Add permission to user
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ User '{$user->username}' (ID: {$user->id}) - Permission ditambahkan\n";
            $addedCount++;
        } else {
            echo "- User '{$user->username}' (ID: {$user->id}) - Permission sudah ada\n";
            $existingCount++;
        }
    }
    
    echo "\n========================================\n";
    echo "RINGKASAN:\n";
    echo "========================================\n";
    echo "Permission: {$permissionName}\n";
    echo "Users diproses: " . count($adminUsers) . "\n";
    echo "Permission ditambahkan: {$addedCount}\n";
    echo "Sudah ada sebelumnya: {$existingCount}\n";
    echo "========================================\n";
    
    // Clear cache
    echo "\nMembersihkan cache...\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Cache dibersihkan\n\n";
    
    echo "✓ Selesai! Silakan refresh halaman browser Anda.\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
