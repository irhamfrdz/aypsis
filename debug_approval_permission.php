<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== DEBUGGING APPROVAL PERMISSION ISSUE ===\n\n";

$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Admin user found: {$user->username} (ID: {$user->id})\n\n";

// Check direct permission relationship
echo "=== DIRECT PERMISSION RELATIONSHIP ===\n";
$directPermissions = $user->permissions()->where('name', 'like', 'approval-%')->get();
echo "Direct approval permissions count: {$directPermissions->count()}\n";
foreach ($directPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\n=== PERMISSION CACHE CHECK ===\n";
// Force refresh user model
$user->refresh();

echo "After refresh - Direct approval permissions count: " . $user->permissions()->where('name', 'like', 'approval-%')->count() . "\n";

// Check specific permission
$approvalDashboardPerm = Permission::where('name', 'approval-dashboard')->first();
if ($approvalDashboardPerm) {
    echo "Permission 'approval-dashboard' exists in DB: YES (ID: {$approvalDashboardPerm->id})\n";

    $hasDirectRelation = $user->permissions()->where('permission_id', $approvalDashboardPerm->id)->exists();
    echo "User has direct relationship to approval-dashboard: " . ($hasDirectRelation ? 'YES' : 'NO') . "\n";
} else {
    echo "Permission 'approval-dashboard' NOT found in DB!\n";
}

echo "\n=== USER CAN CHECK ===\n";
echo "\$user->can('approval-dashboard'): " . ($user->can('approval-dashboard') ? 'YES' : 'NO') . "\n";
echo "\$user->can('approval.view'): " . ($user->can('approval.view') ? 'YES' : 'NO') . "\n";

echo "\n=== PERMISSION LOADING CHECK ===\n";
// Force load permissions
$user->load('permissions');
echo "After load - User permissions count: " . $user->permissions->count() . "\n";
echo "Approval permissions in loaded collection: " . $user->permissions->where('name', 'like', 'approval-%')->count() . "\n";

echo "\n=== POSSIBLE ISSUES ===\n";
echo "1. Permission cache issue\n";
echo "2. Permission relationship not properly saved\n";
echo "3. Laravel Gate/Permission system not recognizing the permission\n";
echo "4. Database transaction not committed\n";

echo "\nDebug completed.\n";
