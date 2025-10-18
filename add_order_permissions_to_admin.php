<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔧 Adding Order Permissions to Admin User\n";
echo "=========================================\n\n";

$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Admin user found (ID: {$admin->id})\n";

// Get order permissions
$orderPermissions = Permission::where('name', 'like', 'order-%')
    ->where('name', 'not like', 'order-management-%')
    ->pluck('id');

echo "📋 Found " . $orderPermissions->count() . " order permissions to add\n";

// Get existing permission IDs
$existingPermissionIds = $admin->permissions()->pluck('permissions.id')->toArray();

// Add new order permissions to existing ones
$newPermissionIds = array_unique(array_merge($existingPermissionIds, $orderPermissions->toArray()));

// Sync permissions
$admin->permissions()->sync($newPermissionIds);

echo "✅ Added order permissions to admin user\n";

// Verify
$adminOrderPerms = $admin->permissions()
    ->where('name', 'like', 'order-%')
    ->where('name', 'not like', 'order-management-%')
    ->pluck('name');

echo "\n📊 Admin now has " . $adminOrderPerms->count() . " order permissions:\n";
foreach($adminOrderPerms as $perm) {
    echo "   - $perm\n";
}

echo "\n🎉 Success! Admin can now see Order Management menu in sidebar!\n";

?>
