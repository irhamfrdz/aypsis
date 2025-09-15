<?php
// Debug convertMatrixPermissionsToIds for perbaikan-kontainer with detailed logging
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConfig = [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'aypsis',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];

$db = new DB;
$db->addConnection($dbConfig);
$db->setAsGlobal();
$db->bootEloquent();

echo "=== DEBUGGING convertMatrixPermissionsToIds FOR PERBAIKAN-KONTAINER ===\n\n";

// Simulate the matrix data that would come from the form
$testMatrixData = [
    'perbaikan-kontainer' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1'
    ]
];

echo "Test matrix data:\n";
print_r($testMatrixData);

// Manually implement the convertMatrixPermissionsToIds logic with debug output
$permissionIds = [];
$actionMap = [
    'view' => ['index', 'show', 'view'],
    'create' => ['create', 'store'],
    'update' => ['edit', 'update'],
    'delete' => ['destroy', 'delete'],
    'print' => ['print'],
    'export' => ['export'],
    'import' => ['import'],
    'approve' => ['approve'],
    'access' => ['access'],
    'main' => ['main']
];

foreach ($testMatrixData as $module => $actions) {
    echo "\n--- Processing module: {$module} ---\n";

    if (!is_array($actions)) {
        echo "Skipping module {$module} - actions is not an array\n";
        continue;
    }

    foreach ($actions as $action => $value) {
        echo "Processing action: {$action} = {$value}\n";

        if ($value != '1' && $value !== true) {
            echo "Skipping action {$action} - value is not 1 or true\n";
            continue;
        }

        $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];
        echo "Possible database actions for '{$action}': " . implode(', ', $possibleActions) . "\n";

        $found = false;

        // Special handling for perbaikan-kontainer module
        if ($module === 'perbaikan-kontainer') {
            echo "🎯 SPECIAL HANDLING: perbaikan-kontainer detected\n";
            foreach ($possibleActions as $dbAction) {
                $permissionName = 'perbaikan-kontainer.' . $dbAction;
                echo "  Looking for permission: {$permissionName}\n";
                $permission = DB::table('permissions')->where('name', $permissionName)->first();

                if ($permission) {
                    echo "  ✅ Found permission: {$permissionName} (ID: {$permission->id})\n";
                    $permissionIds[] = $permission->id;
                    $found = true;
                    break;
                } else {
                    echo "  ❌ Permission not found: {$permissionName}\n";
                }
            }
        }

        if (!$found) {
            echo "No permission found for action {$action} in module {$module}\n";
        }
    }
}

echo "\n=== FINAL RESULT ===\n";
echo "Permission IDs: " . implode(', ', $permissionIds) . "\n";

if (!empty($permissionIds)) {
    $permissions = DB::table('permissions')
        ->whereIn('id', $permissionIds)
        ->pluck('name', 'id');

    echo "Permission names:\n";
    foreach ($permissions as $id => $name) {
        echo "- ID {$id}: {$name}\n";
    }
}
