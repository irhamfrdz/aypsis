<?php
/**
 * Script untuk menambah vendor kontainer sewa permissions ke sistem permission kustom
 * Compatible dengan permission table yang sudah ada (tanpa guard_name)
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Setup Vendor Kontainer Sewa Permissions (Custom System)\n";
echo "=" . str_repeat("=", 55) . "\n\n";

// Check if permissions table exists and its structure
try {
    $permissionStructure = DB::select('DESCRIBE permissions');
    echo "✅ Permissions table found:\n";
    foreach ($permissionStructure as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ Cannot access permissions table: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if guard_name column exists
$hasGuardName = false;
foreach ($permissionStructure as $column) {
    if ($column->Field === 'guard_name') {
        $hasGuardName = true;
        break;
    }
}

echo "📋 Permission table structure: " . ($hasGuardName ? "Spatie format" : "Custom format") . "\n\n";

// Define vendor kontainer sewa permissions
$vendorPermissions = [
    [
        'name' => 'vendor-kontainer-sewa-view',
        'description' => 'Melihat daftar vendor kontainer sewa'
    ],
    [
        'name' => 'vendor-kontainer-sewa-create',
        'description' => 'Membuat vendor kontainer sewa baru'
    ],
    [
        'name' => 'vendor-kontainer-sewa-edit',
        'description' => 'Mengedit vendor kontainer sewa'
    ],
    [
        'name' => 'vendor-kontainer-sewa-delete',
        'description' => 'Menghapus vendor kontainer sewa'
    ]
];

echo "🔑 Creating Vendor Kontainer Sewa Permissions...\n";

$createdCount = 0;
$existingCount = 0;

foreach ($vendorPermissions as $permData) {
    // Check if permission already exists
    $existingPermission = DB::table('permissions')
        ->where('name', $permData['name'])
        ->first();

    if ($existingPermission) {
        echo "   ✅ Already exists: {$permData['name']}\n";
        $existingCount++;
    } else {
        // Create permission data array
        $permissionData = [
            'name' => $permData['name'],
            'description' => $permData['description'],
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Add guard_name if the column exists (Spatie format)
        if ($hasGuardName) {
            $permissionData['guard_name'] = 'web';
        }

        try {
            DB::table('permissions')->insert($permissionData);
            echo "   ➕ Created: {$permData['name']}\n";
            $createdCount++;
        } catch (Exception $e) {
            echo "   ❌ Failed to create {$permData['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n📊 Summary:\n";
echo "   Created: {$createdCount} permissions\n";
echo "   Already existed: {$existingCount} permissions\n";
echo "   Total: " . ($createdCount + $existingCount) . " permissions\n\n";

// Find admin user for assignment
echo "👤 Looking for admin user...\n";
$adminUser = DB::table('users')->where('username', 'admin')->first();

if (!$adminUser) {
    // Try to find first user
    $adminUser = DB::table('users')->first();
    if ($adminUser) {
        echo "⚠ Admin user not found, using first user: {$adminUser->username} (ID: {$adminUser->id})\n";
    } else {
        echo "❌ No users found in database!\n";
        exit(1);
    }
} else {
    echo "✅ Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";
}

// Check if user_permissions pivot table exists
$userPermissionsTable = null;
$tables = DB::select('SHOW TABLES');
$tableNames = array_map(function($table) {
    return array_values((array)$table)[0];
}, $tables);

// Common pivot table names
$possibleTables = [
    'user_permissions',
    'permission_user',
    'model_has_permissions',
    'user_permission'
];

foreach ($possibleTables as $tableName) {
    if (in_array($tableName, $tableNames)) {
        $userPermissionsTable = $tableName;
        break;
    }
}

if (!$userPermissionsTable) {
    echo "❌ Could not find user permissions pivot table!\n";
    echo "Available tables: " . implode(', ', $tableNames) . "\n";
    echo "Please assign permissions manually via the admin interface.\n\n";
} else {
    echo "✅ Found user permissions table: {$userPermissionsTable}\n\n";

    // Get permission IDs
    $permissionIds = DB::table('permissions')
        ->whereIn('name', array_column($vendorPermissions, 'name'))
        ->pluck('id', 'name')
        ->toArray();

    echo "🎯 Assigning permissions to admin user...\n";

    $assignedCount = 0;
    $alreadyAssignedCount = 0;

    foreach ($permissionIds as $permName => $permId) {
        // Check table structure to determine insert format
        $pivotData = [];

        if ($userPermissionsTable === 'model_has_permissions') {
            // Spatie format
            $existingAssignment = DB::table($userPermissionsTable)
                ->where('permission_id', $permId)
                ->where('model_type', 'App\\Models\\User')
                ->where('model_id', $adminUser->id)
                ->exists();

            if (!$existingAssignment) {
                $pivotData = [
                    'permission_id' => $permId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $adminUser->id
                ];
            }
        } else {
            // Custom format - check what columns exist
            $pivotStructure = DB::select("DESCRIBE {$userPermissionsTable}");
            $pivotColumns = array_column($pivotStructure, 'Field');

            if (in_array('user_id', $pivotColumns) && in_array('permission_id', $pivotColumns)) {
                $existingAssignment = DB::table($userPermissionsTable)
                    ->where('user_id', $adminUser->id)
                    ->where('permission_id', $permId)
                    ->exists();

                if (!$existingAssignment) {
                    $pivotData = [
                        'user_id' => $adminUser->id,
                        'permission_id' => $permId
                    ];
                }
            }
        }

        if (empty($pivotData)) {
            echo "   ✅ Already assigned: {$permName}\n";
            $alreadyAssignedCount++;
        } else {
            try {
                DB::table($userPermissionsTable)->insert($pivotData);
                echo "   ➕ Assigned: {$permName}\n";
                $assignedCount++;
            } catch (Exception $e) {
                echo "   ❌ Failed to assign {$permName}: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n📊 Assignment Summary:\n";
    echo "   Assigned: {$assignedCount} permissions\n";
    echo "   Already assigned: {$alreadyAssignedCount} permissions\n";
}

echo "\n🔍 Verification:\n";
$allPermissions = DB::table('permissions')
    ->whereIn('name', array_column($vendorPermissions, 'name'))
    ->get();

foreach ($allPermissions as $perm) {
    echo "   ✅ {$perm->name} (ID: {$perm->id})\n";
}

echo "\n✨ Setup Complete!\n";
echo "🌐 You can now access: /vendor-kontainer-sewa\n";
echo "🔧 Permissions available in admin user interface for assignment\n\n";

if ($createdCount > 0 || $assignedCount > 0) {
    echo "🎉 SUCCESS: Vendor Kontainer Sewa permissions setup completed!\n";
} else {
    echo "ℹ️  All permissions already existed and were assigned.\n";
}

echo "=" . str_repeat("=", 55) . "\n";
