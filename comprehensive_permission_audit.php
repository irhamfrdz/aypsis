<?php

// Comprehensive Permission Audit Script
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Http\Controllers\UserController;

echo "=== COMPREHENSIVE PERMISSION AUDIT ===\n\n";

// 1. Get all permissions from database
echo "1. FETCHING ALL PERMISSIONS FROM DATABASE\n";
$allPermissions = Permission::orderBy('name')->get();
echo "   Total permissions in database: " . $allPermissions->count() . "\n\n";

// 2. Test convertPermissionsToMatrix with all permissions
echo "2. TESTING convertPermissionsToMatrix WITH ALL PERMISSIONS\n";
$userController = new UserController();
$allPermissionNames = $allPermissions->pluck('name')->toArray();

try {
    $matrixResult = $userController->testConvertPermissionsToMatrix($allPermissionNames);
    echo "   Matrix conversion successful. Found " . count($matrixResult) . " modules.\n\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 3. Identify permissions NOT covered by matrix system
echo "3. IDENTIFYING PERMISSIONS NOT COVERED BY MATRIX SYSTEM\n";
$coveredPermissions = [];

// Collect all permissions that are represented in the matrix
foreach ($matrixResult as $module => $actions) {
    if (is_array($actions)) {
        foreach ($actions as $action => $state) {
            // Try to reverse-engineer the permission name from module and action
            $possibleNames = generatePossiblePermissionNames($module, $action);
            foreach ($possibleNames as $possibleName) {
                if (in_array($possibleName, $allPermissionNames)) {
                    $coveredPermissions[] = $possibleName;
                }
            }
        }
    }
}

$coveredPermissions = array_unique($coveredPermissions);
$uncoveredPermissions = array_diff($allPermissionNames, $coveredPermissions);

echo "   Permissions covered by matrix: " . count($coveredPermissions) . "\n";
echo "   Permissions NOT covered: " . count($uncoveredPermissions) . "\n\n";

// 4. List uncovered permissions by category
if (!empty($uncoveredPermissions)) {
    echo "4. UNCOVERED PERMISSIONS (CANNOT BE MANAGED VIA USER MENU):\n";
    
    // Group by prefix/category
    $categorized = [];
    foreach ($uncoveredPermissions as $permission) {
        $prefix = getPermissionPrefix($permission);
        if (!isset($categorized[$prefix])) {
            $categorized[$prefix] = [];
        }
        $categorized[$prefix][] = $permission;
    }
    
    foreach ($categorized as $category => $permissions) {
        echo "   \n   📁 {$category}:\n";
        foreach ($permissions as $permission) {
            echo "      ❌ {$permission}\n";
        }
    }
    
    echo "\n";
} else {
    echo "4. ✅ ALL PERMISSIONS ARE COVERED BY MATRIX SYSTEM!\n\n";
}

// 5. Test matrix-to-IDs conversion for all modules
echo "5. TESTING MATRIX-TO-IDS CONVERSION\n";
$testMatrix = [];
foreach ($matrixResult as $module => $actions) {
    if (is_array($actions)) {
        $testMatrix[$module] = [];
        foreach ($actions as $action => $state) {
            $testMatrix[$module][$action] = '1'; // Simulate all checked
        }
    }
}

try {
    $convertedIds = $userController->testConvertMatrixPermissionsToIds($testMatrix);
    echo "   Matrix-to-IDs conversion successful. Generated " . count($convertedIds) . " permission IDs.\n";
    
    // Check how many permissions can be roundtrip converted
    $roundtripPermissions = Permission::whereIn('id', $convertedIds)->pluck('name')->toArray();
    $roundtripCoverage = count($roundtripPermissions);
    
    echo "   Roundtrip coverage: {$roundtripCoverage} / " . $allPermissions->count() . " permissions\n";
    
    $notRoundtrip = array_diff($allPermissionNames, $roundtripPermissions);
    if (!empty($notRoundtrip)) {
        echo "   Permissions that cannot roundtrip (form cannot save them):\n";
        foreach ($notRoundtrip as $permission) {
            echo "      🔄 {$permission}\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ERROR in matrix-to-IDs conversion: " . $e->getMessage() . "\n";
}

echo "\n=== AUDIT COMPLETE ===\n";

// Helper functions
function generatePossiblePermissionNames($module, $action) {
    $possibilities = [];
    
    // Direct mapping
    $possibilities[] = $module . '-' . $action;
    
    // Handle special module mappings
    if ($module === 'pranota-rit') {
        $possibilities[] = 'pranota-rit-' . $action;
    }
    
    if ($module === 'pranota-rit-kenek') {
        $possibilities[] = 'pranota-rit-kenek-' . $action;
    }
    
    // Handle master modules
    if (strpos($module, 'master-') === 0) {
        $possibilities[] = $module . '-' . $action;
        
        // Dot notation for master modules
        $masterPart = str_replace('master-', '', $module);
        $possibilities[] = 'master.' . $masterPart . '.' . $action;
    }
    
    // Handle action mappings
    $actionMappings = [
        'update' => ['edit', 'update'],
        'delete' => ['delete', 'destroy'],
        'view' => ['view', 'index', 'show']
    ];
    
    if (isset($actionMappings[$action])) {
        foreach ($actionMappings[$action] as $altAction) {
            $possibilities[] = $module . '-' . $altAction;
            if (strpos($module, 'master-') === 0) {
                $masterPart = str_replace('master-', '', $module);
                $possibilities[] = 'master.' . $masterPart . '.' . $altAction;
            }
        }
    }
    
    return array_unique($possibilities);
}

function getPermissionPrefix($permission) {
    // Extract meaningful prefix for categorization
    $parts = explode('-', $permission);
    
    if (count($parts) >= 2) {
        if ($parts[0] === 'master') {
            return 'master-' . ($parts[1] ?? '');
        } else {
            return $parts[0] . '-' . ($parts[1] ?? '');
        }
    }
    
    if (strpos($permission, '.') !== false) {
        $parts = explode('.', $permission);
        if (count($parts) >= 2) {
            return $parts[0] . '.' . $parts[1];
        }
    }
    
    return 'other';
}