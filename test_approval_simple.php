<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('username', 'admin')->first();
$userPermissions = $user->permissions->pluck('name')->toArray();

echo "=== ADMIN USER APPROVAL PERMISSIONS ===\n";
echo "User permissions: " . implode(', ', $userPermissions) . "\n\n";

$hasDashboard = in_array('approval-dashboard', $userPermissions);
$hasView = in_array('approval.view', $userPermissions) || in_array('approval-view', $userPermissions);
$hasApprove = in_array('approval.approve', $userPermissions) || in_array('approval-approve', $userPermissions);

echo "Dashboard permission (approval-dashboard): " . ($hasDashboard ? 'YES ✅' : 'NO ❌') . "\n";
echo "View permission (approval.view or approval-view): " . ($hasView ? 'YES ✅' : 'NO ❌') . "\n";
echo "Approve permission (approval.approve or approval-approve): " . ($hasApprove ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n=== DETAILED PERMISSION CHECK ===\n";
$hasDotApprove = $user->permissions()->where('name', 'approval.approve')->exists();
$hasDashApprove = $user->permissions()->where('name', 'approval-approve')->exists();
$hasDotView = $user->permissions()->where('name', 'approval.view')->exists();
$hasDashView = $user->permissions()->where('name', 'approval-view')->exists();
$hasDotDashboard = $user->permissions()->where('name', 'approval.dashboard')->exists();
$hasDashDashboard = $user->permissions()->where('name', 'approval-dashboard')->exists();

echo "approval.approve (dot): " . ($hasDotApprove ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval.approve') ? 'YES' : 'NO') . "\n";
echo "approval-approve (dash): " . ($hasDashApprove ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval-approve') ? 'YES' : 'NO') . "\n";
echo "approval.view (dot): " . ($hasDotView ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval.view') ? 'YES' : 'NO') . "\n";
echo "approval-view (dash): " . ($hasDashView ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval-view') ? 'YES' : 'NO') . "\n";
echo "approval.dashboard (dot): " . ($hasDotDashboard ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval.dashboard') ? 'YES' : 'NO') . "\n";
echo "approval-dashboard (dash): " . ($hasDashDashboard ? 'HAS' : 'NO') . " | can(): " . ($user->can('approval-dashboard') ? 'YES' : 'NO') . "\n";

echo "\nTest completed.\n";
