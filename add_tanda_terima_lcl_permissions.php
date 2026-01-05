<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding Tanda Terima LCL permissions to users...\n";
echo "================================================================================\n";

$permissions = [
    'tanda-terima-tanpa-surat-jalan-view',
    'tanda-terima-tanpa-surat-jalan-edit',
];

DB::beginTransaction();

try {
    // Get all users (we'll add permission to all users for now)
    $users = DB::table('users')->get();

    if ($users->isEmpty()) {
        echo "❌ No users found in database!\n";
        DB::rollBack();
        exit(1);
    }

    echo "Found " . count($users) . " users\n\n";

    $updated = 0;
    $skipped = 0;

    foreach ($users as $user) {
        echo "Processing user: {$user->name}\n";
        
        // Get current permissions
        $userPermissions = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->pluck('permission_name')
            ->toArray();

        $addedForUser = 0;

        foreach ($permissions as $permission) {
            // Check if permission exists in permissions table
            $permissionExists = DB::table('permissions')
                ->where('name', $permission)
                ->exists();

            if (!$permissionExists) {
                // Create permission if it doesn't exist
                DB::table('permissions')->insert([
                    'name' => $permission,
                    'description' => 'Permission for ' . str_replace('-', ' ', $permission),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "  ✅ Created permission: {$permission}\n";
            }

            // Check if user already has this permission
            if (!in_array($permission, $userPermissions)) {
                DB::table('user_permissions')->insert([
                    'user_id' => $user->id,
                    'permission_name' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "  ✅ Added permission: {$permission}\n";
                $addedForUser++;
            } else {
                echo "  ⚠️  Skipped permission: {$permission} (already exists)\n";
            }
        }

        if ($addedForUser > 0) {
            $updated++;
        } else {
            $skipped++;
        }

        echo "\n";
    }

    DB::commit();

    echo "================================================================================\n\n";
    echo "Summary:\n";
    echo "- Users updated: $updated\n";
    echo "- Users skipped (already have permissions): $skipped\n";
    echo "- Total users processed: " . count($users) . "\n\n";

    echo "✅ Done! Users can now remove LCL from containers.\n\n";
    
    // Show verification
    echo "Verification:\n";
    echo "Run this query to verify:\n";
    echo "SELECT u.name, up.permission_name \n";
    echo "FROM users u \n";
    echo "JOIN user_permissions up ON u.id = up.user_id \n";
    echo "WHERE up.permission_name LIKE 'tanda-terima-tanpa-surat-jalan%'\n";
    echo "ORDER BY u.name, up.permission_name;\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
