<?php

/**
 * Script untuk menambahkan permissions Tanda Terima Bongkaran
 * 
 * Cara menjalankan:
 * php add_tanda_terima_bongkaran_perms.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "Menambahkan Permissions Tanda Terima Bongkaran\n";
echo "==============================================\n\n";

try {
    // Define permissions
    $permissions = [
        [
            'name' => 'tanda-terima-bongkaran-view',
            'description' => 'View tanda terima bongkaran'
        ],
        [
            'name' => 'tanda-terima-bongkaran-create',
            'description' => 'Create tanda terima bongkaran'
        ],
        [
            'name' => 'tanda-terima-bongkaran-update',
            'description' => 'Update tanda terima bongkaran'
        ],
        [
            'name' => 'tanda-terima-bongkaran-delete',
            'description' => 'Delete tanda terima bongkaran'
        ],
        [
            'name' => 'tanda-terima-bongkaran-print',
            'description' => 'Print tanda terima bongkaran'
        ],
        [
            'name' => 'tanda-terima-bongkaran-export',
            'description' => 'Export tanda terima bongkaran'
        ],
    ];

    echo "Permissions yang akan ditambahkan:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-40s %-40s\n", "Name", "Description");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($permissions as $perm) {
        printf("%-40s %-40s\n", $perm['name'], $perm['description']);
    }
    echo str_repeat("-", 80) . "\n\n";
    
    // Ask for confirmation
    echo "Apakah Anda yakin ingin menambahkan permissions ini? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'yes' && $confirmation !== 'y') {
        echo "\nProses dibatalkan.\n";
        exit(0);
    }
    
    echo "\nMemulai proses...\n\n";
    
    // Start transaction
    DB::beginTransaction();
    
    try {
        $addedCount = 0;
        $skippedCount = 0;
        $addedPermissions = [];
        
        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existing = DB::table('permissions')
                ->where('name', $permission['name'])
                ->first();
            
            if ($existing) {
                echo "⊘ Permission '{$permission['name']}' sudah ada, dilewati\n";
                $skippedCount++;
                continue;
            }
            
            // Insert permission
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $addedPermissions[] = [
                'id' => $permissionId,
                'name' => $permission['name']
            ];
            
            echo "✓ Permission '{$permission['name']}' berhasil ditambahkan (ID: {$permissionId})\n";
            $addedCount++;
        }
        
        // Auto-assign to admin and user_admin
        if (!empty($addedPermissions)) {
            echo "\n" . str_repeat("-", 80) . "\n";
            echo "Memberikan permissions ke admin dan user_admin...\n";
            echo str_repeat("-", 80) . "\n\n";
            
            $adminUsers = DB::table('users')
                ->whereIn('role', ['admin', 'user_admin'])
                ->get();
            
            $assignedCount = 0;
            
            foreach ($adminUsers as $user) {
                foreach ($addedPermissions as $perm) {
                    // Check if already assigned
                    $existingAssignment = DB::table('user_permissions')
                        ->where('user_id', $user->id)
                        ->where('permission_id', $perm['id'])
                        ->first();
                    
                    if (!$existingAssignment) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $user->id,
                            'permission_id' => $perm['id'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        echo "✓ Permission '{$perm['name']}' diberikan ke {$user->name} ({$user->role})\n";
                        $assignedCount++;
                    }
                }
            }
            
            echo "\nTotal permissions yang diberikan ke admin: {$assignedCount}\n";
        }
        
        DB::commit();
        
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "SUCCESS!\n";
        echo "Total permissions baru ditambahkan: {$addedCount}\n";
        echo "Total permissions yang sudah ada: {$skippedCount}\n";
        echo str_repeat("=", 80) . "\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (\Exception $e) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "ERROR!\n";
    echo "Pesan error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo str_repeat("=", 80) . "\n";
    exit(1);
}
