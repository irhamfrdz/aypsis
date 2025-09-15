<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

echo "🔍 Debugging Gate authorization issue\n\n";

// 1. Create a test user
echo "1️⃣ Creating test user...\n";
$testUser = User::create([
    'username' => 'debug_gate_' . time(),
    'password' => Hash::make('password123'),
    'karyawan_id' => null
]);
echo "✅ Test user created: {$testUser->username} (ID: {$testUser->id})\n\n";

// 2. Get permission IDs
echo "2️⃣ Getting permission IDs...\n";
$mainPerm = Permission::where('name', 'master-karyawan')->first();
$viewPerm = Permission::where('name', 'master-karyawan.view')->first();

echo "  - master-karyawan: " . ($mainPerm ? "ID {$mainPerm->id}" : "NOT FOUND") . "\n";
echo "  - master-karyawan.view: " . ($viewPerm ? "ID {$viewPerm->id}" : "NOT FOUND") . "\n\n";

// 3. Assign permissions
echo "3️⃣ Assigning permissions...\n";
$testUser->permissions()->sync([$mainPerm->id, $viewPerm->id]);
echo "✅ Permissions assigned\n\n";

// 4. Test hasPermissionTo method
echo "4️⃣ Testing hasPermissionTo method...\n";
$hasMain = $testUser->hasPermissionTo('master-karyawan');
$hasView = $testUser->hasPermissionTo('master-karyawan.view');

echo "  - hasPermissionTo('master-karyawan'): " . ($hasMain ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "  - hasPermissionTo('master-karyawan.view'): " . ($hasView ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// 5. Debug permission relationship
echo "5️⃣ Debugging permission relationship...\n";
$userPermissions = $testUser->permissions()->get();
echo "User permissions count: " . $userPermissions->count() . "\n";
foreach ($userPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// 6. Fresh load user
echo "6️⃣ Fresh loading user...\n";
$freshUser = User::with('permissions')->find($testUser->id);
$hasMainFresh = $freshUser->hasPermissionTo('master-karyawan');
$hasViewFresh = $freshUser->hasPermissionTo('master-karyawan.view');

echo "  - Fresh user hasPermissionTo('master-karyawan'): " . ($hasMainFresh ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "  - Fresh user hasPermissionTo('master-karyawan.view'): " . ($hasViewFresh ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// 7. Test Gate with fresh user
echo "7️⃣ Testing Gate with fresh user...\n";
$gateMain = Gate::allows('master-karyawan', $freshUser);
$gateView = Gate::allows('master-karyawan.view', $freshUser);

echo "  - Gate::allows('master-karyawan'): " . ($gateMain ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "  - Gate::allows('master-karyawan.view'): " . ($gateView ? '✅ ALLOWED' : '❌ DENIED') . "\n\n";

// 8. Test Gate::before logic manually
echo "8️⃣ Testing Gate::before logic manually...\n";
$beforeResultMain = null;
$beforeResultView = null;

// Simulate Gate::before logic
if ($freshUser->hasRole('admin')) {
    $beforeResultMain = true;
    $beforeResultView = true;
} elseif ($freshUser->hasPermissionTo('master-karyawan')) {
    $beforeResultMain = true;
} elseif ($freshUser->hasPermissionTo('master-karyawan.view')) {
    $beforeResultView = true;
}

echo "  - Manual Gate::before for master-karyawan: " . ($beforeResultMain ? '✅ TRUE' : '❌ FALSE/NULL') . "\n";
echo "  - Manual Gate::before for master-karyawan.view: " . ($beforeResultView ? '✅ TRUE' : '❌ FALSE/NULL') . "\n\n";

// 9. Clean up
echo "9️⃣ Cleaning up...\n";
$testUser->permissions()->detach();
$testUser->delete();
echo "✅ Cleanup completed\n\n";

echo "🎯 Debug Summary:\n";
echo "  - hasPermissionTo working: " . ($hasMainFresh && $hasViewFresh ? '✅ YES' : '❌ NO') . "\n";
echo "  - Gate working: " . ($gateMain && $gateView ? '✅ YES' : '❌ NO') . "\n";
echo "  - Issue: " . (($hasMainFresh && $hasViewFresh && !($gateMain && $gateView)) ? 'Gate not working despite hasPermissionTo working' : 'Unknown') . "\n";
