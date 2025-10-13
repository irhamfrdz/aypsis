<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "🔧 Adding Order permissions...\n\n";

// Create permissions if they don't exist
$permissions = [
    'order-view',
    'order-create',
    'order-update',
    'order-delete'
];

$createdCount = 0;
foreach ($permissions as $permission) {
    $existing = Permission::where('name', $permission)->first();
    if (!$existing) {
        Permission::create(['name' => $permission]);
        echo "✅ Created permission: $permission\n";
        $createdCount++;
    } else {
        echo "ℹ️ Permission already exists: $permission\n";
    }
}

echo "\n📊 Created $createdCount new permissions\n\n";

// Add permissions to admin user
$user = User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Found admin user (ID: {$user->id})\n\n";

$addedCount = 0;
foreach ($permissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        $existing = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$existing) {
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Added permission to admin: {$permName}\n";
            $addedCount++;
        } else {
            echo "ℹ️ Admin already has permission: {$permName}\n";
        }
    }
}

echo "\n🎉 Permission setup completed!\n";
echo "📊 Created $createdCount permissions, added $addedCount to admin user\n";

?>
