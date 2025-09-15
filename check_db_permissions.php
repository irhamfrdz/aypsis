<?php

// Check database structure for permissions
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking Database Structure for Permissions\n";
echo "==============================================\n\n";

// Get all tables in database
$tables = DB::select('SHOW TABLES');
echo "📋 All tables in database:\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    echo "  - {$tableName}\n";
}

echo "\n🔍 Checking for permission-related tables:\n";
$permissionTables = [];
$allTables = array_map(function($table) {
    return array_values((array)$table)[0];
}, $tables);

$expectedTables = [
    'permissions',
    'model_has_permissions',
    'model_has_roles',
    'roles',
    'role_has_permissions'
];

foreach ($expectedTables as $expectedTable) {
    if (in_array($expectedTable, $allTables)) {
        echo "  ✅ {$expectedTable} - EXISTS\n";
        $permissionTables[] = $expectedTable;
    } else {
        echo "  ❌ {$expectedTable} - MISSING\n";
    }
}

echo "\n🔧 ANALYSIS:\n";
if (count($permissionTables) === 0) {
    echo "❌ No permission tables found. Spatie Laravel Permission package may not be installed or migrated.\n";
    echo "💡 SOLUTION: Run migrations for Spatie Laravel Permission\n";
    echo "   php artisan vendor:publish --provider=\"Spatie\\Permission\\PermissionServiceProvider\"\n";
    echo "   php artisan migrate\n";
} elseif (count($permissionTables) < count($expectedTables)) {
    echo "⚠️  Some permission tables are missing. This may cause permission system to not work properly.\n";
    echo "💡 SOLUTION: Check if all migrations have been run\n";
    echo "   php artisan migrate:status\n";
    echo "   php artisan migrate\n";
} else {
    echo "✅ All permission tables exist. The issue may be elsewhere.\n";
}

// Check if permissions table has data
if (in_array('permissions', $allTables)) {
    $permissionCount = DB::table('permissions')->count();
    echo "\n📊 Permissions table status:\n";
    echo "  Total permissions: {$permissionCount}\n";

    if ($permissionCount > 0) {
        echo "  Sample permissions:\n";
        $samplePerms = DB::table('permissions')->limit(10)->get();
        foreach ($samplePerms as $perm) {
            echo "    - {$perm->name} (ID: {$perm->id})\n";
        }
    } else {
        echo "  ❌ No permissions found in database\n";
        echo "  💡 SOLUTION: Create permissions using artisan commands or seeders\n";
    }
}

echo "\n🔧 RECOMMENDATIONS:\n";
echo "1. Check if Spatie Laravel Permission is installed: composer show | grep permission\n";
echo "2. Run migrations: php artisan migrate\n";
echo "3. Create permissions if needed\n";
echo "4. Clear cache: php artisan cache:clear\n";

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
