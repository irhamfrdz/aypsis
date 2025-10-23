<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔐 Setting up Prospek Permissions...\n";
echo "====================================\n\n";

try {
    // Define prospek permissions
    $prospekPermissions = [
        'prospek-view' => 'View Prospek Data',
    ];

    echo "📋 Adding Prospek Permissions to Database...\n\n";

    // Add permissions to database
    foreach ($prospekPermissions as $name => $description) {
        $existingPermission = DB::table('permissions')->where('name', $name)->first();

        if (!$existingPermission) {
            DB::table('permissions')->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Added permission: {$name} - {$description}\n";
        } else {
            echo "ℹ️  Permission already exists: {$name}\n";
        }
    }

    echo "\n📝 Assigning Prospek Permissions to Admin Users...\n\n";

    // Get admin users
    $adminUsers = DB::table('users')
        ->where('role', 'admin')
        ->orWhere('role', 'user_admin')
        ->get();

    if ($adminUsers->isEmpty()) {
        echo "⚠️  No admin users found. Please create admin users first.\n";
        exit(1);
    }

    // Assign permissions to admin users
    foreach ($adminUsers as $admin) {
        foreach ($prospekPermissions as $permissionName => $description) {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                $existingUserPermission = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->where('permission_id', $permission->id)
                    ->first();

                if (!$existingUserPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "✅ Assigned '{$permissionName}' to admin ID: {$admin->id}\n";
                } else {
                    echo "ℹ️  Admin ID {$admin->id} already has permission: {$permissionName}\n";
                }
            }
        }
    }

    echo "\n🎯 Verifying Prospek Permissions...\n\n";

    // Verify permissions were added
    foreach ($prospekPermissions as $permissionName => $description) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        if ($permission) {
            $userCount = DB::table('user_permissions')
                ->where('permission_id', $permission->id)
                ->count();
            echo "📊 Permission '{$permissionName}': {$userCount} users assigned\n";
        }
    }

    echo "\n✨ PROSPEK PERMISSIONS SETUP COMPLETED! ✨\n";
    echo "==========================================\n\n";

    echo "🔍 What was set up:\n";
    echo "• ✅ Permission 'prospek-view' for viewing prospek data\n";
    echo "• ✅ Permissions assigned to all admin users\n";
    echo "• ✅ Menu prospek will appear for users with permissions\n\n";

    echo "📱 Access Information:\n";
    echo "• Menu: Prospek (appears after Dashboard in sidebar)\n";
    echo "• URL: /prospek\n";
    echo "• Features: List and view detail prospek data\n\n";

    echo "🎮 Next Steps:\n";
    echo "1. Visit /prospek to see the prospek listing\n";
    echo "2. Add sample data to the 'prospek' table if needed\n";
    echo "3. Test the search and filter functionality\n\n";

    echo "✅ Setup completed successfully!\n";

} catch (\Exception $e) {
    echo "❌ Error setting up prospek permissions: " . $e->getMessage() . "\n";
    echo "📍 Error occurred at line: " . $e->getLine() . "\n";
    echo "📄 In file: " . $e->getFile() . "\n";
    exit(1);
}
