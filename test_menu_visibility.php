<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\User;

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';require 'vendor/autoload.php';require_once 'vendor/autoload.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();



echo "Testing COA Menu Visibility:\n";$app = require_once 'bootstrap/app.php';// Load Laravel environment

echo "===========================\n";

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);$app = require_once 'bootstrap/app.php';

$user = App\Models\User::where('username', 'test')->first();

if ($user) {$kernel->bootstrap();$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    echo "User 'test' found\n";

    echo "Can access master.coa.index: " . ($user->can('master.coa.index') ? 'YES' : 'NO') . "\n";



    $permissions = $user->permissions()->where('name', 'like', '%coa%')->get();echo "Testing COA Menu Visibility:\n";use Illuminate\Support\Facades\Auth;

    echo "\nCOA permissions for user 'test':\n";

    foreach($permissions as $permission) {
        echo "===========================\n";
        echo "- " . $permission->name . "\n";
    }

} else {

    echo "User 'test' not found\n";$user = App\Models\User::where('username', 'test')->first();

}

if ($user) {echo "=== Testing User Approval Menu Visibility ===\n\n";

echo "\nDone.\n";
    echo "User 'test' found\n";

    echo "Can access master.coa.index: " . ($user->can('master.coa.index') ? 'YES' : 'NO') . "\n";// Test with user test4

    echo "Can access master-coa.view: " . ($user->can('master-coa.view') ? 'YES' : 'NO') . "\n";$user = User::where('username', 'test4')->first();

if (!$user) {

    $permissions = $user->permissions()->where('name', 'like', '%coa%')->get();    echo "User test4 not found!\n";

    echo "\nCOA permissions for user 'test':\n";    exit(1);

    foreach($permissions as $permission) {}

        echo "- " . $permission->name . "\n";

    }echo "User: {$user->username} (ID: {$user->id})\n";

} else {echo "Email: {$user->email}\n";

    echo "User 'test' not found\n";echo "Status: {$user->status}\n\n";

}

// Check permissions

echo "\nDone.\n";echo "=== Permission Check ===\n";
$permissions = [
    'master-user',
    'user-approval',
    'user-approval.view',
    'user-approval.create',
    'user-approval.update',
    'user-approval.delete',
    'user-approval.approve',
    'user-approval.print',
    'user-approval.export'
];

foreach ($permissions as $permission) {
    $hasPermission = $user->can($permission);
    echo "Can '{$permission}': " . ($hasPermission ? 'YES' : 'NO') . "\n";
}

echo "\n=== Menu Visibility Logic Test ===\n";

// Simulate the logic from app.blade.php
$isAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');
echo "Is Admin: " . ($isAdmin ? 'YES' : 'NO') . "\n";

$hasUserApprovalAccess = $isAdmin ||
    $user->can('master-user') ||
    $user->can('user-approval') ||
    $user->can('user-approval.view') ||
    $user->can('user-approval.create') ||
    $user->can('user-approval.update') ||
    $user->can('user-approval.delete') ||
    $user->can('user-approval.approve') ||
    $user->can('user-approval.print') ||
    $user->can('user-approval.export');

echo "Has User Approval Access: " . ($hasUserApprovalAccess ? 'YES' : 'NO') . "\n";

if ($hasUserApprovalAccess) {
    echo "\n✅ SUCCESS: User approval menu SHOULD be visible for user test4\n";
} else {
    echo "\n❌ FAILED: User approval menu will NOT be visible for user test4\n";
}

echo "\n=== Summary ===\n";
echo "User test4 has user-approval permission: " . ($user->can('user-approval') ? 'YES' : 'NO') . "\n";
echo "Menu visibility condition includes user-approval: YES\n";
echo "Expected result: Menu should be visible\n";
