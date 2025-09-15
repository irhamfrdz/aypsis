<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "🎯 Testing with authenticated user context\n\n";

// 1. Create a test user
echo "1️⃣ Creating test user...\n";
$testUser = User::create([
    'username' => 'auth_context_test_' . time(),
    'password' => Hash::make('password123'),
    'karyawan_id' => null
]);
echo "✅ Test user created: {$testUser->username} (ID: {$testUser->id})\n\n";

// 2. Assign permissions
$mainPerm = Permission::where('name', 'master-karyawan')->first();
$viewPerm = Permission::where('name', 'master-karyawan.view')->first();

$testUser->permissions()->sync([$mainPerm->id, $viewPerm->id]);
echo "✅ Permissions assigned\n\n";

// 3. Login the user
echo "3️⃣ Logging in user...\n";
Auth::login($testUser);
$loggedInUser = Auth::user();
echo "✅ User logged in: " . ($loggedInUser ? $loggedInUser->username : 'FAILED') . "\n\n";

// 4. Test with authenticated context
echo "4️⃣ Testing with authenticated context...\n";

// Test Gate::allows without user parameter (should use authenticated user)
$gateAllowsMain = Gate::allows('master-karyawan');
$gateAllowsView = Gate::allows('master-karyawan.view');
echo "  - Gate::allows('ability') [authenticated]: Main=" . ($gateAllowsMain ? '✅' : '❌') . ", View=" . ($gateAllowsView ? '✅' : '❌') . "\n";

// Test $user->can() with authenticated user
$userCanMain = $loggedInUser->can('master-karyawan');
$userCanView = $loggedInUser->can('master-karyawan.view');
echo "  - \$user->can('ability') [authenticated]: Main=" . ($userCanMain ? '✅' : '❌') . ", View=" . ($userCanView ? '✅' : '❌') . "\n";

// Test Auth::user()->can()
$authUserCanMain = Auth::user()->can('master-karyawan');
$authUserCanView = Auth::user()->can('master-karyawan.view');
echo "  - Auth::user()->can('ability'): Main=" . ($authUserCanMain ? '✅' : '❌') . ", View=" . ($authUserCanView ? '✅' : '❌') . "\n";

// Test hasPermissionTo
$hasPermMain = $loggedInUser->hasPermissionTo('master-karyawan');
$hasPermView = $loggedInUser->hasPermissionTo('master-karyawan.view');
echo "  - hasPermissionTo('ability'): Main=" . ($hasPermMain ? '✅' : '❌') . ", View=" . ($hasPermView ? '✅' : '❌') . "\n\n";

// 5. Test Gate::allows with explicit user parameter
echo "5️⃣ Testing Gate::allows with explicit user parameter...\n";
$gateAllowsExplicitMain = Gate::allows('master-karyawan', $loggedInUser);
$gateAllowsExplicitView = Gate::allows('master-karyawan.view', $loggedInUser);
echo "  - Gate::allows('ability', \$user) [explicit]: Main=" . ($gateAllowsExplicitMain ? '✅' : '❌') . ", View=" . ($gateAllowsExplicitView ? '✅' : '❌') . "\n\n";

// 6. Simulate sidebar check
echo "6️⃣ Simulating sidebar check...\n";
$sidebarCheck = $loggedInUser && $loggedInUser->can('master-karyawan');
echo "  - Sidebar condition (\$user && \$user->can('master-karyawan')): " . ($sidebarCheck ? '✅ SHOW MENU' : '❌ HIDE MENU') . "\n\n";

// 7. Logout and test with non-authenticated context
echo "7️⃣ Testing with non-authenticated context...\n";
Auth::logout();

$gateAllowsNoAuthMain = Gate::allows('master-karyawan');
$gateAllowsNoAuthView = Gate::allows('master-karyawan.view');
echo "  - Gate::allows('ability') [no auth]: Main=" . ($gateAllowsNoAuthMain ? '✅' : '❌') . ", View=" . ($gateAllowsNoAuthView ? '✅' : '❌') . "\n\n";

// 8. Clean up
echo "8️⃣ Cleaning up...\n";
$testUser->permissions()->detach();
$testUser->delete();
echo "✅ Cleanup completed\n\n";

echo "🎯 Summary:\n";
echo "  - Gate::allows() authenticated: " . (($gateAllowsMain && $gateAllowsView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - \$user->can() authenticated: " . (($userCanMain && $userCanView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Auth::user()->can(): " . (($authUserCanMain && $authUserCanView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Sidebar simulation: " . ($sidebarCheck ? '✅ MENU WILL SHOW' : '❌ MENU WILL HIDE') . "\n";
echo "  - Gate::allows() explicit user: " . (($gateAllowsExplicitMain && $gateAllowsExplicitView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Gate::allows() no auth: " . (($gateAllowsNoAuthMain || $gateAllowsNoAuthView) ? '❌ UNEXPECTED' : '✅ CORRECT (denied)') . "\n";
