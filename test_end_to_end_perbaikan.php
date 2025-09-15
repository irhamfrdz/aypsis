<?php
// Test end-to-end perbaikan-kontainer permission matrix display
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

echo "=== END-TO-END TEST: PERBAIKAN-KONTAINER PERMISSION MATRIX ===\n\n";

// Get a test user (let's use user ID 1)
$user = \App\Models\User::with('permissions')->find(1);

if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "Test User: {$user->username} (ID: {$user->id})\n";
echo "User has " . $user->permissions->count() . " permissions\n\n";

// Test convertPermissionsToMatrix method
$controller = new \App\Http\Controllers\UserController();
$reflection = new ReflectionClass($controller);

// Get convertPermissionsToMatrix method
$convertToMatrixMethod = $reflection->getMethod('convertPermissionsToMatrix');
$convertToMatrixMethod->setAccessible(true);

// Get user's permission names
$userPermissionNames = $user->permissions->pluck('name')->toArray();
echo "User permission names:\n";
foreach ($userPermissionNames as $perm) {
    echo "- {$perm}\n";
}

echo "\n=== TESTING convertPermissionsToMatrix ===\n";
$userMatrixPermissions = $convertToMatrixMethod->invoke($controller, $userPermissionNames);

echo "Matrix permissions result:\n";
print_r($userMatrixPermissions);

echo "\n=== CHECKING PERBAIKAN-KONTAINER IN MATRIX ===\n";
if (isset($userMatrixPermissions['perbaikan-kontainer'])) {
    echo "✅ perbaikan-kontainer found in matrix!\n";
    echo "Actions: " . implode(', ', array_keys($userMatrixPermissions['perbaikan-kontainer'])) . "\n";

    foreach ($userMatrixPermissions['perbaikan-kontainer'] as $action => $enabled) {
        echo "- {$action}: " . ($enabled ? 'ENABLED' : 'DISABLED') . "\n";
    }
} else {
    echo "❌ perbaikan-kontainer NOT found in matrix\n";
    echo "Available modules in matrix: " . implode(', ', array_keys($userMatrixPermissions)) . "\n";
}

echo "\n=== TESTING convertMatrixPermissionsToIds (ROUND-TRIP) ===\n";

// Get convertMatrixPermissionsToIds method
$convertToIdsMethod = $reflection->getMethod('convertMatrixPermissionsToIds');
$convertToIdsMethod->setAccessible(true);

// Test round-trip conversion
$permissionIds = $convertToIdsMethod->invoke($controller, $userMatrixPermissions);

echo "Round-trip permission IDs: " . implode(', ', $permissionIds) . "\n";

if (!empty($permissionIds)) {
    $permissions = DB::table('permissions')
        ->whereIn('id', $permissionIds)
        ->pluck('name', 'id');

    echo "Round-trip permission names:\n";
    foreach ($permissions as $id => $name) {
        echo "- ID {$id}: {$name}\n";
    }
}

echo "\n=== CONCLUSION ===\n";
if (isset($userMatrixPermissions['perbaikan-kontainer'])) {
    echo "✅ SUCCESS: perbaikan-kontainer permission matrix should be visible in user edit form\n";
} else {
    echo "❌ FAILURE: perbaikan-kontainer permission matrix will not be visible\n";
}
