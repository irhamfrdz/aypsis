<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== TABLE EXISTENCE CHECK ===\n\n";

$tables = [
    'permissions',
    'user_has_permissions',
    'role_has_permissions',
    'roles',
    'model_has_permissions',
    'model_has_roles'
];

foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "- $table: " . ($exists ? "✅ EXISTS" : "❌ NOT EXISTS") . "\n";
}

echo "\n=== PERMISSIONS COUNT ===\n";
try {
    $permissionCount = \Illuminate\Support\Facades\DB::table('permissions')->count();
    echo "Total permissions: $permissionCount\n";
} catch (Exception $e) {
    echo "Error counting permissions: " . $e->getMessage() . "\n";
}

echo "\n=== USER PERMISSIONS COUNT ===\n";
try {
    $userPermissionCount = \Illuminate\Support\Facades\DB::table('user_has_permissions')->count();
    echo "Total user permissions: $userPermissionCount\n";
} catch (Exception $e) {
    echo "Error counting user permissions: " . $e->getMessage() . "\n";
}
