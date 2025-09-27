<?php



require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING FORM SUBMISSION WITH UNCHECKED PEMBAYARAN-PRANAOTA-CAT ===\n\n";

echo "Testing form submission simulation\n";

// Get a test user
echo "==================================\n\n";

$user = User::find(1);

if (!$user) { // Test data - user test4
    echo "❌ Test user not found\n";
    $user = User::where('username', 'test4')->first();
    if (!$user) {
        echo "❌ User test4 not found\n";
        exit(1);
    }
}
echo "✅ Found test user: {$user->name} (ID: {$user->id})\n\n";

// First, give the user pembayaran-pranota-cat permissions
echo "User: {$user->username} (ID: {$user->id})\n\n";

$user->permissions()->sync([1213, 1214, 1215, 1216, 1217, 1218]); // All pembayaran-pranota-cat permissions

echo "✅ Gave user all pembayaran-pranota-cat permissions\n\n";

// Simulate form data that would be sent when user checks all tagihan-kontainer permissions
// This formData intentionally EXCLUDES 'pembayaran-pranota-cat'
$formData = [
    'username' => 'test4',
    'karyawan_id' => $user->karyawan_id,
    'name' => $user->name,
    'password' => 'password', // Required for validation
    'password_confirmation' => 'password', // Required for validation
    'permissions' => [
        'tagihan-kontainer' => [
            'view' => '1',
            'create' => '1',
            'update' => '1',
            'delete' => '1',
            'approve' => '1',
            'print' => '1',
            'export' => '1'
        ],
        'master-user' => [
            'view' => '1',
            'create' => '1'
        ],
        // Intentionally NOT including pembayaran-pranota-cat
        'master-pranota-tagihan-kontainer' => [
            'access' => '1'
        ]
    ]
];

// Create a mock request and controller instance
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$updateMethod = $reflection->getMethod('update');
$updateMethod->setAccessible(true);

echo "🔄 Calling update method with form data that excludes pembayaran-pranota-cat...\n";
$request = new Request();
$request->merge($formData);

$response = $updateMethod->invoke($controller, $request, $user);

echo "✅ Update method executed. Checking results...\n\n";

// Re-fetch user permissions to check if 'pembayaran-pranota-cat' permissions were removed
$user->refresh(); // Refresh the user model to get the latest permissions

// Check if user still has pembayaran-pranota-cat permissions
$userPermissions = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();

echo "🔍 Checking user permissions after first update (should NOT have pembayaran-pranota-cat):\n";
// Check if user still has pembayaran-pranota-cat permissions
$controller = new UserController();
$userPermissions = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();
$reflection = new ReflectionClass($controller);

echo "🔍 Checking user permissions after update:\n";
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true); // Make the private method accessible
$permissionIds = $method->invoke($controller, $formData['permissions']);

if ($userPermissions->count() > 0) {
    echo "❌ User still has " . $userPermissions->count() . " pembayaran-pranota-cat permissions:\n";
    foreach ($userPermissions as $perm) {
        echo "  - {$perm->name}\n";
    }
    echo "Converted Permission IDs:\n";
    foreach ($permissionIds as $id) {
        $perm = Permission::find($id);
        if ($perm) {
            echo "  - ID {$id}: {$perm->name}\n";
        } else {
            echo "  - ID {$id}: NOT FOUND\n";
        }
    }
    echo "\n❌ This means the permissions were NOT removed as expected!\n";
} else {
    echo "✅ User has no pembayaran-pranota-cat permissions (correctly removed).\n";
    echo "Permissions assigned after first update:\n";
    foreach ($permissionIds as $id) { // These are the IDs that *should* be assigned
        $perm = Permission::find($id);
        if ($perm) {
            // Verify that these permissions are actually present on the user
            echo "  - ID {$id}: {$perm->name}\n";
        } else {
            echo "  - ID {$id}: NOT FOUND\n";
        }
    }
    echo "\n✅ Permissions correctly removed.\n";
}

echo "\n==================================\n";
echo "=== TESTING FORM SUBMISSION WITH CHECKED PEMBAYARAN-PRANAOTA-CAT ===\n\n";

// Simulate form data that would be sent when user checks all tagihan-kontainer permissions
// This formData now INCLUDES 'pembayaran-pranota-cat'
$formData2 = [
    'username' => 'test4',
    'karyawan_id' => $user->karyawan_id,
    'name' => $user->name,
    'password' => 'password', // Required for validation
    'password_confirmation' => 'password', // Required for validation
    'permissions' => [
        'tagihan-kontainer' => [
            'view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1', 'approve' => '1', 'print' => '1', 'export' => '1'
        ],
        'master-user' => [
            'view' => '1', 'create' => '1'
        ],
        'pembayaran-pranota-cat' => [
            'view' => '1', 'create' => '1', 'update' => '1', 'delete' => '1', 'print' => '1', 'export' => '1'
        ],
        'master-pranota-tagihan-kontainer' => [
            'access' => '1'
        ]
    ]
];

echo "🔄 Calling update method with form data that INCLUDES pembayaran-pranota-cat...\n";
$request2 = new Request();
$request2->merge($formData2);

$response2 = $updateMethod->invoke($controller, $request2, $user);

echo "✅ Update method executed. Checking results...\n\n";

// Re-fetch user permissions to check if 'pembayaran-pranota-cat' permissions were added
$user->refresh(); // Refresh the user model to get the latest permissions

$userPermissions2 = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();

echo "🔍 Checking user permissions after second update (should HAVE pembayaran-pranota-cat):\n";

$expectedPermissions = [
    'pembayaran-pranota-cat-view',
    'pembayaran-pranota-cat-create',
    'pembayaran-pranota-cat-update',
    'pembayaran-pranota-cat-delete',
    'pembayaran-pranota-cat-print',
    'pembayaran-pranota-cat-export'
];

$actualPermissions = $userPermissions2->pluck('name')->toArray();

if ($userPermissions2->count() > 0) {
    echo "✅ User has " . $userPermissions2->count() . " pembayaran-pranota-cat permissions:\n";
    foreach ($userPermissions2 as $perm) {
        echo "  - {$perm->name}\n";
    }

    $missing = array_diff($expectedPermissions, $actualPermissions);
    $extra = array_diff($actualPermissions, $expectedPermissions);

    if (empty($missing) && empty($extra)) {
        echo "\n✅ All expected pembayaran-pranota-cat permissions are present and no unexpected ones.\n";
    } else {
        echo "\n❌ Mismatch in pembayaran-pranota-cat permissions:\n";
        if (!empty($missing)) {
            echo "  Missing: " . implode(', ', $missing) . "\n";
        }
        if (!empty($extra)) {
            echo "  Extra: " . implode(', ', $extra) . "\n";
        }
    }
} else {
    echo "❌ User has no pembayaran-pranota-cat permissions (expected to have them).\n";
}

echo "\n=== Test completed! ===\n";
