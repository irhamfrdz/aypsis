<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=================================================\n";
    echo "  Adding Pembayaran DP OB Permissions\n";
    echo "=================================================\n\n";

    // Define permissions
    $permissions = [
        'pembayaran-ob-view',
        'pembayaran-ob-create',
        'pembayaran-ob-edit',
        'pembayaran-ob-delete',
        'pembayaran-ob-print',
        'pembayaran-ob-export',
    ];

    $insertedCount = 0;
    $existingCount = 0;

    foreach ($permissions as $permissionName) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permissionName)
            ->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'description' => 'Permission for ' . str_replace('-', ' ', $permissionName),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Added: {$permissionName}\n";
            $insertedCount++;
        } else {
            echo "- Already exists: {$permissionName}\n";
            $existingCount++;
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo "- New permissions added: $insertedCount\n";
    echo "- Already existing: $existingCount\n";
    echo "- Total: " . count($permissions) . "\n";
    echo str_repeat("=", 50) . "\n\n";

    // Assign to admin user (user_id = 1)
    echo "Assigning permissions to admin user (ID: 1)...\n\n";

    $adminUserId = 1;
    $adminUser = DB::table('users')->where('id', $adminUserId)->first();

    if ($adminUser) {
        $assignedCount = 0;
        $alreadyAssignedCount = 0;

        foreach ($permissions as $permissionName) {
            $permission = DB::table('permissions')
                ->where('name', $permissionName)
                ->first();

            if ($permission) {
                // Check if already assigned
                $alreadyAssigned = DB::table('model_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $adminUserId)
                    ->exists();

                if (!$alreadyAssigned) {
                    DB::table('model_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $adminUserId,
                    ]);
                    echo "✓ Assigned: {$permissionName}\n";
                    $assignedCount++;
                } else {
                    echo "- Already assigned: {$permissionName}\n";
                    $alreadyAssignedCount++;
                }
            }
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Admin Assignment Summary:\n";
        echo "- New assignments: $assignedCount\n";
        echo "- Already assigned: $alreadyAssignedCount\n";
        echo str_repeat("=", 50) . "\n\n";

        echo "✅ All permissions have been processed successfully!\n\n";

        // Show total permissions for admin
        $totalPermissions = DB::table('model_has_permissions')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', $adminUserId)
            ->count();

        echo "Admin user now has {$totalPermissions} permissions in total.\n";
    } else {
        echo "⚠ Admin user (ID: 1) not found.\n";
    }

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
