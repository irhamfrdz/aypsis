<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "🧪 Testing convertPermissionsToMatrix for Operational Modules\n";
echo "=========================================================\n\n";

$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

// Get admin's permission names
$adminPermissionNames = $admin->permissions()->pluck('name')->toArray();

echo "📊 Admin has " . count($adminPermissionNames) . " permissions\n\n";

// Find operational permissions
$operationalPermissions = array_filter($adminPermissionNames, function($name) {
    return strpos($name, 'order-management-') === 0 ||
           strpos($name, 'surat-jalan-') === 0 ||
           strpos($name, 'tanda-terima-') === 0 ||
           strpos($name, 'gate-in-') === 0 ||
           strpos($name, 'pranota-surat-jalan-') === 0 ||
           strpos($name, 'approval-surat-jalan-') === 0;
});

echo "🔧 Found " . count($operationalPermissions) . " operational permissions:\n";
foreach($operationalPermissions as $perm) {
    echo "   - $perm\n";
}

echo "\n🧪 Testing convertPermissionsToMatrix method...\n";

// Test convertPermissionsToMatrix method
$controller = new UserController();
$matrixPermissions = $controller->testConvertPermissionsToMatrix($adminPermissionNames);

echo "\n📊 Matrix conversion results:\n";

$operationalModules = [
    'order-management',
    'surat-jalan',
    'tanda-terima',
    'gate-in',
    'pranota-surat-jalan',
    'approval-surat-jalan'
];

foreach ($operationalModules as $module) {
    if (isset($matrixPermissions[$module])) {
        echo "✅ Module: $module\n";
        foreach ($matrixPermissions[$module] as $action => $value) {
            $status = $value ? '✅' : '❌';
            echo "   $status $action: " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "❌ Module: $module (NOT FOUND in matrix)\n";
    }
    echo "\n";
}

echo "🎯 Summary:\n";
$foundModules = array_intersect($operationalModules, array_keys($matrixPermissions));
echo "✅ Found modules: " . count($foundModules) . "/" . count($operationalModules) . "\n";
echo "✅ Modules: " . implode(', ', $foundModules) . "\n";

if (count($foundModules) === count($operationalModules)) {
    echo "🎉 SUCCESS: All operational modules are properly converted to matrix!\n";
} else {
    echo "❌ ISSUE: Some operational modules are missing from matrix conversion\n";
}

?>
