<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "🚀 Adding BL (Bill of Lading) permissions...\n";

    // 1. Add BL permissions to permissions table if they don't exist
    $blPermissions = [
        [
            'name' => 'bl-view',
            'description' => 'View BL (Bill of Lading) pages',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'bl-create',
            'description' => 'Create new BL (Bill of Lading)',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'bl-edit',
            'description' => 'Edit existing BL (Bill of Lading)',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'bl-delete',
            'description' => 'Delete BL (Bill of Lading)',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];

    foreach ($blPermissions as $permission) {
        $existing = DB::table('permissions')->where('name', $permission['name'])->first();
        if (!$existing) {
            DB::table('permissions')->insert($permission);
            echo "✅ Added permission: {$permission['name']}\n";
        } else {
            echo "⚠️  Permission already exists: {$permission['name']}\n";
        }
    }

    // 2. Get admin user
    $adminUser = DB::table('users')->where('username', 'admin')->first();
    if (!$adminUser) {
        echo "❌ Admin user not found!\n";
        DB::rollBack();
        exit(1);
    }

    echo "👤 Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";

    // 3. Assign all BL permissions to admin
    foreach ($blPermissions as $permissionData) {
        $permission = DB::table('permissions')->where('name', $permissionData['name'])->first();
        if ($permission) {
            $existing = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permission->id)
                ->first();

            if (!$existing) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Assigned {$permission->name} to admin\n";
            } else {
                echo "⚠️  Admin already has {$permission->name} permission\n";
            }
        }
    }

    DB::commit();
    echo "\n🎉 Successfully added BL permissions to admin user!\n";
    echo "\nPermissions added:\n";
    echo "- bl-view: View BL pages\n";
    echo "- bl-create: Create new BL\n";
    echo "- bl-edit: Edit existing BL\n";
    echo "- bl-delete: Delete BL\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}