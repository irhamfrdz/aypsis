<?php
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing User Permission Storage for tagihan-kontainer:\n";
echo "=====================================================\n";

// Create a test user
$testUser = User::create([
    'username' => 'test_user_' . time(),
    'password' => bcrypt('password123'),
    'karyawan_id' => null
]);

echo "Created test user: {$testUser->username} (ID: {$testUser->id})\n";

// Simulate the permission IDs that would be returned by convertMatrixPermissionsToIds
$permissionIds = [265, 266, 267, 268, 269, 270, 271]; // tagihan-kontainer permissions

// Attach permissions to user
$testUser->permissions()->sync($permissionIds);

echo "Attached permissions to user\n";

// Verify permissions were attached
$userPermissions = $testUser->permissions()->get();
echo "User now has " . $userPermissions->count() . " permissions:\n";

foreach ($userPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

// Test permission checking
echo "\nTesting permission checks:\n";
$permissionsToCheck = [
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'tagihan-kontainer-delete',
    'tagihan-kontainer-approve',
    'tagihan-kontainer-print',
    'tagihan-kontainer-export'
];

foreach ($permissionsToCheck as $permName) {
    $hasPermission = $testUser->hasPermissionTo($permName);
    echo "- {$permName}: " . ($hasPermission ? 'YES' : 'NO') . "\n";
}

// Clean up
$testUser->delete();
echo "\nCleaned up test user\n";
?>
