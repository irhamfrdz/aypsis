<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

echo "=== Testing User Approval Route Access ===\n\n";

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

echo "\n=== Route Middleware Check ===\n";

// Simulate middleware check for user-approval routes
$currentMiddleware = 'permission:master-user';
echo "Current middleware: {$currentMiddleware}\n";

$requiredPermission = str_replace('permission:', '', $currentMiddleware);
$hasRequiredPermission = $user->can($requiredPermission);
echo "User has required permission '{$requiredPermission}': " . ($hasRequiredPermission ? 'YES' : 'NO') . "\n";

if (!$hasRequiredPermission) {
    echo "\n❌ PROBLEM FOUND: User test4 does NOT have 'master-user' permission\n";
    echo "But has 'user-approval' permission: " . ($user->can('user-approval') ? 'YES' : 'NO') . "\n";
    echo "And has 'user-approval.view' permission: " . ($user->can('user-approval.view') ? 'YES' : 'NO') . "\n";
    echo "\n💡 SOLUTION: Update middleware in web.php to check for appropriate permissions\n";
} else {
    echo "\n✅ User has required permission - route should be accessible\n";
}

echo "\n=== Suggested Middleware Fix ===\n";
echo "Current: middleware(['auth', 'permission:master-user'])\n";
echo "Suggested: middleware(['auth'])->group(function () {\n";
echo "    // Add permission checks in controller methods instead\n";
echo "})\n";

echo "\nOr use permission:can middleware with multiple permissions:\n";
echo "middleware(['auth', 'permission:user-approval|user-approval.view|master-user'])\n";
