<?php
// Script to check perbaikan-kontainer permissions in database
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

echo "=== CHECKING PERBAIKAN-KONTAINER PERMISSIONS ===\n\n";

// Check all perbaikan-kontainer permissions
$permissions = DB::table('permissions')
    ->where('name', 'like', '%perbaikan-kontainer%')
    ->orderBy('name')
    ->get();

echo "Found " . count($permissions) . " perbaikan-kontainer permissions:\n";
foreach ($permissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\n=== CHECKING SPECIFIC PERMISSION FORMATS ===\n";

// Check specific formats that the system might look for
$formats = [
    'perbaikan-kontainer.view',
    'perbaikan-kontainer.create',
    'perbaikan-kontainer.update',
    'perbaikan-kontainer.delete',
    'perbaikan-kontainer-view',
    'perbaikan-kontainer-create',
    'perbaikan-kontainer-update',
    'perbaikan-kontainer-delete',
    'perbaikan.kontainer.view',
    'perbaikan.kontainer.create',
    'perbaikan.kontainer.update',
    'perbaikan.kontainer.delete',
];

foreach ($formats as $format) {
    $perm = DB::table('permissions')->where('name', $format)->first();
    if ($perm) {
        echo "✅ Found: {$format} (ID: {$perm->id})\n";
    } else {
        echo "❌ Missing: {$format}\n";
    }
}

echo "\n=== CHECKING USER PERMISSIONS ASSIGNMENT ===\n";

// Check if any users have perbaikan-kontainer permissions
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('permissions.name', 'like', '%perbaikan-kontainer%')
    ->select('user_permissions.user_id', 'permissions.name')
    ->get();

if (count($userPermissions) > 0) {
    echo "Users with perbaikan-kontainer permissions:\n";
    foreach ($userPermissions as $up) {
        echo "- User ID {$up->user_id}: {$up->name}\n";
    }
} else {
    echo "❌ No users have perbaikan-kontainer permissions assigned\n";
}
