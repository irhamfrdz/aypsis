<?php

require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "Testing pembayaran-pranota-cat permission conversion...\n";
echo "=====================================================\n";

// Simulate the permission matrix input
$permissionsMatrix = [
    'pembayaran-pranota-cat' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
        'print' => '1',
        'export' => '1'
    ]
];

// Create UserController instance
$userController = new UserController();

// Call the convertMatrixPermissionsToIds method
try {
    $reflection = new ReflectionClass($userController);
    $method = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method->setAccessible(true);

    $permissionIds = $method->invoke($userController, $permissionsMatrix);

    echo "✅ Conversion successful!\n";
    echo "Permission IDs found: " . count($permissionIds) . "\n";

    if (count($permissionIds) > 0) {
        echo "Permission details:\n";
        foreach ($permissionIds as $id) {
            $permission = Permission::find($id);
            if ($permission) {
                echo "- {$permission->name} (ID: {$permission->id})\n";
            }
        }
    } else {
        echo "❌ No permission IDs returned\n";
    }

} catch (Exception $e) {
    echo "❌ Error during conversion: " . $e->getMessage() . "\n";
}
