<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

echo "=== END-TO-END TEST: Form Submission to Database ===\n\n";

// Simulate form data that would be sent when pranota-supir-view is checked
$formData = [
    'permissions' => [
        'pranota-supir' => [
            'view' => '1'
        ]
    ]
];

echo "Simulated Form Data:\n";
print_r($formData);
echo "\n";

// Step 1: Test convertMatrixPermissionsToIds
echo "=== STEP 1: Testing convertMatrixPermissionsToIds ===\n";
$controller = new UserController();
$permissionIds = $controller->testConvertMatrixPermissionsToIds($formData['permissions']);

echo "Permission IDs from conversion: " . print_r($permissionIds, true);

if (!empty($permissionIds)) {
    echo "Permission Details:\n";
    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            echo "- ID {$id}: {$permission->name}\n";
        }
    }
} else {
    echo "❌ No permission IDs found!\n";
    exit(1);
}

echo "\n=== STEP 2: Testing User Permission Sync ===\n";

// Get user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "User test4 found (ID: {$user->id})\n";

// Clear existing permissions first (to simulate clean state)
$user->permissions()->detach();
echo "Cleared existing permissions\n";

// Sync the new permissions
$user->permissions()->sync($permissionIds);
echo "Synced permissions: " . implode(', ', $permissionIds) . "\n";

// Verify permissions were saved
$user->refresh(); // Reload from database
$savedPermissions = $user->permissions->pluck('name')->toArray();

echo "Permissions after sync:\n";
print_r($savedPermissions);

$hasPranotaSupir = in_array('pranota-supir-view', $savedPermissions) ||
                   in_array('pranota-supir.view', $savedPermissions) ||
                   in_array('pranota-supir', $savedPermissions);

echo "\nHas pranota-supir permission: " . ($hasPranotaSupir ? 'YES' : 'NO') . "\n";

echo "\n=== STEP 3: Testing Permission Check (User hasPermissionTo) ===\n";

// Test User hasPermissionTo method
$canPranotaSupir = $user->hasPermissionTo('pranota-supir');
$canPranotaSupirView = $user->hasPermissionTo('pranota-supir-view');
$canPranotaSupirDot = $user->hasPermissionTo('pranota-supir.view');

echo "user->hasPermissionTo('pranota-supir'): " . ($canPranotaSupir ? 'YES' : 'NO') . "\n";
echo "user->hasPermissionTo('pranota-supir-view'): " . ($canPranotaSupirView ? 'YES' : 'NO') . "\n";
echo "user->hasPermissionTo('pranota-supir.view'): " . ($canPranotaSupirDot ? 'YES' : 'NO') . "\n";

echo "\n=== STEP 4: Testing Sidebar Logic ===\n";

// Test the sidebar condition logic
$isAdmin = $user->isAdmin ?? false; // Assuming isAdmin is a method or attribute
$sidebarVisible = $isAdmin || $canPranotaSupir;

echo "isAdmin: " . ($isAdmin ? 'YES' : 'NO') . "\n";
echo "can('pranota-supir'): " . ($canPranotaSupir ? 'YES' : 'NO') . "\n";
echo "Menu should be visible: " . ($sidebarVisible ? 'YES' : 'NO') . "\n";

echo "\n=== FINAL RESULT ===\n";
if ($sidebarVisible) {
    echo "✅ SUCCESS: Menu Pranota Supir should be visible in sidebar\n";
} else {
    echo "❌ FAILURE: Menu Pranota Supir will NOT be visible in sidebar\n";
    echo "🔍 Possible issues:\n";
    echo "   - User is not admin AND cannot 'pranota-supir'\n";
    echo "   - Permission check is failing\n";
    echo "   - Sidebar logic has issues\n";
}

?>
