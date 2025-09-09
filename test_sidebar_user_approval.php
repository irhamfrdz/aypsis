<?php
// Test sidebar conditions for user approval menu
echo "=== TESTING SIDEBAR USER APPROVAL MENU ===\n";

// Set up environment  
define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "1. Testing current user and permissions...\n";
    
    // Get admin user
    $adminUser = App\Models\User::where('username', 'admin')->first();
    if (!$adminUser) {
        echo "❌ Admin user not found\n";
        return;
    }
    
    echo "User: {$adminUser->name} ({$adminUser->username})\n";
    
    // Check if user has admin role or master-user permission
    $hasAdminRole = $adminUser->hasRole('admin');
    $hasMasterUserPermission = $adminUser->hasPermissionTo('master-user');
    
    echo "Has admin role: " . ($hasAdminRole ? 'YES' : 'NO') . "\n";
    echo "Has master-user permission: " . ($hasMasterUserPermission ? 'YES' : 'NO') . "\n";
    
    echo "\n2. Testing sidebar condition...\n";
    $shouldShowMenu = $hasAdminRole || $hasMasterUserPermission;
    echo "Should show User Approval menu: " . ($shouldShowMenu ? 'YES' : 'NO') . "\n";
    
    echo "\n3. Testing pending users count...\n";
    $pendingCount = App\Models\User::where('status', 'pending')->count();
    echo "Pending users count: {$pendingCount}\n";
    
    echo "\n4. Testing route access...\n";
    $routeUrl = route('admin.user-approval.index');
    echo "User approval route: {$routeUrl}\n";
    
    if ($shouldShowMenu) {
        echo "\n✅ MENU SHOULD BE VISIBLE!\n";
        echo "The 'Persetujuan User' menu should appear in the sidebar\n";
        
        if ($pendingCount > 0) {
            echo "✅ Badge should show: {$pendingCount}\n";
        } else {
            echo "ℹ️ No badge (no pending users)\n";
        }
    } else {
        echo "\n❌ MENU WILL NOT BE VISIBLE\n";
        echo "User doesn't have required permissions\n";
    }
    
    echo "\n5. Testing all users and their roles...\n";
    $users = App\Models\User::with('roles')->get();
    foreach ($users as $user) {
        $roles = $user->roles->pluck('name')->join(', ');
        $isAdmin = $user->hasRole('admin');
        $canSeeMasterUser = $user->hasPermissionTo('master-user');
        $canSeeMenu = $isAdmin || $canSeeMasterUser;
        
        echo "- {$user->username}: roles=[{$roles}] admin={$isAdmin} master-user={$canSeeMasterUser} can_see_menu={$canSeeMenu}\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
