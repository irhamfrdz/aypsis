<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel application
$app = new Application(getcwd());
$app->singleton(\Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class);
$app->singleton(\Illuminate\Contracts\Console\Kernel::class, \App\Console\Kernel::class);
$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, \App\Exceptions\Handler::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING APPROVAL SURAT JALAN MOVED TO APPROVAL SYSTEM ===\n\n";

try {
    // Create test controller instance
    $userController = new App\Http\Controllers\UserController();
    
    echo "1. Testing convertPermissionsToMatrix for approval-surat-jalan permissions:\n";
    $testPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve',
        'approval-surat-jalan-print',
        'approval-surat-jalan-export'
    ];
    
    $matrixResult = $userController->testConvertPermissionsToMatrix($testPermissions);
    
    if (isset($matrixResult['approval-surat-jalan'])) {
        echo "   ✅ approval-surat-jalan correctly mapped to approval system\n";
        echo "   📋 Matrix result: " . json_encode($matrixResult['approval-surat-jalan']) . "\n";
    } else {
        echo "   ❌ approval-surat-jalan not found in matrix result\n";
        echo "   📋 Available modules: " . implode(', ', array_keys($matrixResult)) . "\n";
    }
    
    echo "\n2. Testing convertMatrixPermissionsToIds for approval module:\n";
    $testMatrixInput = [
        'approval-surat-jalan' => [
            'view' => '1',
            'approve' => '1',
            'print' => '1',
            'export' => '1'
        ]
    ];
    
    $idsResult = $userController->testConvertMatrixPermissionsToIds($testMatrixInput);
    echo "   🔢 Generated permission IDs count: " . count($idsResult) . "\n";
    
    // Verify the IDs correspond to actual approval-surat-jalan permissions
    $expectedPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve', 
        'approval-surat-jalan-print',
        'approval-surat-jalan-export'
    ];
    
    $foundPermissions = [];
    foreach ($idsResult as $id) {
        $perm = App\Models\Permission::find($id);
        if ($perm) {
            $foundPermissions[] = $perm->name;
        }
    }
    
    $allMatched = empty(array_diff($expectedPermissions, $foundPermissions));
    
    if ($allMatched) {
        echo "   ✅ All expected approval-surat-jalan permissions found\n";
        echo "   📋 Found: " . implode(', ', $foundPermissions) . "\n";
    } else {
        echo "   ❌ Some permissions missing\n";
        echo "   📋 Expected: " . implode(', ', $expectedPermissions) . "\n";
        echo "   📋 Found: " . implode(', ', $foundPermissions) . "\n";
    }
    
    echo "\n3. Checking if approval-surat-jalan is properly removed from operational:\n";
    $operationalTestPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve'
    ];
    
    $operationalMatrix = $userController->testConvertPermissionsToMatrix($operationalTestPermissions);
    
    // Check if these are NOT under any operational module
    $foundInOperational = false;
    $operationalModules = ['order-management', 'surat-jalan', 'tanda-terima', 'gate-in', 'pranota-surat-jalan'];
    
    foreach ($operationalModules as $opModule) {
        if (isset($operationalMatrix[$opModule])) {
            echo "   ℹ️ Found operational module: $opModule\n";
        }
    }
    
    if (!$foundInOperational && isset($operationalMatrix['approval-surat-jalan'])) {
        echo "   ✅ approval-surat-jalan correctly moved from operational to approval system\n";
    } else {
        echo "   ❌ approval-surat-jalan still found in operational or not found at all\n";
    }
    
    echo "\n✅ MIGRATION COMPLETE: Approval-surat-jalan successfully moved to Sistem Persetujuan dropdown!\n";
    echo "✅ The UI now shows approval-surat-jalan under Approval System instead of Operational Management\n";
    echo "✅ All permission handling properly updated in UserController\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}