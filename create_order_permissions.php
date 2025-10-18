<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "🔧 Creating Order Permissions (order-*)\n";
echo "=====================================\n\n";

$orderPermissions = [
    'order-view' => 'View Orders',
    'order-create' => 'Create Orders',
    'order-update' => 'Update Orders',
    'order-delete' => 'Delete Orders',
    'order-print' => 'Print Orders',
    'order-export' => 'Export Orders'
];

foreach ($orderPermissions as $name => $description) {
    $existingPermission = Permission::where('name', $name)->first();

    if ($existingPermission) {
        echo "⚠️  Permission '$name' already exists\n";
    } else {
        $permission = Permission::create([
            'name' => $name,
            'description' => $description
        ]);
        echo "✅ Created permission: $name ($description)\n";
    }
}

echo "\n📊 Summary:\n";
$orderPerms = Permission::where('name', 'like', 'order-%')->where('name', 'not like', 'order-management-%')->pluck('name');
echo "Total order permissions: " . $orderPerms->count() . "\n";
foreach($orderPerms as $perm) {
    echo "   - $perm\n";
}

echo "\n🎯 Now sidebar will recognize these permissions!\n";

?>
