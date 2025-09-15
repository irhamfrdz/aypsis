<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing form submission simulation\n";
echo "==================================\n\n";

// Test data - user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate form data that would be sent when user checks all tagihan-kontainer permissions
$formData = [
    'username' => 'test4',
    'karyawan_id' => $user->karyawan_id,
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
        'master-pranota-tagihan-kontainer' => [
            'access' => '1'
        ]
    ]
];

echo "Simulated form data:\n";
print_r($formData);
echo "\n";

// Create a mock request
$request = new Request();
$request->merge($formData);

// Test convertMatrixPermissionsToIds
$controller = new UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

$permissionIds = $method->invoke($controller, $formData['permissions']);

echo "Converted Permission IDs:\n";
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        echo "  - ID {$id}: {$perm->name}\n";
    } else {
        echo "  - ID {$id}: NOT FOUND\n";
    }
}
echo "\n";

// Check if these are the expected permissions
$expectedPermissions = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete',
    'tagihan-kontainer-approve',
    'tagihan-kontainer-print',
    'tagihan-kontainer-export',
    'master-pranota-tagihan-kontainer'
];

$actualPermissions = [];
foreach ($permissionIds as $id) {
    $perm = Permission::find($id);
    if ($perm) {
        $actualPermissions[] = $perm->name;
    }
}

echo "Expected permissions:\n";
foreach ($expectedPermissions as $perm) {
    echo "  - $perm\n";
}

echo "\nActual permissions from conversion:\n";
foreach ($actualPermissions as $perm) {
    echo "  - $perm\n";
}

$missing = array_diff($expectedPermissions, $actualPermissions);
$extra = array_diff($actualPermissions, $expectedPermissions);

if (empty($missing) && empty($extra)) {
    echo "\n✅ All permissions converted correctly!\n";
} else {
    if (!empty($missing)) {
        echo "\n❌ Missing permissions:\n";
        foreach ($missing as $perm) {
            echo "  - $perm\n";
        }
    }
    if (!empty($extra)) {
        echo "\n❌ Extra permissions:\n";
        foreach ($extra as $perm) {
            echo "  - $perm\n";
        }
    }
}

echo "\nTest completed!\n";
