<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding Master Jenis Layanan Pelabuhan Permissions ===\n\n";

$permissions = [
    'master-jenis-layanan-pelabuhan-view',
    'master-jenis-layanan-pelabuhan-create',
    'master-jenis-layanan-pelabuhan-edit',
    'master-jenis-layanan-pelabuhan-delete',
];

$createdCount = 0;
$existingCount = 0;

foreach ($permissions as $permissionName) {
    $existing = DB::table('permissions')->where('name', $permissionName)->first();
    
    if (!$existing) {
        DB::table('permissions')->insert([
            'name' => $permissionName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Created permission: {$permissionName}\n";
        $createdCount++;
    } else {
        echo "• Permission already exists: {$permissionName}\n";
        $existingCount++;
    }
}

echo "\n=== Assigning Permissions to Admin Role ===\n";

$adminRole = DB::table('roles')->where('name', 'admin')->first();

if ($adminRole) {
    $rolePermissionTable = DB::select("SHOW TABLES LIKE 'role_has_permissions'");
    
    if (empty($rolePermissionTable)) {
        // Try alternative table name
        $rolePermissionTable = DB::select("SHOW TABLES LIKE 'permission_role'");
        
        if (empty($rolePermissionTable)) {
            echo "⚠ Warning: Could not find role-permission pivot table.\n";
            echo "   Permissions created but not assigned to any role.\n";
            echo "   Please assign manually or check your database schema.\n";
        } else {
            assignPermissionsToRole($adminRole, $permissions, 'permission_role');
        }
    } else {
        assignPermissionsToRole($adminRole, $permissions, 'role_has_permissions');
    }
} else {
    echo "⚠ Warning: Admin role not found. Permissions created but not assigned to any role.\n";
}

function assignPermissionsToRole($adminRole, $permissions, $tableName) {
    foreach ($permissions as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if ($permission) {
            $hasPermission = DB::table($tableName)
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $permission->id)
                ->exists();
            
            if (!$hasPermission) {
                DB::table($tableName)->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id,
                ]);
                echo "✓ Assigned '{$permissionName}' to admin role\n";
            } else {
                echo "• Admin already has '{$permissionName}'\n";
            }
        }
    }
    echo "\n✓ All permissions assigned to admin role successfully!\n";
}

echo "\n=== Summary ===\n";
echo "Created: {$createdCount} permissions\n";
echo "Already existed: {$existingCount} permissions\n";
echo "Total: " . count($permissions) . " permissions\n";
echo "\nDone! ✓\n";
