<?php
// Include composer autoload and bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\User;

echo "=== TESTING USER MARLINA PERMISSION CHECK ===\n\n";

// Find Marlina user
$marlina = User::where('username', 'marlina')->first();

if (!$marlina) {
    echo "❌ Marlina user not found!\n";
    exit;
}

echo "✅ User: {$marlina->username} (ID: {$marlina->id})\n";
echo "   Name: {$marlina->name}\n";
echo "   Status: " . ($marlina->status ? 'Active' : 'Inactive') . "\n\n";

// Test individual permissions
$orderPermissions = ['order-view', 'order-create', 'order-update', 'order-delete', 'order-print', 'order-export'];

echo "Testing Order Permissions:\n";
foreach ($orderPermissions as $permission) {
    $canDo = $marlina->can($permission);
    echo "   " . ($canDo ? "✅" : "❌") . " {$permission}: " . ($canDo ? "ALLOWED" : "DENIED") . "\n";
}

echo "\n";

// Test the exact condition from hasSuratJalanPermissions
$hasOrderPermissions = $marlina->can('order-view') || $marlina->can('order-create') || $marlina->can('order-update') || $marlina->can('order-delete');
echo "hasSuratJalanPermissions (order part): " . ($hasOrderPermissions ? "✅ TRUE" : "❌ FALSE") . "\n";

// Test surat jalan permissions too
$hasSuratJalanPerms = $marlina->can('surat-jalan-view') || $marlina->can('surat-jalan-create') || $marlina->can('surat-jalan-update') || $marlina->can('surat-jalan-delete');
echo "hasSuratJalanPermissions (surat-jalan part): " . ($hasSuratJalanPerms ? "✅ TRUE" : "❌ FALSE") . "\n";

$hasApprovalOrder = $marlina->can('approval-order-view');
echo "hasSuratJalanPermissions (approval-order part): " . ($hasApprovalOrder ? "✅ TRUE" : "❌ FALSE") . "\n";

$finalResult = $hasOrderPermissions || $hasSuratJalanPerms || $hasApprovalOrder;
echo "\nFinal hasSuratJalanPermissions result: " . ($finalResult ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\n";

// Check if permissions are actually in database
echo "Checking permissions in database:\n";
$userPerms = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $marlina->id)
    ->whereIn('permissions.name', $orderPermissions)
    ->select('permissions.name', 'permissions.id')
    ->get();

foreach ($userPerms as $perm) {
    echo "   ✅ {$perm->name} (ID: {$perm->id})\n";
}

echo "\n";

// Test specific permission class method
echo "Testing Permission class methods:\n";
foreach (['order-view', 'order-create', 'order-update'] as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $marlina->id)
            ->where('permission_id', $permission->id)
            ->exists();
        echo "   {$permName}: DB check = " . ($hasPermission ? "✅ TRUE" : "❌ FALSE") . "\n";
    }
}

echo "\n=== END TEST ===\n";