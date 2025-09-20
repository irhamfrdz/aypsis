<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "Checking Tagihan CAT permissions in database:\n";
echo "===========================================\n";

$tagihanCatPermissions = Permission::where('name', 'like', 'tagihan-cat%')->get();

if ($tagihanCatPermissions->count() > 0) {
    echo "Found " . $tagihanCatPermissions->count() . " Tagihan CAT permissions:\n";
    foreach ($tagihanCatPermissions as $perm) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "❌ No Tagihan CAT permissions found in database!\n";
}

echo "\nChecking if admin user has Tagihan CAT permissions:\n";
echo "================================================\n";

$adminUser = User::where('username', 'admin')->first();
if ($adminUser) {
    $userPermissions = $adminUser->permissions()->where('name', 'like', 'tagihan-cat%')->get();
    echo "Admin user has " . $userPermissions->count() . " Tagihan CAT permissions:\n";
    foreach ($userPermissions as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "❌ Admin user not found!\n";
}

echo "\nTesting permission matrix conversion:\n";
echo "=====================================\n";

$userController = new \App\Http\Controllers\UserController();

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

$permissionIds = $userController->testConvertMatrixPermissionsToIds($testMatrixPermissions);

echo "\nConverted permission IDs: " . implode(', ', $permissionIds) . "\n";

if (!empty($permissionIds)) {
    $permissions = Permission::whereIn('id', $permissionIds)->get();
    echo "\nPermission names:\n";
    foreach ($permissions as $perm) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
    echo "\n✅ SUCCESS: Permission conversion is working!\n";
} else {
    echo "\n❌ FAILED: No permission IDs found!\n";
}
