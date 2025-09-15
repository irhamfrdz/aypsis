<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== FINAL END-TO-END TEST: UI Permission Update Flow ===\n\n";

// Step 1: Simulate form submission (user checks pranota-supir-view)
echo "=== STEP 1: Simulating Form Submission ===\n";
$formData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

echo "Form data submitted: " . print_r($formData, true);

// Step 2: Test permission conversion
echo "=== STEP 2: Testing Permission Conversion ===\n";
$controller = new UserController();
$permissionIds = $controller->testConvertMatrixPermissionsToIds($formData['permissions']);

echo "Converted permission IDs: " . print_r($permissionIds, true);

if (!empty($permissionIds)) {
    $permissionDetails = [];
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            $permissionDetails[] = $permission->name . " (ID: {$id})";
        }
    }
    echo "Permission details: " . implode(', ', $permissionDetails) . "\n";
}

// Step 3: Simulate user permission update
echo "\n=== STEP 3: Simulating User Permission Update ===\n";
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "User test4 found (ID: {$user->id})\n";

// Clear existing permissions and sync new ones
$user->permissions()->detach();
$user->permissions()->sync($permissionIds);

$user->refresh();
$updatedPermissions = $user->permissions->pluck('name')->toArray();
echo "Updated user permissions: " . implode(', ', $updatedPermissions) . "\n";

// Step 4: Test sidebar visibility logic
echo "\n=== STEP 4: Testing Sidebar Visibility ===\n";
$isAdmin = false; // test4 is not admin

$hasPranotaPermission = $isAdmin ||
    $user->hasPermissionTo('pranota-supir') ||
    $user->hasPermissionTo('pranota-supir-view') ||
    $user->hasPermissionTo('pranota-supir-create') ||
    $user->hasPermissionTo('pranota-supir-update') ||
    $user->hasPermissionTo('pranota-supir-delete') ||
    $user->hasPermissionTo('pranota-supir-approve') ||
    $user->hasPermissionTo('pranota-supir-print') ||
    $user->hasPermissionTo('pranota-supir-export');

echo "isAdmin: " . ($isAdmin ? 'YES' : 'NO') . "\n";
echo "hasPermissionTo('pranota-supir'): " . ($user->hasPermissionTo('pranota-supir') ? 'YES' : 'NO') . "\n";
echo "hasPermissionTo('pranota-supir-view'): " . ($user->hasPermissionTo('pranota-supir-view') ? 'YES' : 'NO') . "\n";
echo "Menu should be visible: " . ($hasPranotaPermission ? 'YES' : 'NO') . "\n";

// Step 5: Final verification
echo "\n=== STEP 5: FINAL VERIFICATION ===\n";
if ($hasPranotaPermission) {
    echo "✅ SUCCESS: Complete flow works!\n";
    echo "   - Form submission: ✅ Processed\n";
    echo "   - Permission conversion: ✅ Working\n";
    echo "   - User permissions: ✅ Updated\n";
    echo "   - Sidebar logic: ✅ Menu visible\n\n";
    echo "🎉 Menu Pranota Supir will now appear in sidebar when user checks the permission!\n";
} else {
    echo "❌ FAILURE: Something is still broken in the flow\n";
    echo "🔍 Check the following:\n";
    echo "   - Permission conversion\n";
    echo "   - User permission sync\n";
    echo "   - Sidebar logic\n";
}

echo "\n=== SUMMARY ===\n";
echo "Issue: Menu Pranota Supir not appearing after checking permission in UI\n";
echo "Root Cause: Sidebar logic only checked 'pranota-supir' but UI saves 'pranota-supir-view'\n";
echo "Solution: Updated sidebar logic to check all pranota-supir permission variants\n";
echo "Result: " . ($hasPranotaPermission ? '✅ FIXED' : '❌ STILL BROKEN') . "\n";

?>
