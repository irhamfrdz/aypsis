<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== TAGIHAN CAT PERMISSION TROUBLESHOOTING ===\n\n";

// 1. Check if permissions exist in database
echo "1. Checking Tagihan CAT permissions in database:\n";
echo "================================================\n";

$tagihanCatPermissions = Permission::where('name', 'like', 'tagihan-cat%')->get();
$permissionNames = $tagihanCatPermissions->pluck('name')->toArray();

echo "Found " . count($permissionNames) . " permissions:\n";
foreach ($permissionNames as $name) {
    echo "- $name\n";
}

if (count($permissionNames) < 6) {
    echo "\n❌ WARNING: Missing some Tagihan CAT permissions!\n";
    echo "Expected: tagihan-cat-view, tagihan-cat-create, tagihan-cat-update, tagihan-cat-delete, tagihan-cat-print, tagihan-cat-export\n";
}

// 2. Test UserController conversion
echo "\n2. Testing UserController conversion:\n";
echo "=====================================\n";

$userController = new \App\Http\Controllers\UserController();

// Simulate form data from edit page
$formData = [
    'tagihan-cat' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1',
    ]
];

echo "Form data simulation:\n";
print_r($formData);

$permissionIds = $userController->testConvertMatrixPermissionsToIds($formData);

echo "\nConverted permission IDs: " . implode(', ', $permissionIds) . "\n";

$convertedPermissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
echo "Converted permission names: " . implode(', ', $convertedPermissions) . "\n";

// 3. Test with a test user
echo "\n3. Testing with a test user:\n";
echo "=============================\n";

$testUser = User::where('username', 'test')->first();
if (!$testUser) {
    // Create test user if not exists
    $testUser = User::create([
        'username' => 'test',
        'password' => bcrypt('password'),
        'karyawan_id' => null
    ]);
    echo "Created test user: {$testUser->username} (ID: {$testUser->id})\n";
} else {
    echo "Using existing test user: {$testUser->username} (ID: {$testUser->id})\n";
}

// Get current permissions
$currentPermissions = $testUser->permissions()->where('name', 'like', 'tagihan-cat%')->pluck('name')->toArray();
echo "Current Tagihan CAT permissions: " . implode(', ', $currentPermissions) . "\n";

// Assign permissions
$testUser->permissions()->sync($permissionIds);

// Get updated permissions
$testUser->refresh();
$updatedPermissions = $testUser->permissions()->where('name', 'like', 'tagihan-cat%')->pluck('name')->toArray();
echo "Updated Tagihan CAT permissions: " . implode(', ', $updatedPermissions) . "\n";

if (count($updatedPermissions) === count($convertedPermissions)) {
    echo "\n✅ SUCCESS: All permissions were saved correctly!\n";
} else {
    echo "\n❌ FAILED: Permission count mismatch!\n";
    echo "Expected: " . count($convertedPermissions) . ", Got: " . count($updatedPermissions) . "\n";
}

// 4. Test convertPermissionsToMatrix (reverse conversion)
echo "\n4. Testing reverse conversion (permissions to matrix):\n";
echo "=====================================================\n";

$userPermissionNames = $testUser->permissions->pluck('name')->toArray();
echo "User permissions: " . implode(', ', $userPermissionNames) . "\n";

// Test reverse conversion manually
$matrixPermissions = [];
foreach ($userPermissionNames as $permissionName) {
    if (strpos($permissionName, 'tagihan-cat-') === 0) {
        $action = str_replace('tagihan-cat-', '', $permissionName);
        if (!isset($matrixPermissions['tagihan-cat'])) {
            $matrixPermissions['tagihan-cat'] = [];
        }
        $matrixPermissions['tagihan-cat'][$action] = true;
    }
}

if (isset($matrixPermissions['tagihan-cat'])) {
    echo "Matrix permissions for tagihan-cat:\n";
    print_r($matrixPermissions['tagihan-cat']);
} else {
    echo "❌ No tagihan-cat permissions found in matrix!\n";
}

// 5. Summary
echo "\n5. SUMMARY:\n";
echo "===========\n";

$issues = [];

if (count($permissionNames) < 6) {
    $issues[] = "Missing Tagihan CAT permissions in database";
}

if (empty($permissionIds)) {
    $issues[] = "Permission conversion failed";
}

if (count($updatedPermissions) !== count($convertedPermissions)) {
    $issues[] = "Permission sync failed";
}

if (!isset($matrixPermissions['tagihan-cat'])) {
    $issues[] = "Reverse conversion failed";
}

if (empty($issues)) {
    echo "✅ ALL TESTS PASSED! Tagihan CAT permissions should work correctly.\n";
    echo "\nPossible reasons for user issues:\n";
    echo "- User might not be checking the right checkboxes\n";
    echo "- Form might not be submitting correctly\n";
    echo "- User might be looking at wrong user account\n";
    echo "- Browser cache issues\n";
} else {
    echo "❌ ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "- $issue\n";
    }
}

echo "\n=== END OF TROUBLESHOOTING ===\n";
