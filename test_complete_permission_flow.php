<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

echo "🧪 Testing complete permission flow for master-karyawan\n\n";

// 1. Create a test user
echo "1️⃣ Creating test user...\n";
$testUser = User::create([
    'username' => 'test_karyawan_main_' . time(),
    'password' => Hash::make('password123'),
    'karyawan_id' => null
]);
echo "✅ Test user created: {$testUser->username} (ID: {$testUser->id})\n\n";

// 2. Get permission IDs for master-karyawan permissions
echo "2️⃣ Getting permission IDs...\n";
$permissions = [
    'master-karyawan',        // Main permission for sidebar
    'master-karyawan.view',   // View permission
    'master-karyawan.create', // Create permission
];

$permissionIds = [];
foreach ($permissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if ($perm) {
        $permissionIds[] = $perm->id;
        echo "  ✅ Found: {$permName} (ID: {$perm->id})\n";
    } else {
        echo "  ❌ Not found: {$permName}\n";
    }
}
echo "\n";

// 3. Assign permissions to user
echo "3️⃣ Assigning permissions to user...\n";
$testUser->permissions()->sync($permissionIds);
echo "✅ Permissions assigned successfully\n\n";

// 4. Verify permissions
echo "4️⃣ Verifying user permissions...\n";
$userPermissions = $testUser->permissions->pluck('name')->toArray();
echo "User has permissions: " . implode(', ', $userPermissions) . "\n\n";

// 5. Test sidebar permission check
echo "5️⃣ Testing sidebar permission check...\n";
$hasMainPermission = $testUser->hasPermissionTo('master-karyawan');
$hasViewPermission = $testUser->hasPermissionTo('master-karyawan.view');

echo "  - master-karyawan (main): " . ($hasMainPermission ? '✅ YES' : '❌ NO') . "\n";
echo "  - master-karyawan.view: " . ($hasViewPermission ? '✅ YES' : '❌ NO') . "\n\n";

// 6. Test with Gate (AuthServiceProvider)
echo "6️⃣ Testing Gate authorization...\n";
$canAccessMain = \Illuminate\Support\Facades\Gate::allows('master-karyawan', $testUser);
$canAccessView = \Illuminate\Support\Facades\Gate::allows('master-karyawan.view', $testUser);

echo "  - Gate master-karyawan: " . ($canAccessMain ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "  - Gate master-karyawan.view: " . ($canAccessView ? '✅ ALLOWED' : '❌ DENIED') . "\n\n";

// 7. Clean up
echo "7️⃣ Cleaning up test user...\n";
$testUser->permissions()->detach();
$testUser->delete();
echo "✅ Test user and permissions cleaned up\n\n";

echo "🎉 Complete permission flow test finished!\n";
echo "📋 Summary:\n";
echo "  - Main permission (master-karyawan): " . ($hasMainPermission ? 'WORKING ✅' : 'FAILED ❌') . "\n";
echo "  - Detail permissions: " . (count($userPermissions) > 1 ? 'WORKING ✅' : 'FAILED ❌') . "\n";
echo "  - Gate authorization: " . ($canAccessMain ? 'WORKING ✅' : 'FAILED ❌') . "\n";
