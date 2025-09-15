<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

echo "🔍 Investigating Gate execution order\n\n";

// 1. Create a test user
echo "1️⃣ Creating test user...\n";
$testUser = User::create([
    'username' => 'gate_order_test_' . time(),
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

// 4. Check if gates are defined
echo "4️⃣ Checking gate definitions...\n";
$hasMainGate = Gate::has('master-karyawan');
$hasViewGate = Gate::has('master-karyawan.view');

echo "  - Gate 'master-karyawan' defined: " . ($hasMainGate ? '✅ YES' : '❌ NO') . "\n";
echo "  - Gate 'master-karyawan.view' defined: " . ($hasViewGate ? '✅ YES' : '❌ NO') . "\n\n";

// 5. Test Gate::check method
echo "5️⃣ Testing Gate::check method...\n";
$checkMain = Gate::check('master-karyawan', $freshUser);
$checkView = Gate::check('master-karyawan.view', $freshUser);

echo "  - Gate::check('master-karyawan'): " . ($checkMain ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "  - Gate::check('master-karyawan.view'): " . ($checkView ? '✅ ALLOWED' : '❌ DENIED') . "\n\n";

// 6. Test with different user parameter order
echo "6️⃣ Testing with different parameter order...\n";
$allowsMainAlt = Gate::allows('master-karyawan', [$freshUser]);
$allowsViewAlt = Gate::allows('master-karyawan.view', [$freshUser]);

echo "  - Gate::allows('master-karyawan', [\$user]): " . ($allowsMainAlt ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "  - Gate::allows('master-karyawan.view', [\$user]): " . ($allowsViewAlt ? '✅ ALLOWED' : '❌ DENIED') . "\n\n";

// 7. Test Gate::inspect
echo "7️⃣ Testing Gate::inspect...\n";
try {
    $inspectMain = Gate::inspect('master-karyawan', $freshUser);
    $inspectView = Gate::inspect('master-karyawan.view', $freshUser);

    echo "  - Gate::inspect('master-karyawan'): " . ($inspectMain->allowed() ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    echo "    Message: " . $inspectMain->message() . "\n";
    echo "  - Gate::inspect('master-karyawan.view'): " . ($inspectView->allowed() ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    echo "    Message: " . $inspectView->message() . "\n\n";
} catch (Exception $e) {
    echo "  - Error with Gate::inspect: " . $e->getMessage() . "\n\n";
}

// 8. Test raw permission check
echo "8️⃣ Testing raw permission check...\n";
$rawMain = $freshUser->hasPermissionTo('master-karyawan');
$rawView = $freshUser->hasPermissionTo('master-karyawan.view');

echo "  - Raw hasPermissionTo('master-karyawan'): " . ($rawMain ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "  - Raw hasPermissionTo('master-karyawan.view'): " . ($rawView ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// 9. Clean up
echo "9️⃣ Cleaning up...\n";
$testUser->permissions()->detach();
$testUser->delete();
echo "✅ Cleanup completed\n\n";

echo "🎯 Summary:\n";
echo "  - Gates defined: " . (($hasMainGate && $hasViewGate) ? '✅ YES' : '❌ NO') . "\n";
echo "  - Gate::check: " . (($checkMain && $checkView) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Gate::allows alt: " . (($allowsMainAlt && $allowsViewAlt) ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "  - Raw permission: " . (($rawMain && $rawView) ? '✅ WORKING' : '❌ FAILED') . "\n";
