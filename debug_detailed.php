<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Detailed Debugging of convertPermissionsToMatrix\n";
echo "==============================================\n\n";

$userController = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$method = $reflection->getMethod('convertPermissionsToMatrix');
$method->setAccessible(true);

$testPermissions = [
    'dashboard-view',
    'master-karyawan',
    'master.karyawan.index',
    'master-user-view',
    'pembayaran.create',
    'laporan.export'
];

echo "Input permissions:\n";
print_r($testPermissions);
echo "\n";

$matrixResult = $method->invoke($userController, $testPermissions);

echo "Matrix result:\n";
print_r($matrixResult);
echo "\n";

// Manual processing to debug
echo "Manual processing:\n";
$manualMatrix = [];

foreach ($testPermissions as $index => $permissionName) {
    echo "\nProcessing permission #$index: $permissionName\n";
    echo "  Contains dot: " . (strpos($permissionName, '.') !== false ? 'YES' : 'NO') . "\n";
    echo "  Contains dash: " . (strpos($permissionName, '-') !== false ? 'YES' : 'NO') . "\n";

    // Pattern 1: dot notation
    if (strpos($permissionName, '.') !== false) {
        echo "  → Processing as DOT notation\n";
        $parts = explode('.', $permissionName);
        echo "    Parts: " . implode(', ', $parts) . "\n";

        if (count($parts) >= 3 && $parts[0] === 'master') {
            $module = $parts[0] . '-' . $parts[1];
            $action = $parts[2];
            echo "    ✓ Master dot notation: module=$module, action=$action\n";

            if (!isset($manualMatrix[$module])) {
                $manualMatrix[$module] = [];
            }
            $manualMatrix[$module][$action] = true;
            echo "    Added to manual matrix\n";
            continue;
        } elseif (count($parts) >= 2) {
            $module = $parts[0];
            $action = $parts[1];
            echo "    ✓ Generic dot notation: module=$module, action=$action\n";

            if (!isset($manualMatrix[$module])) {
                $manualMatrix[$module] = [];
            }
            $manualMatrix[$module][$action] = true;
            echo "    Added to manual matrix\n";
            continue;
        }
    }

    // Pattern 2: dash notation
    if (strpos($permissionName, '-') !== false) {
        echo "  → Processing as DASH notation\n";
        $parts = explode('-', $permissionName, 2);
        echo "    Parts: " . implode(', ', $parts) . "\n";

        if (count($parts) == 2) {
            $module = $parts[0];
            $action = $parts[1];
            echo "    ✓ Dash notation: module=$module, action=$action\n";

            if (!isset($manualMatrix[$module])) {
                $manualMatrix[$module] = [];
            }
            $manualMatrix[$module][$action] = true;
            echo "    Added to manual matrix\n";
            continue;
        }
    }

    // Pattern 3: simple
    if (strpos($permissionName, '-') === false && strpos($permissionName, '.') === false) {
        echo "  → Processing as SIMPLE notation\n";
        $module = $permissionName;
        echo "    ✓ Simple notation: module=$module\n";

        if (!isset($manualMatrix[$module])) {
            $manualMatrix[$module] = [];
        }
        $manualMatrix[$module]['view'] = true;
        echo "    Added to manual matrix\n";
    } else {
        echo "  → SKIPPED (already processed or invalid)\n";
    }
}

echo "\nManual matrix result:\n";
print_r($manualMatrix);
