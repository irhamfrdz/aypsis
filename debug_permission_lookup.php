<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== DEBUG PERMISSION LOOKUP ===\n\n";

// Test lookup untuk master-karyawan permissions
$testCases = [
    'master-karyawan' => ['view', 'create', 'update', 'delete'],
    'master.karyawan' => ['index', 'create', 'edit', 'destroy']
];

foreach ($testCases as $module => $actions) {
    echo "Testing module: $module\n";
    foreach ($actions as $action) {
        $permissionName = $module . '.' . $action;
        $permission = Permission::where('name', $permissionName)->first();

        if ($permission) {
            echo "  ✅ $permissionName -> ID: {$permission->id}\n";
        } else {
            echo "  ❌ $permissionName -> NOT FOUND\n";
        }
    }
    echo "\n";
}

// Test dengan pola yang berbeda
echo "=== TESTING DIFFERENT PATTERNS ===\n";
$patterns = [
    'master.karyawan.index',
    'master.karyawan.create',
    'master.karyawan.edit',
    'master.karyawan.update',
    'master.karyawan.destroy',
    'master-karyawan-view',
    'master-karyawan-create',
    'master-karyawan-update',
    'master-karyawan-delete'
];

foreach ($patterns as $pattern) {
    $permission = Permission::where('name', $pattern)->first();
    if ($permission) {
        echo "✅ $pattern -> ID: {$permission->id}\n";
    } else {
        echo "❌ $pattern -> NOT FOUND\n";
    }
}

echo "\n=== DONE ===\n";
