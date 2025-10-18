<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Permission;

echo "🧪 Testing Operational Permission Conversion...\n\n";

try {
    // Create test matrix permissions for operational modules
    $testMatrixPermissions = [
        'order-management' => [
            'view' => '1',
            'create' => '1',
            'update' => '1'
        ],
        'surat-jalan' => [
            'view' => '1',
            'create' => '1'
        ],
        'tanda-terima' => [
            'view' => '1'
        ],
        'gate-in' => [
            'view' => '1',
            'create' => '1'
        ],
        'pranota-surat-jalan' => [
            'view' => '1'
        ],
        'approval-surat-jalan' => [
            'view' => '1',
            'approve' => '1'
        ]
    ];

    echo "📋 Test Matrix Permissions:\n";
    foreach ($testMatrixPermissions as $module => $actions) {
        echo "   • {$module}: " . implode(', ', array_keys(array_filter($actions))) . "\n";
    }
    echo "\n";

    // Create UserController instance and test conversion
    $controller = new UserController();
    $permissionIds = $controller->testConvertMatrixPermissionsToIds($testMatrixPermissions);

    echo "🔍 Converted Permission IDs: " . implode(', ', $permissionIds) . "\n\n";

    // Get permission names for the IDs
    $permissions = Permission::whereIn('id', $permissionIds)->get(['id', 'name']);
    
    echo "📝 Found Permissions:\n";
    foreach ($permissions as $permission) {
        echo "   • ID {$permission->id}: {$permission->name}\n";
    }

    echo "\n✅ Test completed successfully!\n";
    echo "   • Total permissions converted: " . count($permissionIds) . "\n";
    echo "   • Expected operational permissions: 9\n";
    
    if (count($permissionIds) === 9) {
        echo "   • ✅ All operational permissions converted correctly!\n";
    } else {
        echo "   • ⚠️  Some permissions may be missing or duplicated.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}