<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

echo "🔬 Testing Gate definition directly\n\n";

// 1. Create a test user
echo "1️⃣ Creating test user...\n";
$testUser = User::create([
    'username' => 'direct_gate_test_' . time(),
    'password' => Hash::make('password123'),
    'karyawan_id' => null
]);
echo "✅ Test user created: {$testUser->username} (ID: {$testUser->id})\n\n";

// 2. Assign permissions
$mainPerm = Permission::where('name', 'master-karyawan')->first();
$viewPerm = Permission::where('name', 'master-karyawan.view')->first();

$testUser->permissions()->sync([$mainPerm->id, $viewPerm->id]);
echo "✅ Permissions assigned\n\n";

// 3. Fresh load user
$freshUser = User::with('permissions')->find($testUser->id);

// 4. Test direct gate definition call
echo "4️⃣ Testing direct gate definition call...\n";

// Try to call the gate directly by simulating the callback
try {
    // Simulate the gate definition: function ($user) use ($permission) { return $user->hasPermissionTo($permission); }
    $directResultMain = $freshUser->hasPermissionTo('master-karyawan');
    $directResultView = $freshUser->hasPermissionTo('master-karyawan.view');

    echo "  - Simulated gate for 'master-karyawan': " . ($directResultMain ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    echo "  - Simulated gate for 'master-karyawan.view': " . ($directResultView ? '✅ ALLOWED' : '❌ DENIED') . "\n";
} catch (Exception $e) {
    echo "  - Error calling gate directly: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Test Gate::allows again
echo "5️⃣ Testing Gate::allows again...\n";
$allowsMain = Gate::allows('master-karyawan', $freshUser);
$allowsView = Gate::allows('master-karyawan.view', $freshUser);

echo "  - Gate::allows('master-karyawan'): " . ($allowsMain ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "  - Gate::allows('master-karyawan.view'): " . ($allowsView ? '✅ ALLOWED' : '❌ DENIED') . "\n\n";

// 6. Debug Gate::before
echo "6️⃣ Testing Gate::before logic step by step...\n";

$ability = 'master-karyawan';
$user = $freshUser;

// Simulate Gate::before logic
echo "  Checking user instance: " . (get_class($user) === 'App\Models\User' ? '✅ OK' : '❌ WRONG CLASS') . "\n";

// Check admin role
$hasAdminRole = $user->hasRole('admin');
echo "  Has admin role: " . ($hasAdminRole ? '✅ YES' : '❌ NO') . "\n";

// Check hasPermissionTo
$hasPerm = $user->hasPermissionTo($ability);
echo "  hasPermissionTo('$ability'): " . ($hasPerm ? '✅ TRUE' : '❌ FALSE') . "\n";

// Check permission aliases
$abilityAliases = config('permission_aliases', []);
$hasAlias = false;
if (isset($abilityAliases[$ability])) {
    echo "  Found aliases for '$ability': " . implode(', ', $abilityAliases[$ability]) . "\n";
    foreach ($abilityAliases[$ability] as $alias) {
        if ($user->hasPermissionTo($alias) || $user->hasPermissionLike($alias)) {
            $hasAlias = true;
            echo "    ✅ Has alias permission: $alias\n";
            break;
        }
    }
} else {
    echo "  No aliases found for '$ability'\n";
}

// Check dashboard
$isDashboard = ($ability === 'dashboard');
echo "  Is dashboard ability: " . ($isDashboard ? '✅ YES' : '❌ NO') . "\n";

// Final Gate::before result
$beforeResult = null;
if ($hasAdminRole) {
    $beforeResult = true;
    echo "  Gate::before result: ✅ TRUE (admin role)\n";
} elseif ($hasPerm) {
    $beforeResult = true;
    echo "  Gate::before result: ✅ TRUE (has permission)\n";
} elseif ($hasAlias) {
    $beforeResult = true;
    echo "  Gate::before result: ✅ TRUE (has alias)\n";
} elseif ($isDashboard) {
    $beforeResult = true;
    echo "  Gate::before result: ✅ TRUE (dashboard)\n";
} else {
    $beforeResult = null;
    echo "  Gate::before result: null (continue to specific gate)\n";
}

// 7. Clean up
echo "\n7️⃣ Cleaning up...\n";
$testUser->permissions()->detach();
$testUser->delete();
echo "✅ Cleanup completed\n\n";

echo "🎯 Summary:\n";
echo "  - Simulated gate: " . (($directResultMain && $directResultView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Gate::allows: " . (($allowsMain && $allowsView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Gate::before logic: " . ($beforeResult ? '✅ WORKING' : '❌ FAILED') . "\n";
