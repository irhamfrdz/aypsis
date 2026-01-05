<?php

/**
 * Script untuk menambahkan permissions Master Pricelist Buruh
 * 
 * Permissions yang ditambahkan:
 * - master-pricelist-buruh-view
 * - master-pricelist-buruh-create
 * - master-pricelist-buruh-update
 * - master-pricelist-buruh-delete
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "==============================================\n";
echo "  ADDING MASTER PRICELIST BURUH PERMISSIONS\n";
echo "==============================================\n\n";

// Define permissions to be added
$permissions = [
    [
        'name' => 'master-pricelist-buruh-view',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'master-pricelist-buruh-create',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'master-pricelist-buruh-update',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'master-pricelist-buruh-delete',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
];

try {
    DB::beginTransaction();

    echo "Processing permissions...\n\n";

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permission['name'])
            ->exists();

        if ($exists) {
            echo "⚠️  Permission '{$permission['name']}' already exists. Skipping...\n";
        } else {
            DB::table('permissions')->insert($permission);
            echo "✅ Permission '{$permission['name']}' added successfully!\n";
        }
    }

    DB::commit();

    echo "\n==============================================\n";
    echo "  PERMISSIONS ADDED SUCCESSFULLY!\n";
    echo "==============================================\n\n";

    echo "Summary:\n";
    echo "- Total permissions processed: " . count($permissions) . "\n";
    echo "- Module: Master Pricelist Buruh\n\n";

    echo "Next steps:\n";
    echo "1. Assign these permissions to appropriate roles/users\n";
    echo "2. Test the permissions in the application\n";
    echo "3. Verify that the menu items appear correctly\n\n";

    // Optional: Assign to admin user
    echo "Do you want to assign all these permissions to admin user? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    
    if (trim(strtolower($line)) === 'yes' || trim(strtolower($line)) === 'y') {
        assignToAdmin($permissions);
    }

    fclose($handle);

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

/**
 * Assign permissions to admin user
 */
function assignToAdmin($permissions)
{
    echo "\nAssigning permissions to admin user...\n";

    try {
        // Find admin user (you may need to adjust this query based on your admin identification)
        $adminUser = DB::table('users')
            ->where('username', 'admin')
            ->orWhere('id', 1)
            ->first();

        if (!$adminUser) {
            echo "⚠️  Admin user not found. Skipping permission assignment.\n";
            return;
        }

        foreach ($permissions as $permission) {
            // Get permission ID
            $permissionRecord = DB::table('permissions')
                ->where('name', $permission['name'])
                ->first();

            if ($permissionRecord) {
                // Check if user already has this permission
                $hasPermission = DB::table('model_has_permissions')
                    ->where('permission_id', $permissionRecord->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $adminUser->id)
                    ->exists();

                if (!$hasPermission) {
                    DB::table('model_has_permissions')->insert([
                        'permission_id' => $permissionRecord->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $adminUser->id,
                    ]);
                    echo "✅ Assigned '{$permission['name']}' to admin user\n";
                } else {
                    echo "⚠️  Admin already has '{$permission['name']}'\n";
                }
            }
        }

        echo "\n✅ All permissions assigned to admin user successfully!\n";

    } catch (\Exception $e) {
        echo "\n❌ Error assigning to admin: " . $e->getMessage() . "\n";
    }
}

echo "\n==============================================\n";
echo "  SCRIPT COMPLETED\n";
echo "==============================================\n";
