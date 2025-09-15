<?php
// Test convertMatrixPermissionsToIds for perbaikan-kontainer
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

echo "=== TESTING convertMatrixPermissionsToIds FOR PERBAIKAN-KONTAINER ===\n\n";

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

echo "\n=== TESTING convertMatrixPermissionsToIds METHOD ===\n";

// Use reflection to access the private method
$controller = new \App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);

// Call the method
$permissionIds = $method->invoke($controller, $testMatrixData);

echo "Permission IDs returned: ";
print_r($permissionIds);

echo "\n=== VERIFYING PERMISSION NAMES ===\n";

// Get permission names for the IDs
if (!empty($permissionIds)) {
    $permissions = DB::table('permissions')
        ->whereIn('id', $permissionIds)
        ->pluck('name', 'id');

    echo "Permission names for returned IDs:\n";
    foreach ($permissions as $id => $name) {
        echo "- ID {$id}: {$name}\n";
    }
} else {
    echo "❌ No permission IDs returned\n";
}

echo "\n=== MANUAL VERIFICATION ===\n";

// Manually check what permissions should be found
$expectedPermissions = [
    'perbaikan-kontainer.view',
    'perbaikan-kontainer.create',
    'perbaikan-kontainer.update',
    'perbaikan-kontainer.delete'
];

foreach ($expectedPermissions as $permName) {
    $perm = DB::table('permissions')->where('name', $permName)->first();
    if ($perm) {
        echo "✅ Expected permission found: {$permName} (ID: {$perm->id})\n";
    } else {
        echo "❌ Expected permission missing: {$permName}\n";
    }
}
