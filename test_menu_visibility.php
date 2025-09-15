<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\User;

echo "=== Testing User Approval Menu Visibility ===\n\n";

// Test with user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "User test4 not found!\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n";
echo "Email: {$user->email}\n";
echo "Status: {$user->status}\n\n";

// Check permissions
echo "=== Permission Check ===\n";
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
