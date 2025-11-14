<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Get admin user
    $adminUser = DB::table('users')->where('username', 'admin')->first();
    
    if (!$adminUser) {
        echo "User admin tidak ditemukan!\n";
        exit(1);
    }
    
    echo "User Admin ditemukan: ID {$adminUser->id}, Username: {$adminUser->username}\n\n";
    
    // Define pranota-ob permissions
    $permissions = [
        'pranota-ob-view',
        'pranota-ob-create',
        'pranota-ob-update',
        'pranota-ob-delete',
    ];
    
    $addedCount = 0;
    $existingCount = 0;
    
    foreach ($permissions as $permissionName) {
        // Check if permission exists
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if (!$permission) {
            // Create permission if not exists
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'description' => ucwords(str_replace('-', ' ', $permissionName)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Permission '{$permissionName}' dibuat (ID: {$permissionId})\n";
        } else {
            $permissionId = $permission->id;
            echo "- Permission '{$permissionName}' sudah ada (ID: {$permissionId})\n";
        }
        
        // Check if user already has this permission
        $existingUserPermission = DB::table('user_permissions')
            ->where('user_id', $adminUser->id)
            ->where('permission_id', $permissionId)
            ->first();
        
        if (!$existingUserPermission) {
            // Add permission to admin user
            DB::table('user_permissions')->insert([
                'user_id' => $adminUser->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  → Permission ditambahkan ke user admin\n";
            $addedCount++;
        } else {
            echo "  → Permission sudah ada di user admin\n";
            $existingCount++;
        }
    }
    
    echo "\n";
    echo "========================================\n";
    echo "RINGKASAN:\n";
    echo "========================================\n";
    echo "Total permissions: " . count($permissions) . "\n";
    echo "Ditambahkan: {$addedCount}\n";
    echo "Sudah ada: {$existingCount}\n";
    echo "========================================\n";
    echo "✓ Selesai!\n";
    
    // Clear cache
    echo "\nMembersihkan cache...\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Cache dibersihkan\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
