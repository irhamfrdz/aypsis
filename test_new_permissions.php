<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;
use App\Models\Permission;

try {
    echo "🧪 Testing UserController permission handling for new modules...\n\n";

    $controller = new UserController();

    // Test sample permissions conversion to matrix
    $testPermissions = [
        'pembayaran-uang-muka-view',
        'pembayaran-uang-muka-create',
        'pembayaran-uang-muka-update',
        'pembayaran-uang-muka-delete',
        'pembayaran-uang-muka-print',
        'realisasi-uang-muka-view',
        'realisasi-uang-muka-create',
        'realisasi-uang-muka-approve',
        'pembayaran-ob-view',
        'pembayaran-ob-create',
        'pembayaran-ob-export'
    ];

    echo "🔍 Testing convertPermissionsToMatrix...\n";
    $matrixResult = $controller->testConvertPermissionsToMatrix($testPermissions);

    echo "✅ Matrix conversion results:\n";
    foreach ($matrixResult as $module => $actions) {
        echo "   📁 $module:\n";
        foreach ($actions as $action => $value) {
            echo "      • $action: " . ($value ? 'YES' : 'NO') . "\n";
        }
        echo "\n";
    }

    // Test converting matrix back to IDs
    echo "🔄 Testing convertMatrixPermissionsToIds...\n";
    $permissionIds = $controller->testConvertMatrixPermissionsToIds($matrixResult);

    echo "✅ Converted to " . count($permissionIds) . " permission IDs\n";

    // Verify each permission ID exists
    $validPermissions = 0;
    $missingPermissions = [];

    foreach ($permissionIds as $id) {
        $permission = Permission::find($id);
        if ($permission) {
            $validPermissions++;
            echo "   ✓ {$permission->name} (ID: $id)\n";
        } else {
            $missingPermissions[] = $id;
            echo "   ❌ Missing permission ID: $id\n";
        }
    }

    echo "\n📊 Summary:\n";
    echo "   • Valid permissions: $validPermissions\n";
    echo "   • Missing permissions: " . count($missingPermissions) . "\n";

    if (count($missingPermissions) === 0) {
        echo "\n🎉 SUCCESS! All permission mappings work correctly.\n";
    } else {
        echo "\n⚠️  Some permission IDs are missing from database.\n";
    }

    // Check if the actual permissions exist in database
    echo "\n🔍 Checking if required permissions exist in database...\n";

    $requiredModules = ['pembayaran-uang-muka', 'realisasi-uang-muka', 'pembayaran-ob'];
    $requiredActions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

    $missingFromDb = [];

    foreach ($requiredModules as $module) {
        echo "   📁 Checking $module permissions:\n";
        foreach ($requiredActions as $action) {
            $permissionName = "$module-$action";
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                echo "      ✓ $permissionName (ID: {$permission->id})\n";
            } else {
                echo "      ❌ $permissionName - NOT FOUND\n";
                $missingFromDb[] = $permissionName;
            }
        }
        echo "\n";
    }

    if (count($missingFromDb) > 0) {
        echo "⚠️  Missing permissions from database:\n";
        foreach ($missingFromDb as $missing) {
            echo "   • $missing\n";
        }
        echo "\nℹ️  You may need to create these permissions first.\n";
    } else {
        echo "🎉 All required permissions exist in database!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
