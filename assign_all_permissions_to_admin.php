<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Assigning ALL Permissions to Admin User\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Find admin user
$admin = DB::table('users')->where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    echo "Available users:\n";
    $users = DB::table('users')->select('id', 'username')->get();
    foreach ($users as $user) {
        echo "  - {$user->username} (ID: {$user->id})\n";
    }
    exit(1);
}

echo "✅ Found admin user: {$admin->username} (ID: {$admin->id})\n\n";

// Get all permissions
$allPermissions = DB::table('permissions')->get();

echo "📋 Total permissions in database: " . count($allPermissions) . "\n\n";

// Get current admin permissions
$currentPermissions = DB::table('user_permissions')
    ->where('user_id', $admin->id)
    ->pluck('permission_id')
    ->toArray();

echo "📊 Admin currently has: " . count($currentPermissions) . " permissions\n\n";

echo "🔄 Assigning all permissions to admin...\n\n";

$assigned = 0;
$skipped = 0;

DB::beginTransaction();

try {
    foreach ($allPermissions as $permission) {
        if (in_array($permission->id, $currentPermissions)) {
            $skipped++;
        } else {
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $permission->id
            ]);
            $assigned++;
            
            if ($assigned % 50 == 0) {
                echo "  ⏳ Assigned {$assigned} permissions...\n";
            }
        }
    }
    
    DB::commit();
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✅ SUCCESS!\n\n";
    echo "  📊 Total permissions: " . count($allPermissions) . "\n";
    echo "  ✅ Newly assigned: {$assigned}\n";
    echo "  ⏭️  Already had: {$skipped}\n";
    echo "  🎯 Final total: " . (count($currentPermissions) + $assigned) . "\n\n";
    echo "💡 Admin user now has ALL permissions!\n";
    echo "🔄 Please logout and login again to refresh permissions\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
