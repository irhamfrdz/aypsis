<?php

/**
 * Script untuk menambahkan permissions Master Gudang ke database
 * 
 * Cara menjalankan:
 * php add_master_gudang_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "========================================\n";
    echo "Adding Master Gudang Permissions\n";
    echo "========================================\n\n";

    // Permissions untuk Master Gudang
    $permissionNames = [
        'master-gudang-view',
        'master-gudang-create',
        'master-gudang-edit',
        'master-gudang-delete',
        'master-gudang-print',
        'master-gudang-export',
    ];

    $addedCount = 0;
    $existingCount = 0;

    foreach ($permissionNames as $permissionName) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permissionName)
            ->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Added permission: {$permissionName}\n";
            $addedCount++;
        } else {
            echo "- Permission already exists: {$permissionName}\n";
            $existingCount++;
        }
    }

    echo "\n========================================\n";
    echo "Summary:\n";
    echo "- Permissions added: {$addedCount}\n";
    echo "- Already existing: {$existingCount}\n";
    echo "- Total processed: " . count($permissionNames) . "\n";
    echo "========================================\n\n";

    // Assign all Master Gudang permissions to admin users
    echo "Assigning all Master Gudang permissions to admin user(s)...\n\n";
    
    // Get admin user(s) - assuming admin is user_id = 1, or users with username 'admin'
    $adminUsers = DB::table('users')
        ->where('username', 'admin')
        ->orWhere('id', 1)
        ->get();

    if ($adminUsers->isEmpty()) {
        echo "⚠ No admin users found. Skipping permission assignment.\n";
    } else {
        $assignedCount = 0;

        foreach ($adminUsers as $admin) {
            echo "Processing user: {$admin->username} (ID: {$admin->id})\n";
            
            foreach ($permissionNames as $permissionName) {
                // Get permission ID
                $permissionRecord = DB::table('permissions')
                    ->where('name', $permissionName)
                    ->first();

                if ($permissionRecord) {
                    // Check if permission is already assigned
                    $exists = DB::table('user_permissions')
                        ->where('user_id', $admin->id)
                        ->where('permission_id', $permissionRecord->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $admin->id,
                            'permission_id' => $permissionRecord->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        echo "  ✓ Assigned {$permissionName} to {$admin->username}\n";
                        $assignedCount++;
                    } else {
                        echo "  - Already assigned: {$permissionName}\n";
                    }
                }
            }
        }

        echo "\n========================================\n";
        echo "Permission Assignment Summary:\n";
        echo "- Admin users processed: " . count($adminUsers) . "\n";
        echo "- Permissions assigned: {$assignedCount}\n";
        echo "========================================\n\n";
    }

    echo "✅ Script completed successfully!\n";
    echo "Master Gudang permissions have been added to the database.\n\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
