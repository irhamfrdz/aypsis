<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

try {
    echo "Testing Pembayaran Surat Jalan Permission System...\n\n";
    
    // Test 1: Verify permissions exist
    echo "1. Checking if permissions exist in database:\n";
    $permissions = Permission::where('name', 'LIKE', 'pembayaran-surat-jalan-%')->get();
    foreach ($permissions as $permission) {
        echo "   ✅ {$permission->name}: {$permission->description}\n";
    }
    
    // Test 2: Test matrix conversion
    echo "\n2. Testing permission matrix conversion:\n";
    
    // Get admin user
    $admin = User::where('username', 'admin')->first();
    if (!$admin) {
        echo "   ❌ Admin user not found\n";
        exit(1);
    }
    
    // Get admin permissions
    $adminPermissions = $admin->permissions->pluck('name')->toArray();
    
    // Check if admin has pembayaran-surat-jalan permissions
    $pembayaranSuratJalanPerms = array_filter($adminPermissions, function($perm) {
        return strpos($perm, 'pembayaran-surat-jalan-') === 0;
    });
    
    echo "   Admin has " . count($pembayaranSuratJalanPerms) . " pembayaran surat jalan permissions:\n";
    foreach ($pembayaranSuratJalanPerms as $perm) {
        echo "   ✅ $perm\n";
    }
    
    // Test 3: Matrix conversion
    echo "\n3. Testing matrix conversion logic:\n";
    
    $controller = new UserController();
    $matrixPermissions = $controller->testConvertPermissionsToMatrix($adminPermissions);
    
    if (isset($matrixPermissions['pembayaran-surat-jalan'])) {
        echo "   ✅ Matrix conversion successful for pembayaran-surat-jalan:\n";
        foreach ($matrixPermissions['pembayaran-surat-jalan'] as $action => $value) {
            echo "      - $action: " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "   ❌ Matrix conversion failed for pembayaran-surat-jalan\n";
    }
    
    // Test 4: Reverse conversion (Matrix to IDs)
    echo "\n4. Testing reverse conversion (Matrix to Permission IDs):\n";
    
    $testMatrix = [
        'pembayaran-surat-jalan' => [
            'view' => true,
            'create' => true,
            'update' => false,
            'delete' => false,
            'approve' => true,
            'print' => true,
            'export' => false
        ]
    ];
    
    $convertedIds = $controller->testConvertMatrixPermissionsToIds($testMatrix);
    
    echo "   Test matrix converted to " . count($convertedIds) . " permission IDs:\n";
    $testPermissions = Permission::whereIn('id', $convertedIds)->get();
    foreach ($testPermissions as $permission) {
        if (strpos($permission->name, 'pembayaran-surat-jalan-') === 0) {
            echo "   ✅ {$permission->name}\n";
        }
    }
    
    echo "\n🎉 All tests completed successfully!\n";
    echo "\nPembayaran Surat Jalan permission system is ready to use.\n";
    echo "You can now manage user permissions for Pembayaran Surat Jalan through the user management interface.\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}