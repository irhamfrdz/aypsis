<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TEST SISTEM PERMISSION ===\n\n";

// 1. Test User Permission Method
echo "1. Test User hasPermissionTo method:\n";
$user = User::with('permissions')->first();
if ($user) {
    echo "   User: {$user->name}\n";
    echo "   Total permissions: " . $user->permissions->count() . "\n";
    
    // Test permission check
    $testPermission = 'master-user';
    $hasPermission = $user->hasPermissionTo($testPermission);
    echo "   Has permission '{$testPermission}': " . ($hasPermission ? 'YES' : 'NO') . "\n";
    
    // Test another permission
    $testPermission2 = 'non-existent-permission';
    $hasPermission2 = $user->hasPermissionTo($testPermission2);
    echo "   Has permission '{$testPermission2}': " . ($hasPermission2 ? 'YES' : 'NO') . "\n";
}

// 2. Test Gate Authorization
echo "\n2. Test Gate Authorization:\n";
try {
    auth()->login($user);
    
    $gateResult = Gate::allows('master-user');
    echo "   Gate allows 'master-user': " . ($gateResult ? 'YES' : 'NO') . "\n";
    
    $gateResult2 = Gate::allows('non-existent-permission');
    echo "   Gate allows 'non-existent-permission': " . ($gateResult2 ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "   Error testing gates: " . $e->getMessage() . "\n";
}

// 3. Test Permission Assignment
echo "\n3. Test Permission Assignment:\n";
try {
    // Create test user if not exists
    $testUser = User::firstOrCreate([
        'username' => 'test_permission_user'
    ], [
        'name' => 'Test Permission User',
        'password' => bcrypt('password123'),
    ]);
    
    echo "   Test user: {$testUser->name}\n";
    echo "   Before assignment - permissions: " . $testUser->permissions()->count() . "\n";
    
    // Assign a permission
    $permission = Permission::where('name', 'master-user')->first();
    if ($permission) {
        $testUser->permissions()->syncWithoutDetaching([$permission->id]);
        $testUser->refresh();
        echo "   After assignment - permissions: " . $testUser->permissions()->count() . "\n";
        echo "   Has 'master-user' permission: " . ($testUser->hasPermissionTo('master-user') ? 'YES' : 'NO') . "\n";
        
        // Test gate with this user
        auth()->login($testUser);
        $gateResult = Gate::allows('master-user');
        echo "   Gate allows for test user: " . ($gateResult ? 'YES' : 'NO') . "\n";
    }
    
} catch (Exception $e) {
    echo "   Error testing assignment: " . $e->getMessage() . "\n";
}

// 4. Test Route-based Permissions
echo "\n4. Available Route-based Permissions:\n";
$routePermissions = Permission::where('name', 'like', 'master.user.%')->get();
foreach ($routePermissions as $perm) {
    echo "   - {$perm->name}\n";
}

echo "\n=== TEST SELESAI ===\n";
