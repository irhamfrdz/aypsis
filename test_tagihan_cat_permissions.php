<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Tagihan CAT permission matrix conversion...\n";

// Test matrix permissions for tagihan-cat
$testMatrixPermissions = [
    'tagihan-cat' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1',
    ]
];

echo "Test matrix permissions:\n";
print_r($testMatrixPermissions);

// Get UserController instance
$userController = new \App\Http\Controllers\UserController();

// Test the conversion method
$permissionIds = $userController->testConvertMatrixPermissionsToIds($testMatrixPermissions);

echo "\nConverted permission IDs: " . implode(', ', $permissionIds) . "\n";

// Get permission names from IDs
$permissions = Permission::whereIn('id', $permissionIds)->get();
echo "\nPermission names:\n";
foreach ($permissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

// Test with a user
$user = User::find(1); // Assuming admin user
if ($user) {
    echo "\nTesting with user: {$user->username}\n";

    // Get current permissions
    $currentPermissions = $user->permissions->pluck('name')->toArray();
    echo "Current permissions: " . implode(', ', $currentPermissions) . "\n";

    // Sync the test permissions
    $user->permissions()->sync($permissionIds);

    // Get updated permissions
    $user->refresh();
    $updatedPermissions = $user->permissions->pluck('name')->toArray();
    echo "Updated permissions: " . implode(', ', $updatedPermissions) . "\n";

    // Check if tagihan-cat permissions are included
    $tagihanCatPermissions = array_filter($updatedPermissions, function($perm) {
        return strpos($perm, 'tagihan-cat') === 0;
    });

    echo "\nTagihan CAT permissions in user:\n";
    foreach ($tagihanCatPermissions as $perm) {
        echo "- {$perm}\n";
    }

    if (count($tagihanCatPermissions) > 0) {
        echo "\n✅ SUCCESS: Tagihan CAT permissions are properly saved!\n";
    } else {
        echo "\n❌ FAILED: No Tagihan CAT permissions found in user\n";
    }
} else {
    echo "❌ Admin user not found\n";
}

echo "\nTest completed!\n";
