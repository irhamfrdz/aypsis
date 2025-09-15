<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get a test user
$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "User not found.\n";
    exit(1);
}

echo "Checking user: {$user->username} (ID: {$user->id})\n\n";

// Check if permission exists
$perm = Permission::where('name', 'tagihan-kontainer-view')->first();
if ($perm) {
    echo "Permission found: {$perm->name} (ID: {$perm->id})\n";

    // Check if user has this permission
    $hasPermission = $user->permissions->contains('id', $perm->id);
    echo "User has permission: " . ($hasPermission ? 'YES' : 'NO') . "\n";

    // Check using hasPermissionTo method
    $hasPermissionTo = $user->hasPermissionTo('tagihan-kontainer-view');
    echo "hasPermissionTo('tagihan-kontainer-view'): " . ($hasPermissionTo ? 'YES' : 'NO') . "\n";

    // Check gate without user parameter
    $gateAllowsNoUser = Gate::allows('tagihan-kontainer-view');
    echo "Gate::allows('tagihan-kontainer-view') [no user param]: " . ($gateAllowsNoUser ? 'YES' : 'NO') . "\n";

    // Check gate with user parameter
    $gateAllows = Gate::allows('tagihan-kontainer-view', $user);
    echo "Gate::allows('tagihan-kontainer-view', \$user): " . ($gateAllows ? 'YES' : 'NO') . "\n";

    // Check gate with can method
    $canMethod = $user->can('tagihan-kontainer-view');
    echo "user->can('tagihan-kontainer-view'): " . ($canMethod ? 'YES' : 'NO') . "\n";

} else {
    echo "Permission 'tagihan-kontainer-view' not found in database.\n";
}

// Test with a known working permission
$masterUserPerm = Permission::where('name', 'master-user')->first();
if ($masterUserPerm) {
    echo "\n=== Testing master-user permission ===\n";
    echo "Permission found: {$masterUserPerm->name} (ID: {$masterUserPerm->id})\n";

    $hasMasterUser = $user->permissions->contains('id', $masterUserPerm->id);
    echo "User has permission: " . ($hasMasterUser ? 'YES' : 'NO') . "\n";

    $hasPermissionToMaster = $user->hasPermissionTo('master-user');
    echo "hasPermissionTo('master-user'): " . ($hasPermissionToMaster ? 'YES' : 'NO') . "\n";

    $gateAllowsMaster = Gate::allows('master-user', $user);
    echo "Gate::allows('master-user', \$user): " . ($gateAllowsMaster ? 'YES' : 'NO') . "\n";

    $canMethodMaster = $user->can('master-user');
    echo "user->can('master-user'): " . ($canMethodMaster ? 'YES' : 'NO') . "\n";
}
